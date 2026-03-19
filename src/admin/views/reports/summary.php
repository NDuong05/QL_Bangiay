<?php if (!$hasReportViewPermission): ?>
    <div style="filter: blur(3px); pointer-events: none;">
<?php endif; ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-8">Báo cáo tổng hợp hệ thống</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Thống kê tài chính -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Thống kê tài chính</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Tổng nhập hàng:</span>
                    <span class="font-medium text-red-600"><?= number_format($totalImport, 0, ',', '.') ?> ₫</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Tổng doanh thu:</span>
                    <span class="font-medium text-green-600"><?= number_format($totalSales, 0, ',', '.') ?> ₫</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                    <span class="text-gray-600 font-medium">Lợi nhuận:</span>
                    <span class="font-bold text-blue-600"><?= number_format($totalSales - $totalImport, 0, ',', '.') ?> ₫</span>
                </div>
            </div>
        </div>

        <!-- Thống kê người dùng -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Thống kê người dùng</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Tổng số người dùng:</span>
                    <span class="font-medium text-purple-600"><?= $userCount ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Số quản trị viên:</span>
                    <span class="font-medium text-purple-600"><?= $adminCount ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Số nhân viên:</span>
                    <span class="font-medium text-purple-600"><?= $employeeCount ?></span>
                </div>
            </div>
        </div>

        <!-- Thống kê sản phẩm -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Thống kê sản phẩm</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Số sản phẩm trong kho:</span>
                    <span class="font-medium text-green-600"><?= $productCount ?></span>
                </div>
                <!-- Có thể thêm các thống kê khác về sản phẩm nếu có -->
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Số đơn hàng:</span>
                    <span class="font-medium text-green-600"><?= $orderCount ?? 'N/A' ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Số phiếu nhập:</span>
                    <span class="font-medium text-green-600"><?= $importCount ?? 'N/A' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê đơn hàng và nhập hàng -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Thống kê đơn hàng -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Thống kê đơn hàng</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Tổng số đơn hàng:</span>
                    <span class="font-medium text-yellow-600"><?= $orderCount ?></span>
                </div>
                <div class="flex items-center mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <?php
                        $pendingPercent = $orderCount > 0 ? ($pendingOrderCount / $orderCount) * 100 : 0;
                        $processedPercent = $orderCount > 0 ? ($processedOrderCount / $orderCount) * 100 : 0;
                        $cancelledPercent = $orderCount > 0 ? ($cancelledOrderCount / $orderCount) * 100 : 0;
                        ?>
                        <div class="bg-yellow-300 h-2.5 rounded-l-full" style="width: <?= $pendingPercent ?>%"></div>
                        <div class="bg-green-500 h-2.5" style="width: <?= $processedPercent ?>%; margin-top: -2.5px;"></div>
                        <div class="bg-red-500 h-2.5 rounded-r-full" style="width: <?= $cancelledPercent ?>%; margin-top: -2.5px;"></div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <div class="text-center">
                        <div class="flex items-center justify-center space-x-1">
                            <div class="w-3 h-3 bg-yellow-300 rounded-full"></div>
                            <span class="text-xs text-gray-600">Chờ xử lý</span>
                        </div>
                        <p class="font-medium"><?= $pendingOrderCount ?> (<?= round($pendingPercent) ?>%)</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center space-x-1">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-xs text-gray-600">Đã xử lý</span>
                        </div>
                        <p class="font-medium"><?= $processedOrderCount ?> (<?= round($processedPercent) ?>%)</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center space-x-1">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-xs text-gray-600">Đã hủy</span>
                        </div>
                        <p class="font-medium"><?= $cancelledOrderCount ?> (<?= round($cancelledPercent) ?>%)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê nhập hàng -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Thống kê nhập hàng</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Tổng số phiếu nhập:</span>
                    <span class="font-medium text-indigo-600"><?= $importCount ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Trung bình giá trị phiếu nhập:</span>
                    <span class="font-medium text-indigo-600">
                        <?= $importCount > 0 ? number_format($totalImport / $importCount, 0, ',', '.') : 0 ?> ₫
                    </span>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (!$hasReportViewPermission): ?>
    </div>
    <div style="margin-top: 15px; color: red; font-weight: bold;">
        Bạn không được quyền xem nội dung này.
    </div>
<?php endif; ?>