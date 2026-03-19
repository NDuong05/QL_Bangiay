<?php
require_once 'models/Account.php';
require_once 'db_connection.php';

$accountModel = new Account($pdo);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'list':
            $accounts = $accountModel->getAll();
            include 'views/accounts/list.php';
            break;
        case 'detail':
            $id = $_GET['id'];
            $account = $accountModel->getById($id);
            include 'views/accounts/detail.php';
            break;
        case 'update':
            $id = $_POST['id'];
            $data = [
                'username' => $_POST['username'],
                'full_name' => $_POST['full_name'],
                'phone' => $_POST['phone'],
                'email' => $_POST['email'],
                'address' => $_POST['address'],
                'status' => $_POST['status']
            ];
            $accountModel->update($id, $data);
            header('Location: admin.php?page=accounts&action=list');
            exit;
    }
}