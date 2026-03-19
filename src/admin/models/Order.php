<?php
class Order
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT o.*, u.Fullname AS UserFullname, u.Email AS UserEmail, u.PhoneNumber AS UserPhoneNumber,
                                  e.Fullname AS EmployeeFullname
                                  FROM `orders` o 
                                  LEFT JOIN `user` u ON o.UserID = u.UserID 
                                  LEFT JOIN `employee` e ON o.SaleID = e.EmployeeID");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // public function getTotalOrders($orderId)
    // {
    //     $total = 0;
    //     $stmt = $this->pdo->prepare("SELECT SUM(Subtotal) FROM `orderdetail` WHERE OrderID = ?");
    //     $stmt->execute([$orderId]);
    //     $total = $stmt->fetchColumn();
    //     return $total;
    // }
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT o.*, u.Fullname AS UserFullname, u.Email AS UserEmail, u.PhoneNumber AS UserPhoneNumber,
                                   e.Fullname AS EmployeeFullname,
                                   (SELECT SUM(od.Subtotal) FROM `orderdetail` od WHERE od.OrderID = o.OrderID) AS Total
                                   FROM `orders` o 
                                   LEFT JOIN `user` u ON o.UserID = u.UserID 
                                   LEFT JOIN `employee` e ON o.SaleID = e.EmployeeID
                                   WHERE o.OrderID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO `order` (UserID, ShippingAddress, ProvinceID, DistrictID, WardID, Status, SaleID, WarehouseID) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['UserID'],
            $data['ShippingAddress'],
            $data['ProvinceID'],
            $data['DistrictID'],
            $data['WardID'],
            $data['Status'] ?? 'Pending',
            $data['SaleID'] ?? null,
            $data['WarehouseID'] ?? null,
            $data['ExportDate'] ?? null

        ]);
        return $this->pdo->lastInsertId();
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE `orders` SET Status = ? WHERE OrderID = ?");
        $stmt->execute([$status, $id]);
        return true;
    }

    public function updateOrderInfo($id, $status, $total = null)
    {
        try {
            // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
            $this->pdo->beginTransaction();

            // Nếu chỉ cập nhật trạng thái
            if ($total === null) {
                $stmt = $this->pdo->prepare("UPDATE `orders` SET Status = ? WHERE OrderID = ?");
                $stmt->execute([$status, $id]);
            }
            // Nếu cập nhật cả trạng thái và tổng tiền
            else {
                $stmt = $this->pdo->prepare("UPDATE `orders` SET Status = ? WHERE OrderID = ?");
                $stmt->execute([$status, $id]);

                // Cập nhật tổng tiền bằng cách điều chỉnh subtotal của mỗi chi tiết đơn hàng
                $details = $this->getOrderDetails($id);
                if (!empty($details)) {
                    // Tính tổng hiện tại
                    $currentTotal = 0;
                    foreach ($details as $detail) {
                        $currentTotal += $detail['Subtotal'];
                    }

                    if ($currentTotal > 0) {
                        // Tỷ lệ điều chỉnh
                        $ratio = $total / $currentTotal;

                        // Cập nhật mỗi chi tiết đơn hàng
                        $updateDetailStmt = $this->pdo->prepare("UPDATE `orderdetail` SET Subtotal = ? WHERE OrderID = ? AND ProductSizeID = ?");
                        foreach ($details as $detail) {
                            $newSubtotal = $detail['Subtotal'] * $ratio;
                            $updateDetailStmt->execute([$newSubtotal, $id, $detail['ProductSizeID']]);
                        }
                    }
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function decreaseStock($productSizeID, $quantity)
    {
        $stmt = $this->pdo->prepare("
        UPDATE productsize
        SET StockQuantity = StockQuantity - ?
        WHERE ProductSizeID = ? AND StockQuantity >= ?
    ");
        $stmt->execute([$quantity, $productSizeID, $quantity]);
        if ($stmt->rowCount() == 0) {
            error_log("Insufficient stock for ProductSizeID: $productSizeID");
        }
    }

    public function getOrderDetails($orderId)
    {
        // Kiểm tra và debug dữ liệu
        $sql = "SELECT od.*, ps.size, p.ProductName, p.ImageURL 
                FROM `orderdetail` od
                JOIN `productsize` ps ON od.ProductSizeID = ps.ProductSizeID
                JOIN `product` p ON ps.ProductID = p.ProductID
                WHERE od.OrderID = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Làm rõ giá trị Subtotal
        foreach ($result as &$item) {
            // Đảm bảo Subtotal là số thực
            if (isset($item['Subtotal'])) {
                $item['Subtotal'] = floatval($item['Subtotal']);
                // Debug
                // error_log("Subtotal for product " . $item['ProductName'] . ": " . $item['Subtotal']);
            }
        }

        return $result;
    }
    public function getOrdersByUserAndDateRange($userID, $startDate, $endDate) {
        $query = "
            SELECT 
                o.OrderID,
                o.OrderDate,
                o.Status,
                (SELECT SUM(Subtotal) FROM orderdetail WHERE OrderID = o.OrderID) as total
            FROM 
                orders o
            WHERE 
                o.UserID = :user_id
                AND o.OrderDate BETWEEN :start_date AND :end_date
            ORDER BY 
                o.OrderDate ASC
        ";
        
        $stmt = $this->pdo->prepare($query);
        
        // Sử dụng bindValue thay vì bindParam
        $stmt->bindValue(':user_id', $userID, PDO::PARAM_INT); // bindValue thay vì bindParam
        $stmt->bindValue(':start_date', $startDate . ' 00:00:00', PDO::PARAM_STR); // bindValue thay vì bindParam
        $stmt->bindValue(':end_date', $endDate . ' 23:59:59', PDO::PARAM_STR); // bindValue thay vì bindParam
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalIncome() {
        $query = "
            SELECT SUM(od.Subtotal) as total
            FROM orders o
            JOIN orderdetail od ON o.OrderID = od.OrderID
            WHERE o.Status = 'Completed'
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }
}
