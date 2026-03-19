<?php
// 1) Nhận orderID từ URL
$orderID = $_GET['id'] ?? null;
if (!$orderID) {
    header('Location: admin.php?page=orders&action=list');
    exit;
}

// 2) XỬ LÝ POST TỪ FORM “CẬP NHẬT NHANH”
//    - Khi người dùng submit, sẽ có $_POST['status']
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'];

    // Cập nhật vào bảng orders
    $stmt = $pdo->prepare("UPDATE `orders` SET `Status` = ? WHERE `OrderID` = ?");
    $stmt->execute([$newStatus, $orderID]);

    // PRG: redirect về lại detail để tránh resubmit khi reload
    header("Location: admin.php?page=orders&action=detail&id={$orderID}");
    exit;
}

// 3) FETCH DỮ LIỆU ORDER
try {
    $stmt = $pdo->prepare("
        SELECT o.*, u.Fullname AS UserFullname, u.Email AS UserEmail, u.PhoneNumber AS UserPhoneNumber
        FROM `orders` o
        LEFT JOIN `user` u ON o.UserID = u.UserID
        WHERE o.OrderID = ?
    ");
    $stmt->execute([$orderID]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        echo "<p style='color:red;'>Đơn hàng #{$orderID} không tồn tại.</p>";
        exit;
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Lỗi SQL: " . $e->getMessage() . "</p>";
    exit;
}

// 4) FETCH ORDER DETAIL như bạn đã có
try {
    $stmt = $pdo->prepare("
        SELECT od.*, ps.size AS ProductSize, p.ProductName, p.ImageURL 
        FROM `orderdetail` od
        JOIN `productsize` ps ON od.ProductSizeID = ps.ProductSizeID
        JOIN `product` p ON ps.ProductID = p.ProductID
        WHERE od.OrderID = ?
    ");
    $stmt->execute([$orderID]);
    $orderdetail = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red;'>Lỗi SQL: " . $e->getMessage() . "</p>";
    $orderdetail = [];
}



// Lấy trạng thái hiện tại
$currentStatus = $order['Status'];

// Định nghĩa các tùy chọn cho dropdown dựa trên trạng thái hiện tại
$statusOptions = [];
if ($currentStatus == 'Pending') {
    $statusOptions = ['Processing', 'Completed', 'Cancelled'];
} elseif ($currentStatus == 'Processing') {
    $statusOptions = ['Completed', 'Cancelled'];
} elseif ($currentStatus == 'Completed' || $currentStatus == 'Cancelled') {
    $statusOptions = []; // Không có tùy chọn nào để chỉnh sửa
}


?>
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-8">
        <!-- Header section -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Chi tiết đơn hàng #<?= $order['OrderID'] ?></h2>
                <a href="admin.php?page=orders&action=list" class="text-blue-600 hover:text-blue-800 text-sm flex items-center mt-2">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách đơn hàng
                </a>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    <?= $order['Status'] == 'Completed' ? 'bg-green-100 text-green-800' : ($order['Status'] == 'Processing' ? 'bg-blue-100 text-blue-800' : ($order['Status'] == 'Cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) ?>">
                    <?= $order['Status'] == 'Completed' ? 'Đã giao hàng' : ($order['Status'] == 'Processing' ? 'Đang giao hàng' : ($order['Status'] == 'Cancelled' ? 'Đã hủy' : 'Chờ xác nhận')) ?>
                </span>
            </div>
        </div>

        <!-- Order info section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 mb-3">Thông tin khách hàng</h3>
                <p class="text-gray-600 mb-2"><span class="font-medium">Tên:</span> <?= htmlspecialchars($order['UserFullname']) ?></p>
                <p class="text-gray-600 mb-2"><span class="font-medium">Email:</span> <?= htmlspecialchars($order['UserEmail']) ?></p>
                <p class="text-gray-600"><span class="font-medium">SĐT:</span> <?= htmlspecialchars($order['UserPhoneNumber']) ?></p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 mb-3">Thông tin đơn hàng</h3>
                <p class="text-gray-600 mb-2"><span class="font-medium">Ngày đặt:</span> <?= date('d/m/Y H:i', strtotime($order['OrderDate'])) ?></p>
                <p class="text-gray-600 mb-2"><span class="font-medium">Tổng tiền:</span>
                    <span class="font-bold text-blue-600 text-lg itemsTotal">0 ₫</span>
                </p>
            </div>
        </div>

        <!-- Address section -->
        <div class="mb-8 bg-gray-50 p-4 rounded-lg">
            <h3 class="font-medium text-gray-700 mb-3">Thông tin giao hàng</h3>
            <p class="text-gray-600 mb-2"><span class="font-medium">Địa chỉ:</span> <?= htmlspecialchars($order['ShippingAddress']) ?></p>
            <p class="text-gray-600 mb-2"><span class="font-medium">ID Tỉnh/Thành:</span> <?= htmlspecialchars($order['ProvinceID']) ?></p>
            <p class="text-gray-600 mb-2"><span class="font-medium">ID Quận/Huyện:</span> <?= htmlspecialchars($order['DistrictID']) ?></p>
            <p class="text-gray-600"><span class="font-medium">ID Phường/Xã:</span> <?= htmlspecialchars($order['WardID']) ?></p>
        </div>

        <!-- Quick status update form -->
        <?php if (!empty($statusOptions)): ?>
            <form method="POST" action="admin.php?page=orders&action=detail&id=<?= $order['OrderID'] ?>" class="border-t pt-6 mb-8">
                <input type="hidden" name="id" value="<?= $order['OrderID'] ?>">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cập nhật nhanh trạng thái</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <?php foreach ($statusOptions as $option): ?>
                                <option value="<?= $option ?>" <?= $option == $currentStatus ? 'selected' : '' ?>>
                                    <?= $option == 'Processing' ? 'Đang giao hàng' : ($option == 'Completed' ? 'Đã giao hàng' : 'Đã hủy') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                            <i class="fas fa-sync-alt mr-2"></i> Cập nhật nhanh
                        </button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="border-t pt-6 mb-8 text-gray-500">
                <p>Đơn hàng đã ở trạng thái cuối (<?= $currentStatus == 'Completed' ? 'Đã giao hàng' : 'Đã hủy' ?>), không thể cập nhật trạng thái.</p>
            </div>
        <?php endif; ?>

        <!-- Products list section -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sản phẩm đã đặt</h3>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <ul class="divide-y divide-gray-200">
                    <?php if (!empty($orderdetail)): ?>
                        <?php foreach ($orderdetail as $item): ?>
                            <li class="p-4 hover:bg-gray-50">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 w-16 h-16">
                                        <img src=".<?= htmlspecialchars($item['ImageURL']) ?>"
                                            alt="<?= htmlspecialchars($item['ProductName']) ?>"
                                            class="w-full h-full object-cover rounded-md">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            <?= htmlspecialchars($item['ProductName']) ?>
                                            <?php if (isset($item['ProductSize'])): ?>
                                                <span class="text-gray-500">(Size: <?= htmlspecialchars($item['ProductSize']) ?>)</span>
                                            <?php endif; ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Số lượng: <?= $item['Quantity'] ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Đơn giá: <?= number_format($item['UnitPrice'], 0, ',', '.') ?> ₫
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-blue-600">
                                            <?= number_format($item['Subtotal'], 0, ',', '.') ?> ₫
                                        </p>

                                        <input type="hidden" class="subtotal-value" value="<?= $item['Subtotal'] ?>">
                                        <input type="hidden" class="calculated-value" value="<?= $item['UnitPrice'] * $item['Quantity'] ?>">
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500">Không có sản phẩm nào trong đơn hàng này.</p>
                    <?php endif; ?>
                </ul>

                <!-- Order summary -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="flex flex-col justify-end space-y-2">
                        <div class="flex justify-end space-x-4">
                            <span class="text-gray-600">Tổng cộng:</span>
                            <span class="font-bold text-blue-600 text-lg itemsTotal">0 ₫</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let total = 0;
        // Duyệt qua tất cả input subtotal-value
        document.querySelectorAll('.subtotal-value').forEach(function(el) {
            const v = parseFloat(el.value);
            if (!isNaN(v)) total += v;
        });

        // Cập nhật vào span#itemsTotal
        document.querySelectorAll('.itemsTotal').forEach((item) => {
            item.textContent = total.toLocaleString('vi-VN') + ' ₫';
        })
    });
</script>