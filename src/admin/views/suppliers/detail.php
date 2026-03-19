<!DOCTYPE html>
<html>

<head>
    <title>Chi tiết nhà cung cấp</title>

</head>

<body>
    <h1>Chi tiết nhà cung cấp #<?php echo htmlspecialchars($supplier['SupplierID']); ?></h1>
    <?php if ($supplier): ?>
        <p><strong>Tên nhà cung cấp:</strong> <?php echo htmlspecialchars($supplier['SupplierName']); ?></p>

        <!-- Nếu có danh sách sản phẩm cung cấp, có thể thêm sau -->
        <h2>Sản phẩm cung cấp</h2>
        <table>
            <tr>
                <th>ID Sản phẩm</th>
                <th>Tên sản phẩm</th>
                <th>Giá nhập</th>
                <th>Cập nhật lần cuối</th>
            </tr>
            <?php
            // Giả sử bạn có phương thức getProductsBySupplierId trong SupplierModel
            $products = $supplierModel->getProductsBySupplierId($supplier['SupplierID']);
            ?>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['ProductID']); ?></td>
                        <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                        <td><?php echo number_format($product['ImportPrice'], 2); ?> VND</td>
                        <td><?php echo htmlspecialchars($product['LastUpdated']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Không có sản phẩm nào từ nhà cung cấp này.</td>
                </tr>
            <?php endif; ?>
        </table>
    <?php else: ?>
        <p>Không tìm thấy nhà cung cấp.</p>
    <?php endif; ?>
    <br>
    <a href="admin.php?page=suppliers&action=list">Quay lại danh sách</a>
</body>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

   

    a:hover {
        text-decoration: underline;
    }
</style>

</html>