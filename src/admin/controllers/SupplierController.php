<?php
require_once 'models/Supplier.php';
require_once 'db_connection.php';
require_once 'models/Permission.php';
$permissionModel = new Permission($pdo);
$userRoleId = $_SESSION['user']['RoleID'] ?? null;
$permissions = $permissionModel->getPermissionsByRole($userRoleId);
$supplierModel = new Supplier($pdo);
$hasSupplierViewPermission = in_array('supplier_view', $permissions);
$hasSupplierAddPermission = in_array('supplier_add', $permissions);
$hasSupplierEditPermission = in_array('supplier_edit', $permissions);
$hasSupplierDeletePermission = in_array('supplier_delete', $permissions);
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'list':
            $suppliers = $supplierModel->getAll();
            include 'views/suppliers/list.php';
            break;
        case 'detail':
            if(!$hasSupplierViewPermission) {
                header('Location: admin.php?page=suppliers&action=list');
                exit;
            }
            $id = $_GET['id'];
            $supplier = $supplierModel->getById($id);
            include 'views/suppliers/detail.php';
            break;
        case 'add':
            if(!$hasSupplierAddPermission) {
                header('Location: admin.php?page=suppliers&action=list');
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'];
                $supplierModel->add($name);
                header('Location: admin.php?page=suppliers&action=list');
                exit;
            } else {
                include 'views/suppliers/add.php';
            }
            break;
        case 'update':
            if(!$hasSupplierEditPermission) {
                header('Location: admin.php?page=suppliers&action=list');
                exit;
            }
            $id = $_GET['id'] ?? $_POST['id'];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'];
                $supplierModel->update($id, $name);
                header('Location: admin.php?page=suppliers&action=list');
                exit;
            } else {
                $supplier = $supplierModel->getById($id);
                include 'views/suppliers/edit.php';
            }
            break;
        case 'delete':
            if(!$hasSupplierDeletePermission) {
                header('Location: admin.php?page=suppliers&action=list');
                exit;
            }
            $id = $_GET['id'];
            $supplierModel->delete($id);
            header('Location: admin.php?page=suppliers&action=list');
            exit;
    }
}