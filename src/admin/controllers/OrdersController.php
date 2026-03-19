<?php
require_once 'models/Order.php';
require_once 'db_connection.php';
require_once 'models/Permission.php';
$orderModel = new Order($pdo);
$permissionModel = new Permission($pdo);
$userRoleId = $_SESSION['user']['RoleID'] ?? null;
$permissions = $permissionModel->getPermissionsByRole($userRoleId);
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'list':      
            // Kiểm tra có quyền xem đơn hàng hay không
            $hasOrderViewPermission = in_array('order_view', $permissions);
            $orders = $orderModel->getAll();
            include 'views/orders/list.php';
            break;
        case 'detail':
            $hasOrderViewPermission = in_array('order_view', $permissions);
            if (!$hasOrderViewPermission) {
                header('Location: admin.php?page=orders&action=list');
                exit;
            }
            $hasOrderEditPermission = in_array('order_edit', $permissions);
            $id = $_GET['id'];
            $order = $orderModel->getById($id);
            $orderDetails = $orderModel->getOrderDetails($id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
                $id = $_POST['id'];
                $newStatus = $_POST['status'];
                $oldStatus = $order['Status']; // Lấy trạng thái cũ từ đơn hàng

                // Cập nhật trạng thái
                $orderModel->updateStatus($id, $newStatus);

                // Giảm stock nếu trạng thái cũ là "Pending" và trạng thái mới là "Processing" hoặc "Completed"
                if ($oldStatus === 'Pending' && ($newStatus === 'Processing' || $newStatus === 'Completed')) {
                    $orderDetails = $orderModel->getOrderDetails($id);
                    foreach ($orderDetails as $detail) {
                        $productSizeID = $detail['ProductSizeID'];
                        $quantity = $detail['Quantity'];
                        $orderModel->decreaseStock($productSizeID, $quantity);
                    }
                }

                header("Location: admin.php?page=orders&action=detail&id=$id&success=Cập nhật trạng thái thành công");
                exit;
            }

            include 'views/orders/detail.php';
            break;
        case 'update':
            // Kiểm tra có quyền cập nhật đơn hàng hay không
            $hasOrderEditPermission = in_array('order_edit', $permissions);
            if(!$hasOrderEditPermission) {
                header('Location: admin.php?page=orders&action=list');
                exit;
            }
            // Xử lý từ form cập nhật nhanh trong detail.php
            if (isset($_POST['id']) && isset($_POST['status'])) {
                $id = $_POST['id'];
                $status = $_POST['status'];
                $orderModel->updateStatus($id, $status);
                header("Location: admin.php?page=orders&action=detail&id=$id&success=Cập nhật trạng thái thành công");
                exit;
            }
            // Xử lý từ form chính trong update.php
            else if (isset($_GET['id']) && isset($_POST['Status'])) {
                $id = $_GET['id'];
                $status = $_POST['Status'];
                $total = isset($_POST['Total']) ? $_POST['Total'] : null;

                $order = $orderModel->getById($id);
                $oldStatus = $order['Status'];

                // Cập nhật thông tin đơn hàng
                $success = $orderModel->updateOrderInfo($id, $status, $total);

                // Giảm stock nếu trạng thái cũ là "Pending" và trạng thái mới là "Processing" hoặc "Completed"
                if ($success && $oldStatus === 'Pending' && ($status === 'Processing' || $status === 'Completed')) {
                    $orderDetails = $orderModel->getOrderDetails($id);
                    foreach ($orderDetails as $detail) {
                        $productSizeID = $detail['ProductSizeID'];
                        $quantity = $detail['Quantity'];
                        $orderModel->decreaseStock($productSizeID, $quantity);
                    }
                }

                if ($success) {
                    header("Location: admin.php?page=orders&action=detail&id=$id&success=Cập nhật đơn hàng thành công");
                } else {
                    header("Location: admin.php?page=orders&action=update&id=$id&error=Có lỗi xảy ra khi cập nhật đơn hàng");
                }
                exit;
            }
            break;
    }
}
