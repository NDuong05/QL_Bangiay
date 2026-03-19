<?php
require_once 'models/User.php';
require_once 'models/Permission.php';
$permissionModel = new Permission($pdo);
$userRoleId = $_SESSION['user']['RoleID'] ?? null;
$permissions = $permissionModel->getPermissionsByRole($userRoleId);
$userModel = new User($pdo);
$page = $_GET['page'] ?? '';
$action = $_GET['action'] ?? 'list';
$hasUserViewPermission = in_array('user_view', $permissions);
$hasUserAddPermission = in_array('user_add', $permissions);
$hasUserEditPermission = in_array('user_edit', $permissions);
$hasUserDeletePermission = in_array('user_delete', $permissions);
switch ($action) {
    case 'add':
        if(!$hasUserAddPermission) {
            // echo "Bạn không có quyền thêm người dùng.";
            header('Location: admin.php?page=users&action=list');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'Username' => $_POST['Username'],
                'Fullname' => $_POST['Fullname'],
                'PhoneNumber' => $_POST['PhoneNumber'],
                'Email' => $_POST['Email'],
                'Address' => $_POST['Address'],
                'PasswordHash' => password_hash($_POST['Password'], PASSWORD_DEFAULT),
                'isActivate' => $_POST['isActivate']
            ];
            $userModel->add($data);
            header('Location: admin.php?page=users&action=list');
            exit;
        } else {
            include 'views/users/add.php';
        }
        break;

    case 'edit':
        if(!$hasUserEditPermission) {
            // echo "Bạn không có quyền sửa người dùng.";
            header('Location: admin.php?page=users&action=list');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'UserID' => $_POST['UserID'],
                'Fullname' => $_POST['Fullname'],
                'PhoneNumber' => $_POST['PhoneNumber'],
                'Email' => $_POST['Email'],
                'Address' => $_POST['Address'],
                'isActivate' => $_POST['isActivate']
            ];
            $userModel->update($data);
            header('Location: admin.php?page=users&action=list');
            exit;
        } else {
            $user = $userModel->getById($_GET['id']);
            include 'views/users/edit.php';
        }
        break;

    case 'list':
        $users = $userModel->getAll();
        include 'views/users/list.php';
        break;

    case 'delete':
        if(!$hasUserDeletePermission) {
            // echo "Bạn không có quyền xóa người dùng.";
            header('Location: admin.php?page=users&action=list');
            exit;
        }
        $userModel->delete($_GET['id']);
        header('Location: admin.php?page=users&action=list');
        exit;
        break;
    default:
        $hasUserViewPermission = in_array('user_view', $permissions);
        $hasUserAddPermission = in_array('user_add', $permissions);
        $hasUserEditPermission = in_array('user_edit', $permissions);
        $hasUserDeletePermission = in_array('user_delete', $permissions);
        $users = $userModel->getAll();
        include 'views/users/list.php';
        break;
}