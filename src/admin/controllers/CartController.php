<?php
require_once 'models/Cart.php';
require_once 'db_connection.php';

$cartModel = new Cart($pdo);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'view':
            $userId = $_GET['userId'];
            $cartItems = $cartModel->getByUserId($userId);
            include 'views/cart/view.php';
            break;
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = [
                    'UserID' => $_POST['UserID'],
                    'ProductID' => $_POST['ProductID'],
                    'Quantity' => $_POST['Quantity']
                ];
                $cartModel->add($data);
                header('Location: index.php?page=cart&action=view&userId=' . $data['UserID']);
                exit;
            }
            break;
        case 'remove':
            $userId = $_GET['userId'];
            $productId = $_GET['productId'];
            $cartModel->remove($userId, $productId);
            header('Location: index.php?page=cart&action=view&userId=' . $userId);
            exit;
    }
}