<?php
class Employee {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT e.*, v.ten_vai_tro 
                                   FROM employee e 
                                   LEFT JOIN vai_tro v ON e.RoleID = v.id 
                                   WHERE e.isActivate = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getRoles() {
        $stmt = $this->pdo->query("SELECT * FROM vai_tro WHERE trangthai = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE EmployeeID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($data) {
        $stmt = $this->pdo->prepare("INSERT INTO employee (Username, Fullname, PhoneNumber, Email, Address, PasswordHash, RoleID, isActivate) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([
            $data['Username'], 
            $data['Fullname'], 
            $data['PhoneNumber'], 
            $data['Email'], 
            $data['Address'], 
            password_hash($data['Password'], PASSWORD_DEFAULT), 
            $data['RoleID'], 
            $data['isActivate']
        ]);
        if (!$success) {
            throw new Exception("Không thể thêm nhân viên mới");
        }
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE employee 
                                     SET Fullname = ?, PhoneNumber = ?, Email = ?, Address = ?, RoleID = ?, isActivate = ? 
                                     WHERE EmployeeID = ?");
        if (!isset($data['Fullname'], $data['PhoneNumber'], $data['Email'], $data['Address'], $data['RoleID'], $data['isActivate'])) {
            throw new Exception("Invalid data provided for updating employee ID: $id");
        }

        $success = $stmt->execute([
            $data['Fullname'], 
            $data['PhoneNumber'], 
            $data['Email'], 
            $data['Address'], 
            $data['RoleID'], 
            $data['isActivate'], 
            $id
        ]);

        if (!$success) {
            throw new Exception("Không thể cập nhật nhân viên ID: $id");
        }
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("UPDATE employee SET isActivate = 0 WHERE EmployeeID = ?");
        $stmt->execute([$id]);
    }
}