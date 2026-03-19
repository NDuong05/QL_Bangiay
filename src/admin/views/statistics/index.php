<?php if (!$hasStatisticViewPermission): ?>
    <div style="filter: blur(3px); pointer-events: none;">
<?php endif; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Thống kê</h2>
        
        <!-- Thống kê tổng quan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-2">Tổng thu nhập</p>
                <p class="text-2xl font-bold text-blue-600"><?= number_format($totalIncome, 0, ',', '.') ?> VND</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-2">Tổng số sản phẩm</p>
                <p class="text-2xl font-bold text-blue-600"><?= count($products) ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-2">Tổng số tài khoản</p>
                <p class="text-2xl font-bold text-blue-600"><?= count($accounts) ?></p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600 mb-2">Tổng số đơn hàng</p>
                <p class="text-2xl font-bold text-blue-600"><?= count($orders) ?></p>
            </div>
        </div>
        
        <!-- Form lọc thời gian -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Thống kê top 5 khách hàng mua nhiều nhất</h3>
            <form method="POST" action="" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="start_date" class="block text-gray-700 font-medium">Từ ngày:</label>
                        <input type="date" id="start_date" name="start_date" required value="<?= $startDate ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="end_date" class="block text-gray-700 font-medium">Đến ngày:</label>
                        <input type="date" id="end_date" name="end_date" required value="<?= $endDate ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" name="filter_submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors">
                        Thống kê
                    </button>
                </div>
            </form>
        </div>

        <?php

                if (!empty($topCustomers)) {
                    usort($topCustomers, function($a, $b) {
                        return $b['total_purchase'] - $a['total_purchase'];
                });
             }
        ?>

        <!-- Kết quả thống kê top 5 khách hàng -->
        <?php if (!empty($topCustomers)): ?>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Top 5 khách hàng có mức mua hàng cao nhất</h3>
                <p class="text-sm text-gray-600">
                    Thời gian: <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?>
                </p>
            </div>
            
            <div class="space-y-6 p-6">
                <?php foreach ($topCustomers as $index => $customer): ?>
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4">
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">
                                #<?= $index + 1 ?> - <?= htmlspecialchars($customer['Fullname']) ?>
                            </h4>
                            <p class="text-gray-600"><?= htmlspecialchars($customer['Email']) ?></p>
                            <p class="text-gray-600">SĐT: <?= htmlspecialchars($customer['PhoneNumber']) ?></p>
                        </div>
                        <div class="mt-2 md:mt-0">
                            <p class="text-sm text-gray-600">Tổng giá trị mua hàng:</p>
                            <p class="text-xl font-bold text-blue-600"><?= number_format($customer['total_purchase'], 0, ',', '.') ?> VND</p>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Mã đơn hàng</th>
                                    <th class="py-3 px-6 text-left">Ngày đặt</th>
                                    <th class="py-3 px-6 text-right">Giá trị</th>
                                    <th class="py-3 px-6 text-center">Trạng thái</th>
                                    <th class="py-3 px-6 text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm">
                                <?php foreach ($customer['orders'] as $order): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">
                                        #<?= $order['OrderID'] ?>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <?= date('d/m/Y', strtotime($order['OrderDate'])) ?>
                                    </td>
                                    <td class="py-3 px-6 text-right">
                                        <?= number_format($order['total'], 0, ',', '.') ?> VND
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <?php 
                                        $statusClass = 'bg-green-100 text-green-800';
                                        if ($order['Status'] == 'Pending') {
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                        } elseif ($order['Status'] == 'Cancelled') {
                                            $statusClass = 'bg-red-100 text-red-800';
                                        }
                                        ?>
                                        <span class="<?= $statusClass ?> py-1 px-3 rounded-full text-xs">
                                            <?= $order['Status'] ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                    <a href="admin.php?page=statistics&action=order_details&id=<?= $order['OrderID'] ?>" class="text-blue-600 hover:text-blue-800 underline">
                                                Xem chi tiết
                                    </a>

                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="2" class="py-3 px-6 text-right font-bold">Tổng cộng:</td>
                                    <td class="py-3 px-6 text-right font-bold text-blue-600">
                                        <?= number_format($customer['total_purchase'], 0, ',', '.') ?> VND
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        // JavaScript để kiểm tra form
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                
                if (new Date(startDate) > new Date(endDate)) {
                    e.preventDefault();
                    alert('Ngày bắt đầu không thể lớn hơn ngày kết thúc!');
                }
            });
        });
    </script>
</body>
</html>
<?php if (!$hasStatisticViewPermission): ?>
    </div>
    <div style="margin-top: 15px; color: red; font-weight: bold;">
        Bạn không được quyền xem nội dung này.
    </div>
<?php endif; ?>