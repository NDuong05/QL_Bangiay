<?php
require_once 'db_connection.php'; 

class User {
    private $pdo;

    public function __construct() {
        global $pdo;  
        $this->pdo = $pdo;
    }
    public function add($data) {
        $stmt = $this->pdo->prepare("INSERT INTO user (Username, Fullname, PhoneNumber, Email, Address, PasswordHash, isActivate) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['Username'], 
            $data['Fullname'], 
            $data['PhoneNumber'], 
            $data['Email'], 
            $data['Address'], 
            $data['PasswordHash'], 
            $data['isActivate']
        ]);
    }

    public function update($data) {
        $stmt = $this->pdo->prepare("UPDATE user SET Fullname = ?, PhoneNumber = ?, Email = ?, Address = ?, isActivate = ? 
                                     WHERE UserID = ?");
        $stmt->execute([
            $data['Fullname'], 
            $data['PhoneNumber'], 
            $data['Email'], 
            $data['Address'], 
            $data['isActivate'], 
            $data['UserID']
        ]);
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("UPDATE user SET isActivate = 0 WHERE UserID = ?");
        $stmt->execute([$id]);
    }
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE UserID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM user");
        return $stmt->fetchAll();
    }

    public function getTopCustomersByPurchase($startDate, $endDate, $limit = 5) {
        $query = "
            SELECT 
                u.UserID, 
                u.Username, 
                u.Fullname, 
                u.Email, 
                u.PhoneNumber,
                SUM(od.Subtotal) as total_purchase
            FROM 
                `user` u
            JOIN 
                `orders` o ON u.UserID = o.UserID
            JOIN 
                `orderdetail` od ON o.OrderID = od.OrderID
            WHERE 
                o.OrderDate BETWEEN :start_date AND :end_date
                AND o.Status = 'Completed'
            GROUP BY 
                u.UserID
            ORDER BY 
                total_purchase ASC
            LIMIT :limit
        ";
    
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':start_date', $startDate . ' 00:00:00', PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $endDate . ' 23:59:59', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Sử dụng bindValue thay vì bindParam
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}