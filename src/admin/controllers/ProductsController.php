<?php
require_once 'models/Product.php';
require_once 'db_connection.php';
require_once 'models/Permission.php';
$productModel = new Product($pdo);
$permissionModel = new Permission($pdo);
$userRoleId = $_SESSION['user']['RoleID'] ?? null;
$permissions = $permissionModel->getPermissionsByRole($userRoleId);
function getsize($pdo, $productId)
{
    $stmt = $pdo->prepare("SELECT * FROM productsize WHERE ProductID = ?");
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm lấy danh sách danh mục
function getCategories($pdo)
{
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY CategoryName");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm lấy danh sách thương hiệu
function getBrands($pdo)
{
    $stmt = $pdo->query("SELECT * FROM brand ORDER BY BrandName");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm xử lý upload ảnh
function handleImageUpload($productName)
{
    $targetDir = "../view/layout/asset/img/catalogue/";
    $dbImagePath = "./view/layout/asset/img/catalogue/";

    // Kiểm tra thư mục tồn tại, nếu không thì tạo mới
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Kiểm tra có file upload không
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // Lấy thông tin file
    $file = $_FILES['product_image'];
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileError = $file['error'];

    // Kiểm tra lỗi upload
    if ($fileError !== UPLOAD_ERR_OK) {
        return false;
    }

    // Kiểm tra kích thước file (giới hạn 5MB)
    if ($fileSize > 5000000) {
        return false;
    }

    // Lấy phần mở rộng của file
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Chỉ cho phép các định dạng ảnh phổ biến
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($fileExt, $allowedExts)) {
        return false;
    }

    // Tạo tên file mới để tránh trùng lặp
    $newFileName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $productName)) . '_' . uniqid() . '.' . $fileExt;
    $targetFilePath = $targetDir . $newFileName;
    $dbFilePath = $dbImagePath . $newFileName;

    // Upload file
    if (move_uploaded_file($fileTmp, $targetFilePath)) {
        return $dbFilePath;
    } else {
        return false;
    }
}
$hasProductViewPermission = in_array('product_view', $permissions);
$hasProductAddPermission = in_array('product_add', $permissions);
$hasProductEditPermission = in_array('product_edit', $permissions);
$hasProductDeletePermission = in_array('product_delete', $permissions);
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'list':
            $products = $productModel->getAll();
            include 'views/products/list.php';
            break;
        case 'add':
            if (!$hasProductAddPermission) {
                // echo "Bạn không có quyền thêm sản phẩm.";
                header('Location: admin.php?page=products&action=list');
                exit;
            }
            // Lấy danh sách danh mục và thương hiệu
            $categories = getCategories($pdo);
            $brands = getBrands($pdo);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Xử lý upload ảnh
                $imagePath = handleImageUpload($_POST['ProductName']);

                if ($imagePath === false) {
                    // Có lỗi xảy ra khi upload ảnh
                    $error = "Không thể tải lên hình ảnh. Vui lòng kiểm tra kích thước và định dạng file.";
                    include 'views/products/add.php';
                    break;
                }

                $data = [
                    'ProductName' => $_POST['ProductName'],
                    'CategoryID' => $_POST['CategoryID'],
                    'BrandID' => $_POST['BrandID'],
                    'Gender' => $_POST['Gender'],
                    'Price' => $_POST['Price'],
                    'sizes' => $_POST['sizes'],               // array các size
                    'stock_quantities' => $_POST['stock_quantities'],
                    'ImageURL' => $imagePath
                ];

                $productModel->add($data);
                header('Location: admin.php?page=products&action=list');
                exit;
            } else {
                include 'views/products/add.php';
            }
            break;
        case 'edit':
            if (!$hasProductEditPermission) {
                // echo "Bạn không có quyền sửa sản phẩm.";
                header('Location: admin.php?page=products&action=list');
                exit;
            }
            $id = $_GET['id'];
            // Lấy danh sách danh mục và thương hiệu
            $categories = getCategories($pdo);
            $brands = getBrands($pdo);
            $sizes = getsize($pdo, $id);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = [
                    'ProductName' => $_POST['ProductName'],
                    'CategoryID' => $_POST['CategoryID'],
                    'BrandID' => $_POST['BrandID'],
                    'Gender' => $_POST['Gender'],
                    'Price' => $_POST['Price'],
                    'sizes' => $_POST['sizes'],
                    'product_size_ids' => $_POST['product_size_ids'],              // array các size
                    'stock_quantities' => $_POST['stock_quantities'],
                    'ImageURL' => $_POST['current_image'] // Giữ nguyên ảnh cũ nếu không có ảnh mới

                ];

                // Nếu có file ảnh mới được upload
                if (!empty($_FILES['product_image']['name'])) {
                    $imagePath = handleImageUpload($_POST['ProductName']);

                    if ($imagePath === false) {
                        // Có lỗi xảy ra khi upload ảnh
                        $error = "Không thể tải lên hình ảnh. Vui lòng kiểm tra kích thước và định dạng file.";
                        $product = $productModel->getById($id);
                        include 'views/products/edit.php';
                        break;
                    }

                    if ($imagePath !== null) {
                        $data['ImageURL'] = $imagePath;
                    }
                }

                $productModel->update($id, $data);
                header('Location: admin.php?page=products&action=list');
                exit;
            } else {
                $product = $productModel->getById($id);
                include 'views/products/edit.php';
            }
            break;
        case 'delete':
            if (!$hasProductDeletePermission) {
                // echo "Bạn không có quyền xóa sản phẩm.";
                header('Location: admin.php?page=products&action=list');
                exit;
            }
            $id = $_GET['id'];
            // Lấy thông tin sản phẩm trước khi xóa để xóa cả ảnh
            $product = $productModel->getById($id);
            $productModel->delete($id);

            // Xóa file ảnh nếu tồn tại
            if (!empty($product['ImageURL'])) {
                $imagePath = "../../../" . $product['ImageURL'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            header('Location: admin.php?page=products&action=list');
            exit;
    }
}
