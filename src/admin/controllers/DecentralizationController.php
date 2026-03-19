<?php
require_once 'models/Permission.php';
require_once 'db_connection.php';
require_once 'models/Permission.php';
$permissionModel = new Permission($pdo);
$userRoleId = $_SESSION['user']['RoleID'] ?? null;
$permissions = $permissionModel->getPermissionsByRole($userRoleId);
$hasPermissionViewPermission = in_array('permission_view', $permissions);
$hasPermissionAddPermission = in_array('permission_add', $permissions);
$hasPermissionEditPermission = in_array('permission_edit', $permissions);
$hasPermissionDeletePermission = in_array('permission_delete', $permissions);
$permissionModel = new Permission($pdo);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'list':
            $permissions = $permissionModel->getAll();
            include 'views/permission/list.php';
            break;
        case 'detail':
            if(!$hasPermissionViewPermission) {
                // echo "Bạn không có quyền xem nhóm quyền.";
                header('Location: admin.php?page=decentralization&action=list');
                exit;
            }
            $id = $_GET['id'];
            $allFunctions = $permissionModel->getAllFunctions();
            $role = $permissionModel->getById($id);
            include 'views/permission/detail.php';
            break;
        case 'add':
            if(!$hasPermissionAddPermission) {
                // echo "Bạn không có quyền thêm nhóm quyền.";
                header('Location: admin.php?page=decentralization&action=list');
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'];
                // $permissionID = $_POST['permission_id'];
                $permissionID = $permissionModel->add($name);
                $functionIDs = isset($_POST['function_ids']) ? $_POST['function_ids'] : [];
                $functionIDs = array_filter($functionIDs, 'is_numeric');
                $permissionModel->addPermissionDetail($functionIDs,$permissionID);
                header('Location: admin.php?page=decentralization&action=list');
                exit;
            } else {
                $allFunctions = $permissionModel->getAllFunctions();
                include 'views/permission/add.php';
            }
            break;
            case 'update':
                if(!$hasPermissionEditPermission) {
                    // echo "Bạn không có quyền sửa nhóm quyền.";
                    header('Location: admin.php?page=decentralization&action=list');
                    exit;
                }
                // Get ID from appropriate source based on request method
                $id = ($_SERVER['REQUEST_METHOD'] === 'POST') 
                    ? ($_POST['id'] ?? null) 
                    : ($_GET['id'] ?? null);
            
                // Validate ID exists and is numeric
                if (empty($id) || !is_numeric($id)) {
                    http_response_code(400);
                    die('Invalid permission ID');
                }
                $id = (int)$id;
            
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // POST handling - process update
                    try {
                        // Validate inputs
                        // Process function IDs (ensure they're integers)
                        $functionIDs = isset($_POST['function_ids']) ? $_POST['function_ids'] : [];
                        $functionIDs = array_filter($functionIDs, 'is_numeric');
                        // $roleID = intval($_POST['id']);
                        $name = trim($_POST['name'] ?? '');
                        if (empty($name)) {
                            throw new Exception('Permission name cannot be empty');
                        }            
                        // Update operations
                        if (!$permissionModel->update($id, $name)) {
                            throw new Exception('Failed to update permission');
                        }
            
                        if (!$permissionModel->updatePermissionDetail($functionIDs, $id)) {
                            throw new Exception('Failed to update permission details');
                        }
                        // Success - redirect
                        header('Location: admin.php?page=decentralization&action=list');
                        exit;
                    } catch (Exception $e) {
                        // Log error and show message
                        error_log('Permission update error: ' . $e->getMessage());
                        $_SESSION['error_message'] = $e->getMessage();
                        header('Location: admin.php?page=decentralization&action=list');
                        exit;
                    }
                } else {
                    // GET handling - show edit form
                    try {
                        $allFunctions = $permissionModel->getAllFunctions();
                        $role = $permissionModel->getById($id);
                        if (!$role) {
                            throw new Exception('Permission not found');
                        }
                        include 'views/permission/edit.php';
                    } catch (Exception $e) {
                        http_response_code(404);
                        die($e->getMessage());
                    }
                }
                break;
        case 'delete':
            if(!$hasPermissionDeletePermission) {
                // echo "Bạn không có quyền xóa nhóm quyền.";
                header('Location: admin.php?page=decentralization&action=list');
                exit;
            }
            $id = $_GET['id'];
            $permissionModel->delete($id);
            header('Location: admin.php?page=decentralization&action=list');
            exit;
    }
}