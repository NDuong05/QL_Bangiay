<?php
require 'db_connect.php';

$tensp = $_GET['tensp'] ?? '';
$idcatalog = $_GET['catalogID'] ?? '';

$filterbrands = $_GET['filterbrands'] ?? '';
$filtersizes = $_GET['filtersizes'] ?? '';
$filtergenders = $_GET['filtergenders'] ?? '';
$pricelowerbound =  $_GET['pricelowerbound'] ?? '';
$priceupperbound =  $_GET['priceupperbound'] ?? '';

$tensp = trim($tensp); // loại bỏ khoảng trắng đầu/cuối
$conn = connectdb();
$params = [];
$sql = "SELECT * FROM product";
$where = [];

if ($idcatalog != 0) {
    $where[] = "CategoryID = $idcatalog";
}

if (!empty($filterbrands)) {
    $brands = array_map('intval', $filterbrands);
    $where[] = "BrandID IN (" . implode(',', $brands) . ")";
}

if (!empty($filtergenders)) {
    $genders = array_map(function($g) {
        return "'" . addslashes($g) . "'";
    }, $filtergenders);
    $where[] = "Gender IN (" . implode(',', $genders) . ")";
}

if (!empty($filtersizes)) {
    // Đổi bảng nếu có lọc size
    $sql = "SELECT * FROM product p JOIN productsize ps ON p.ProductID = ps.ProductID";
    $sizes = array_map('intval', $filtersizes);
    $where[] = "ProductSizeID IN (" . implode(',', $sizes) . ")";
}
if ($pricelowerbound !== '' && $priceupperbound !== '') {
    if ((int)$pricelowerbound > (int)$priceupperbound) {
        echo '<p style="padding: 10px;">The minimum price must be lower than the maximum price.</p>';
        exit;
    }

    $where[] = "Price BETWEEN ? AND ?";
    $params[] = (int)$pricelowerbound;
    $params[] = (int)$priceupperbound;
}
// Ghép điều kiện WHERE nếu có
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
if ($tensp != '') {
    $sql .= " WHERE ProductName LIKE ?";
    $params[] = "%$tensp%";
}
// if ($pricelowerbound !== '' && $priceupperbound !== '') {
//     if ((int)$pricelowerbound > (int)$priceupperbound) {
//         echo '<p style="padding: 10px;">The minimum price must be lower than the maximum price.</p>';
//         exit;
//     } else {
//         $sql .= "WHERE Price BETWEEN ? AND ?" ;
//         $params[] = "$pricelowerbound";
//         $params[] = "$priceupperbound";
//     }
// }

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Chuyển kích thước thành mảng và thêm thuộc tính isDeleted
foreach ($products as &$product) {
    $product['size'] = !empty($product['size']) ? explode(',', $product['size']) : [];
    $product['isDeleted'] = false;
}

if (empty($products)) {
    echo '<p style="padding: 10px;">The valid products are not found.</p>';
    exit;
}

$kq = "";

$seenNames = [];

foreach ($products as $product) {
    if (in_array($product['ProductID'], $seenNames)) {
        continue; // nếu tên đã hiển thị, bỏ qua
    }
    $seenNames[] = $product['ProductID'];

    $id = $product['ProductID'];
    $name = htmlspecialchars($product['ProductName']);
    $image = htmlspecialchars($product['ImageURL']);
    $price = number_format($product['Price'], 0, ',', '.');

    $kq .= '
        <div class="product-box" onclick="detailProduct(' . $id . ')">
            <div class="img-container">
                <img src="' . $image . '" alt="' . $name . '" 
                    onerror="this.src=\'view/layout/asset/img/catalogue/coming-soon.jpg\'" />
            </div>
            <div class="shoes-name">' . $name . '</div>
            <div class="shoes-price">' . $price . ' ₫</div>
        </div>';
}
$conn = null;
echo $kq;
?>
