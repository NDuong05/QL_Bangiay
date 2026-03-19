<?php
class Product
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT p.*, c.CategoryName, b.BrandName 
                                   FROM product p 
                                   LEFT JOIN categories c ON p.CategoryID = c.CategoryID 
                                   LEFT JOIN brand b ON p.BrandID = b.BrandID 
                                   WHERE p.IsDeleted = 0");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM product WHERE ProductID = ? AND IsDeleted = 0");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($data)
    {
        try {
            $this->pdo->beginTransaction();

            // Insert vào bảng product
            $stmt = $this->pdo->prepare("INSERT INTO product (ProductName, CategoryID, BrandID, Gender, Price, ImageURL) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['ProductName'],
                $data['CategoryID'],
                $data['BrandID'],
                $data['Gender'],
                $data['Price'],
                $data['ImageURL']
            ]);

            $productId = $this->pdo->lastInsertId(); // Lấy ID sản phẩm vừa tạo

            // Insert nhiều dòng vào bảng productsize
            $sizes = $data['sizes']; // array
            $stockQuantities = $data['stock_quantities']; // array

            $stmtSize = $this->pdo->prepare("INSERT INTO productsize (ProductID, Size, StockQuantity) VALUES (?, ?, ?)");

            foreach ($sizes as $index => $size) {
                $stockQuantity = $stockQuantities[$index] ?? 0;
                $stmtSize->execute([$productId, $size, $stockQuantity]);
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }



    public function update($id, $data)
    {
        try {

            $this->pdo->beginTransaction();

            // Update bảng product
            $stmt = $this->pdo->prepare("UPDATE product 
                                     SET ProductName = ?, CategoryID = ?, BrandID = ?, Gender = ?, Price = ?, ImageURL = ?
                                     WHERE ProductID = ?");
            $stmt->execute([
                $data['ProductName'],
                $data['CategoryID'],
                $data['BrandID'],
                $data['Gender'],
                $data['Price'],
                $data['ImageURL'],
                $id
            ]);

            // Xử lý size
            $productSizeIds = $data['product_size_ids'];   // mảng id
            $sizes = $data['sizes'];                       // mảng size
            $stockQuantities = $data['stock_quantities'];  // mảng stock

            $filteredSizes = [];
            $filteredQuantities = [];
            $filteredIds = [];

            foreach ($sizes as $index => $size) {
                $stockQuantity = $stockQuantities[$index] ?? null;

                // Nếu cả size và stockQuantity đều không rỗng
                if (trim($size) !== '' && $stockQuantity !== '' && $stockQuantity !== null) {
                    $filteredSizes[] = $size;
                    $filteredQuantities[] = $stockQuantity;
                    $filteredIds[] = $productSizeIds[$index] ?? '';
                }
            }

            // Gán lại
            $sizes = $filteredSizes;
            $stockQuantities = $filteredQuantities;
            $productSizeIds = $filteredIds;


            foreach ($sizes as $index => $size) {
                $stockQuantity = $stockQuantities[$index] ?? 0;
                $productSizeId = $productSizeIds[$index] ?? '';

                if (!empty($productSizeId)) {
                    // Nếu có ID → Update
                    $stmtUpdateSize = $this->pdo->prepare("UPDATE productsize SET Size = ?, StockQuantity = ? WHERE ProductSizeID = ?");
                    $stmtUpdateSize->execute([$size, $stockQuantity, $productSizeId]);
                } else {
                    // Nếu không có ID → Insert mới
                    $stmtInsertSize = $this->pdo->prepare("INSERT INTO productsize (ProductID, Size, StockQuantity) VALUES (?, ?, ?)");
                    $stmtInsertSize->execute([$id, $size, $stockQuantity]);
                }
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }


    public function delete($id)
    {
        $stmt = $this->pdo->prepare("UPDATE product SET IsDeleted = 1 WHERE ProductID = ?");
        $stmt->execute([$id]);
    }

    public function updatePrice($productID, $price)
    {
        $stmt = $this->pdo->prepare("UPDATE product SET Price = ? WHERE ProductID = ?");
        $stmt->execute([$price, $productID]);
    }
}
