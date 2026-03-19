<h2>Giỏ hàng</h2>
<table border="1">
    <thead>
        <tr>
            <th>Sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cartItems as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['ProductName']) ?></td>
            <td><?= number_format($item['Price'], 0, ',', '.') ?> VND</td>
            <td><?= $item['Quantity'] ?></td>
            <td>
                <a href="index.php?page=cart&action=remove&userId=<?= $item['UserID'] ?>&productId=<?= $item['ProductID'] ?>" onclick="return confirm('Xóa khỏi giỏ hàng?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>