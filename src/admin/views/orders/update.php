<div class="max-w-2xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Cập nhật đơn hàng #<?= $order['OrderID'] ?></h2>
            <a href="admin.php?page=orders&action=list" class="text-blue-600 hover:text-blue-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Lỗi</p>
                <p><?= $error ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p class="font-bold">Thành công</p>
                <p><?= $success ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin.php?page=orders&action=update&id=<?= $order['OrderID'] ?>" class="space-y-6">
            <!-- Order Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã đơn hàng</label>
                    <input type="text" value="<?= $order['OrderID'] ?>" readonly
                        class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tổng tiền (VND)</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">₫</span>
                        </div>
                        <input type="number" name="Total" value="<?= $order['Total'] ?>" required
                            class="block w-full pl-10 pr-12 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- User Info -->
            <div class="bg-gray-50 p-4 rounded-md mb-4">
                <h3 class="text-md font-medium text-gray-700 mb-2">Thông tin khách hàng</h3>
                <p class="text-sm text-gray-600"><span class="font-medium">Tên:</span> <?= htmlspecialchars($order['UserFullname']) ?></p>
                <p class="text-sm text-gray-600"><span class="font-medium">Email:</span> <?= htmlspecialchars($order['UserEmail']) ?></p>
                <p class="text-sm text-gray-600"><span class="font-medium">SĐT:</span> <?= htmlspecialchars($order['UserPhoneNumber'] ?? 'N/A') ?></p>
            </div>

            <!-- Status Update -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái đơn hàng</label>
                <select name="Status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="Pending" <?= $order['Status'] == 'Pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                    <option value="Processing" <?= $order['Status'] == 'Processing' ? 'selected' : '' ?>>Đang giao hàng</option>
                    <option value="Processed" <?= $order['Status'] == 'Processed' ? 'selected' : '' ?>>Đã giao hàng</option>
                    <option value="Cancelled" <?= $order['Status'] == 'Cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">Lưu ý: Việc thay đổi trạng thái có thể ảnh hưởng đến kho hàng</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
                <a href="admin.php?page=orders&action=detail&id=<?= $order['OrderID'] ?>"
                    class="mr-3 px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-times mr-2"></i> Hủy
                </a>
                <button type="submit" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>