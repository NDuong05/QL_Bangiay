<?php
require_once 'models/Import.php';
require_once 'models/Employee.php';
require_once 'db_connection.php';
require_once 'models/Permission.php';
require_once 'models/Product.php';

$permissionModel = new Permission($pdo);
$userRoleId = $_SESSION['user']['RoleID'] ?? null;
$permissions = $permissionModel->getPermissionsByRole($userRoleId);
$hasImportViewPermission = in_array('import_view', $permissions);
$hasImportAddPermission = in_array('import_add', $permissions);
$hasImportEditPermission = in_array('import_edit', $permissions);
$hasImportDeletePermission = in_array('import_delete', $permissions);
$importModel = new Import($pdo);
$employeeModel = new Employee($pdo);
$productmodel = new Product($pdo);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'list':
            $imports = $importModel->getAll();
            include 'views/imports/list.php';
            break;

        case 'detail':
            if (!$hasImportViewPermission) {
                header('Location: admin.php?page=imports&action=list');
                exit;
            }
            $id = $_GET['id'];
            $import = $importModel->getById($id);
            $importDetails = $importModel->getImportDetails($id);
            include 'views/imports/detail.php';
            break;

        case 'add':
            if (!$hasImportAddPermission) {
                header('Location: admin.php?page=imports&action=list');
                exit;
            }
            $employees = $employeeModel->getAll();
            $productSizes = $importModel->getProductSizes();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $details = [];
                $total = 0;

                // Xử lý chi tiết sản phẩm
                if (isset($_POST['productSizeID']) && is_array($_POST['productSizeID'])) {
                    for ($i = 0; $i < count($_POST['productSizeID']); $i++) {
                        if (!empty($_POST['productSizeID'][$i]) && !empty($_POST['quantity'][$i]) && !empty($_POST['unitPrice'][$i])) {
                            $subtotal = $_POST['quantity'][$i] * $_POST['unitPrice'][$i];
                            $total += $subtotal;

                            $details[] = [
                                'ProductSizeID' => $_POST['productSizeID'][$i],
                                'Quantity' => $_POST['quantity'][$i],
                                'UnitPrice' => $_POST['unitPrice'][$i]
                            ];
                        }
                    }
                }
                // Thu thập giá bán cho từng productID
                $productSizeIDs = $_POST['productSizeID'];
                $productSellPrices = [];
                if (!empty($productSizeIDs)) {
                    // Truy vấn để ánh xạ productSizeID sang productID
                    $placeholders = implode(',', array_fill(0, count($productSizeIDs), '?'));
                    $stmt = $pdo->prepare("SELECT ProductSizeID, ProductID FROM productsize WHERE ProductSizeID IN ($placeholders)");
                    $stmt->execute($productSizeIDs);
                    $productSizeToProduct = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

                    // Gán giá bán cho từng productID
                    foreach ($_POST['productSizeID'] as $i => $productSizeID) {
                        $productID = $productSizeToProduct[$productSizeID] ?? null;
                        if ($productID && isset($_POST['sellprice'][$i])) {
                            $productSellPrices[$productID] = $_POST['sellprice'][$i]; // Lấy giá bán cuối cùng cho mỗi productID
                        }
                    }
                }
                foreach ($productSellPrices as $productID => $sellPrice) {
                    $productmodel->updatePrice($productID, $sellPrice);
                }
                $data = [
                    'EmployeeID' => $_POST['EmployeeID'],
                    'Total' => $total,
                    'details' => $details
                ];

                $importModel->add($data);

                $importModel->updateStock($details);


                header('Location: admin.php?page=imports&action=list');
                exit;
            } else {
                include 'views/imports/add.php';
            }
            break;
    }
}
