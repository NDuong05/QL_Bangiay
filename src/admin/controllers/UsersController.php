<?php
require_once 'models/User.php';
require_once 'models/Permission.php';
require_once 'models/Province.php';
require_once 'models/District.php';
require_once 'models/Ward.php';

// Khởi tạo các Model
$permissionModel = new Permission($pdo);
$provinceModel = new Province($pdo);
$userModel = new User($pdo);
$districtModel = new District($pdo);
$wardModel = new Ward($pdo);

$userRoleId = $_SESSION['user']['RoleID'] ?? null;
$permissions = $permissionModel->getPermissionsByRole($userRoleId);

$page = $_GET['page'] ?? '';
$action = $_GET['action'] ?? 'list';

$hasUserViewPermission = in_array('user_view', $permissions);
$hasUserAddPermission = in_array('user_add', $permissions);
$hasUserEditPermission = in_array('user_edit', $permissions);
$hasUserDeletePermission = in_array('user_delete', $permissions);

switch ($action) {
    case 'add':
        if(!$hasUserAddPermission) {
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
                'ProvinceID' => $_POST['ProvinceID'],
                'DistrictID' => $_POST['DistrictID'],
                'WardID' => $_POST['WardID'],
                'PasswordHash' => password_hash($_POST['Password'], PASSWORD_DEFAULT),
                'isActivate' => $_POST['isActivate']
            ];
            $userModel->add($data);
            header('Location: admin.php?page=users&action=list');
            exit;
        } else {
            // Bây giờ các biến này mới có dữ liệu để đổ vào View
            $provinces = $provinceModel->getAll(); 
            $districts = $districtModel->getAll();
            $wards = $wardModel->getAll();
            include 'views/users/add.php';
        }
        break;

    case 'edit':
        if(!$hasUserEditPermission) {
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
                'ProvinceID' => $_POST['ProvinceID'],
                'DistrictID' => $_POST['DistrictID'], // BỔ SUNG: Phải có DistrictID khi sửa
                'WardID' => $_POST['WardID'],         // BỔ SUNG: Phải có WardID khi sửa
                'isActivate' => $_POST['isActivate']
            ];
            $userModel->update($data);
            header('Location: admin.php?page=users&action=list');
            exit;
        } else {
            $user = $userModel->getById($_GET['id']);
            $provinces = $provinceModel->getAll(); 
            $districts = $districtModel->getAll(); // BỔ SUNG để hiện lại vùng miền cũ
            $wards = $wardModel->getAll();         // BỔ SUNG
            include 'views/users/edit.php';
        }
        break;

    case 'list':
        $users = $userModel->getAll();
        include 'views/users/list.php';
        break;

    case 'delete':
        if(!$hasUserDeletePermission) {
            header('Location: admin.php?page=users&action=list');
            exit;
        }
        $userModel->delete($_GET['id']);
        header('Location: admin.php?page=users&action=list');
        exit;
        break;
    default:
        $users = $userModel->getAll();
        include 'views/users/list.php';
        break;
}