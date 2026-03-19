<?php
    class Supplier {
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        // Lấy tất cả nhà cung cấp
        public function getAll() {
            $query = "SELECT * FROM supplier ORDER BY SupplierID DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Lấy thông tin nhà cung cấp theo ID
        public function getById($supplierId) {
            $query = "SELECT * FROM supplier WHERE SupplierID = :supplierId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':supplierId', $supplierId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Thêm nhà cung cấp mới
        public function add($supplierName) {
            $query = "INSERT INTO supplier (SupplierName) VALUES (:supplierName)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':supplierName', $supplierName, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId();
        }

        // Cập nhật nhà cung cấp
        public function update($supplierId, $supplierName) {
            $query = "UPDATE supplier SET SupplierName = :supplierName WHERE SupplierID = :supplierId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':supplierName', $supplierName, PDO::PARAM_STR);
            $stmt->bindParam(':supplierId', $supplierId, PDO::PARAM_INT);
            return $stmt->execute();
        }

        // Xóa nhà cung cấp
        public function delete($supplierId) {
            $this->db->beginTransaction();
            try {
                // Xóa sản phẩm của nhà cung cấp trước
                $queryProduct = "DELETE FROM supplierproduct WHERE SupplierID = :supplierId";
                $stmtProduct = $this->db->prepare($queryProduct);
                $stmtProduct->bindParam(':supplierId', $supplierId, PDO::PARAM_INT);
                $stmtProduct->execute();

                // Xóa nhà cung cấp
                $query = "DELETE FROM supplier WHERE SupplierID = :supplierId";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':supplierId', $supplierId, PDO::PARAM_INT);
                $stmt->execute();

                $this->db->commit();
                return true;
            } catch (Exception $e) {
                $this->db->rollBack();
                return false;
            }
        }

        // Lấy danh sách sản phẩm của nhà cung cấp
        public function getProductsBySupplierId($supplierId) {
            $query = "SELECT sp.*, p.ProductName 
                    FROM supplierproduct sp 
                    LEFT JOIN product p ON sp.ProductID = p.ProductID 
                    WHERE sp.SupplierID = :supplierId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':supplierId', $supplierId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Thêm sản phẩm cho nhà cung cấp
        public function addProductToSupplier($supplierId, $productId, $importPrice) {
            $query = "INSERT INTO supplierproduct (SupplierID, ProductID, ImportPrice) 
                    VALUES (:supplierId, :productId, :importPrice)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':supplierId', $supplierId, PDO::PARAM_INT);
            $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':importPrice', $importPrice, PDO::PARAM_STR);
            return $stmt->execute();
        }
    }