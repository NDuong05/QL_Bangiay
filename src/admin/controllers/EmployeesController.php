<?php
require_once 'models/Employee.php';
require_once 'db_connection.php';
require_once 'models/Permission.php';
$permissionModel = new Permission($pdo);
$userRoleId = $_SESSION['user']['RoleID'] ?? null;
$permissions = $permissionModel->getPermissionsByRole($userRoleId);
$hasStaffViewPermission = in_array('staff_view', $permissions);
$hasStaffAddPermission = in_array('staff_add', $permissions);
$hasStaffEditPermission = in_array('staff_edit', $permissions);
$hasStaffDeletePermission = in_array('staff_delete', $permissions);
$employeeModel = new Employee($pdo);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'list':
            $employees = $employeeModel->getAll();
            include 'views/employees/list.php';
            break;
        case 'add':
            if(!$hasStaffAddPermission) {
                $errorMessage = "Bạn không có quyền thêm nhân viên.";
                header('Location: admin.php?page=employees&action=list');
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                try {
                    $data = [
                        'Username' => $_POST['Username'],
                        'Fullname' => $_POST['Fullname'],
                        'PhoneNumber' => $_POST['PhoneNumber'],
                        'Email' => $_POST['Email'],
                        'Address' => $_POST['Address'],
                        'Password' => $_POST['Password'],
                        'RoleID' => $_POST['RoleID'],
                        'isActivate' => $_POST['isActivate']
                    ];
                    $employeeModel->add($data);
                    
                    header('Location: admin.php?page=employees&action=list');
                    exit;
        
                } catch (Exception $e) {
                    $errorMessage = "Lỗi thêm nhân viên: " . $e->getMessage();
                    $roles = $employeeModel->getRoles();
                    include 'views/employees/add.php';
                }
            } else {
                $roles = $employeeModel->getRoles();
                include 'views/employees/add.php';
            }
            break;
            

        case 'edit':
            if(!$hasStaffEditPermission) {
                $errorMessage = "Bạn không có quyền sửa nhân viên.";
                header('Location: admin.php?page=employees&action=list');
                exit;
            }
            $id = $_GET['id'];
        
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = [
                    'Fullname' => $_POST['Fullname'],
                    'PhoneNumber' => $_POST['PhoneNumber'],
                    'Email' => $_POST['Email'],
                    'Address' => $_POST['Address'],
                    'RoleID' => $_POST['RoleID'],
                    'isActivate' => $_POST['isActivate']
                ];
        
                try {
                    $employeeModel->update($id, $data);
                    header('Location: admin.php?page=employees&action=list');
                    exit;
                } catch (Exception $e) {
                    // Nếu update lỗi thì lưu thông báo lỗi
                    $errorMessage = "Lỗi cập nhật nhân viên: " . $e->getMessage();
                    // Có thể redirect về form edit với thông báo lỗi, hoặc giữ nguyên trang
                    $roles = $employeeModel->getRoles();
                    $employee = $employeeModel->getById($id);
                    include 'views/employees/edit.php';
                }
        
            } else {
                try {
                    $roles = $employeeModel->getRoles();
                    $employee = $employeeModel->getById($id);
                    include 'views/employees/edit.php';
                } catch (Exception $e) {
                    $errorMessage = "Lỗi lấy dữ liệu nhân viên: " . $e->getMessage();
                    include 'views/errors/500.php'; // hoặc show lỗi tùy bạn
                }
            }
            break;
            
        case 'delete':
            if(!$hasStaffDeletePermission) {
                $errorMessage = "Bạn không có quyền xóa nhân viên.";
                header('Location: admin.php?page=employees&action=list');
                exit;
            }
            $id = $_GET['id'];
            $employeeModel->delete($id);
            header('Location: admin.php?page=employees&action=list');
            exit;
    }
}