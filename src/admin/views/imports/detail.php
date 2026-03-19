<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-8">
        <!-- Header section -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Chi tiết phiếu nhập #<?= $import['ImportID'] ?></h2>
                <a href="admin.php?page=imports&action=list" class="text-blue-600 hover:text-blue-800 text-sm flex items-center mt-2">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách nhập hàng
                </a>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                Đã nhập
            </span>
        </div>

        <!-- Import info section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 mb-3">Thông tin nhân viên</h3>
                <p class="text-gray-600 mb-2"><span class="font-medium">Tên:</span> <?= htmlspecialchars($import['EmployeeFullname']) ?></p>
                <p class="text-gray-600"><span class="font-medium">Email:</span> <?= htmlspecialchars($import['EmployeeEmail'] ?? 'Không có') ?></p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 mb-3">Thông tin phiếu nhập</h3>
                <p class="text-gray-600 mb-2"><span class="font-medium">Ngày nhập:</span> <?= date('d/m/Y H:i', strtotime($import['ImportDate'])) ?></p>
                <p class="text-gray-600 mb-2"><span class="font-medium">Tổng tiền:</span>
                    <span class="font-bold text-blue-600"><?= number_format($import['Total'], 0, ',', '.') ?> ₫</span>
                </p>
            </div>
        </div>

        <!-- Products list section -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sản phẩm đã nhập</h3>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($importDetails as $item): ?>
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
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Size: <?= htmlspecialchars($item['size']) ?>
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
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Import summary -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="flex justify-end space-x-4">
                        <span class="text-gray-600">Tổng cộng:</span>
                        <span class="font-bold text-blue-600 text-lg">
                            <?= number_format($import['Total'], 0, ',', '.') ?> ₫
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>