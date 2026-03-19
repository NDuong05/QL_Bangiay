<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_products':
        echo json_encode(getProducts($pdo));
        break;
    case 'get_accounts':
        echo json_encode(getAccounts($pdo));
        break;
    case 'get_orders':
        echo json_encode(getOrders($pdo));
        break;
    case 'add_product':
        addProduct($pdo);
        break;
    case 'update_product':
        updateProduct($pdo);
        break;
    case 'delete_product':
        deleteProduct($pdo);
        break;
    default:
        echo json_encode(["error" => "Invalid action"]);
        break;
}

function getProducts($pdo) {
    $stmt = $pdo->prepare("SELECT p.ProductID AS id, p.ProductName AS name, p.Price AS price, p.ImageURL AS image, 
               c.CategoryName AS category, b.BrandName AS brand, p.Gender AS sex,
               GROUP_CONCAT(ps.Size) AS size
        FROM Products p
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN Brands b ON p.BrandID = b.BrandID
        LEFT JOIN ProductSizes ps ON p.ProductID = ps.ProductID
        GROUP BY p.ProductID");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as &$product) {
        $product['size'] = explode(',', $product['size']);
    }
    return $products;
}

function getAccounts($pdo) {
    $stmt = $pdo->prepare("SELECT UserID AS id, Username AS username, FullName AS fullname, PhoneNumber AS phone,
               Email AS email, Address AS address, CreatedAt AS join, IsAdmin AS isAdmin FROM Users");
    $stmt->execute();
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($accounts as &$account) {
        $account['join'] = date('Y/m/d', strtotime($account['join']));
    }
    return $accounts;
}

function getOrders($pdo) {
    $stmt = $pdo->prepare("SELECT o.OrderID AS id, o.UserID AS customerId, o.OrderDate AS orderDate, 
               o.TotalAmount AS total, o.ShippingAddress AS fullAddress, o.PaymentMethod AS method, o.OrderStatus AS status,
               od.ProductID AS product_id, od.Size, od.Quantity AS quantity, od.UnitPrice AS originalPrice
        FROM Orders o
        LEFT JOIN OrderDetails od ON o.OrderID = od.OrderID");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addProduct($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['name'], $data['price'], $data['image'], $data['category'], $data['brand'], $data['sex'], $data['size'])) {
        echo json_encode(["error" => "Missing fields"]);
        return;
    }
    try {
        $stmt = $pdo->prepare("INSERT INTO Products (ProductName, Price, ImageURL, CategoryID, BrandID, Gender) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['price'], $data['image'], $data['category'], $data['brand'], $data['sex']]);
        echo json_encode(["message" => "Product added successfully"]);
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
}

function updateProduct($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['id'], $data['name'], $data['price'], $data['image'], $data['category'], $data['brand'], $data['sex'], $data['size'])) {
        echo json_encode(["error" => "Missing fields"]);
        return;
    }
    try {
        $stmt = $pdo->prepare("UPDATE Products SET ProductName = ?, Price = ?, ImageURL = ?, CategoryID = ?, BrandID = ?, Gender = ? WHERE ProductID = ?");
        $stmt->execute([$data['name'], $data['price'], $data['image'], $data['category'], $data['brand'], $data['sex'], $data['id']]);
        echo json_encode(["message" => "Product updated successfully"]);
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
}

function deleteProduct($pdo) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['id'])) {
        echo json_encode(["error" => "Missing product ID"]);
        return;
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM Products WHERE ProductID = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(["message" => "Product deleted successfully"]);
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
}

?>