<?php
class Import
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT i.*, e.Fullname AS EmployeeFullname 
                                   FROM `import` i 
                                   LEFT JOIN `employee` e ON i.EmployeeID = e.EmployeeID");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT i.*, e.Fullname AS EmployeeFullname, e.Email AS EmployeeEmail 
                                    FROM `import` i 
                                    LEFT JOIN `employee` e ON i.EmployeeID = e.EmployeeID
                                    WHERE i.ImportID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($data)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO `import` (EmployeeID, Total) VALUES (?, ?)");
            $stmt->execute([$data['EmployeeID'], $data['Total']]);
            $importId = $this->pdo->lastInsertId();

            // Thêm chi tiết nhập hàng
            if (isset($data['details']) && is_array($data['details'])) {
                $detailStmt = $this->pdo->prepare("INSERT INTO `importdetail` (ImportID, ProductSizeID, Quantity, UnitPrice, Subtotal) 
                                                VALUES (?, ?, ?, ?, ?)");
                foreach ($data['details'] as $detail) {
                    $subtotal = $detail['Quantity'] * $detail['UnitPrice'];
                    $detailStmt->execute([
                        $importId,
                        $detail['ProductSizeID'],
                        $detail['Quantity'],
                        $detail['UnitPrice'],
                        $subtotal
                    ]);
                }
            }

            $this->pdo->commit();
            return $importId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getImportDetails($importId)
    {
        $stmt = $this->pdo->prepare("SELECT id.*, ps.size, p.ProductName, p.ImageURL 
                                     FROM `importdetail` id
                                     JOIN `productsize` ps ON id.ProductSizeID = ps.ProductSizeID
                                     JOIN `product` p ON ps.ProductID = p.ProductID
                                     WHERE id.ImportID = ?");
        $stmt->execute([$importId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductSizes()
    {
        $stmt = $this->pdo->query("SELECT ps.*, p.ProductName, p.ImageURL, p.ProductID
                                  FROM `productsize` ps 
                                  JOIN `product` p ON ps.ProductID = p.ProductID");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStock($details)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE productsize SET StockQuantity = StockQuantity + ? WHERE ProductSizeID = ?");
            foreach ($details as $detail) {
                $stmt->execute([$detail['Quantity'], $detail['ProductSizeID']]);
                if ($stmt->rowCount() == 0) {
                    error_log("Failed to update stock for ProductSizeID: {$detail['ProductSizeID']}");
                }
            }
        } catch (Exception $e) {
            error_log("Error updating stock: " . $e->getMessage());
            throw $e;
        }
    }
}
