<?php
class Cart {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getByUserId($userId) {
        $stmt = $this->pdo->prepare("SELECT c.*, p.ProductName, p.Price, p.ImageURL 
                                     FROM cart c 
                                     JOIN product p ON c.ProductID = p.ProductID 
                                     WHERE c.UserID = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($data) {
        $stmt = $this->pdo->prepare("INSERT INTO cart (UserID, ProductID, Quantity) 
                                     VALUES (?, ?, ?)
                                     ON DUPLICATE KEY UPDATE Quantity = Quantity + ?");
        $stmt->execute([
            $data['UserID'], 
            $data['ProductID'], 
            $data['Quantity'], 
            $data['Quantity']
        ]);
    }

    public function remove($userId, $productId) {
        $stmt = $this->pdo->prepare("DELETE FROM cart WHERE UserID = ? AND ProductID = ?");
        $stmt->execute([$userId, $productId]);
    }
}