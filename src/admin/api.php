<?php
header("Content-Type: application/json");
require_once 'db_connection.php'; // File kết nối cơ sở dữ liệu

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    // Lấy danh sách sản phẩm
    case 'get_products':
        if ($method == 'GET') {
            $stmt = $pdo->query("SELECT p.*, c.CategoryName, b.BrandName 
                                 FROM product p 
                                 LEFT JOIN categories c ON p.CategoryID = c.CategoryID 
                                 LEFT JOIN brand b ON p.BrandID = b.BrandID 
                                 WHERE p.IsDeleted = 0");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($products);
        }
        break;

    // Thêm sản phẩm mới
    case 'add_product':
        if ($method == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $stmt = $pdo->prepare("INSERT INTO product (ProductName, CategoryID, BrandID, Gender, Price, ImageURL) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['ProductName'], 
                $data['CategoryID'], 
                $data['BrandID'], 
                $data['Gender'], 
                $data['Price'], 
                $data['ImageURL']
            ]);
            echo json_encode(['success' => true, 'message' => 'Thêm sản phẩm thành công']);
        }
        break;

    // Cập nhật sản phẩm
    case 'update_product':
        if ($method == 'PUT') {
            $data = json_decode(file_get_contents("php://input"), true);
            $stmt = $pdo->prepare("UPDATE product 
                                   SET ProductName = ?, CategoryID = ?, BrandID = ?, Gender = ?, Price = ?, ImageURL = ? 
                                   WHERE ProductID = ?");
            $stmt->execute([
                $data['ProductName'], 
                $data['CategoryID'], 
                $data['BrandID'], 
                $data['Gender'], 
                $data['Price'], 
                $data['ImageURL'], 
                $data['ProductID']
            ]);
            echo json_encode(['success' => true, 'message' => 'Cập nhật sản phẩm thành công']);
        }
        break;

    // Xóa sản phẩm (chuyển IsDeleted = 1)
    case 'delete_product':
        if ($method == 'DELETE') {
            $data = json_decode(file_get_contents("php://input"), true);
            $stmt = $pdo->prepare("UPDATE product SET IsDeleted = 1 WHERE ProductID = ?");
            $stmt->execute([$data['ProductID']]);
            echo json_encode(['success' => true, 'message' => 'Xóa sản phẩm thành công']);
        }
        break;

    // Lấy danh sách đơn hàng
    case 'get_orders':
        if ($method == 'GET') {
            $stmt = $pdo->query("SELECT o.*, u.Fullname AS UserFullname, e.Fullname AS EmployeeFullname 
                                 FROM orders o 
                                 LEFT JOIN user u ON o.UserID = u.UserID 
                                 LEFT JOIN employee e ON o.EmployeeID = e.EmployeeID");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($orders);
        }
        break;

    // Thêm đơn hàng mới
    case 'add_order':
        if ($method == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $stmt = $pdo->prepare("INSERT INTO orders (UserID, ShippingAddress, Province, Ward, PaymentStatus) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['UserID'], 
                $data['ShippingAddress'], 
                $data['Province'], 
                $data['Ward'], 
                $data['PaymentStatus'] ?? 'Pending'
            ]);
            echo json_encode(['success' => true, 'message' => 'Thêm đơn hàng thành công']);
        }
        break;

    // Lấy danh sách người dùng
    case 'get_users':
        if ($method == 'GET') {
            $stmt = $pdo->query("SELECT * FROM user WHERE isActivate = 1");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
        }
        break;

    default:
        echo json_encode(['error' => 'Hành động không hợp lệ']);
        break;
}