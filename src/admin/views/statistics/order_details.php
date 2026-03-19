<?php
$pdo = $this->pdo; // Lấy PDO từ controller
include_once(__DIR__ . '/../../db_connection.php');

// Kiểm tra xem có 'id' trong URL không
if (isset($_GET['id'])) {
    $orderID = $_GET['id'];

    // Truy vấn thông tin đơn hàng
    $query = "
        SELECT 
            o.OrderID,
            o.OrderDate,
            o.Status,
            o.UserID,
            u.Fullname,
            u.Email,
            u.PhoneNumber,
            (SELECT SUM(Subtotal) FROM orderdetail WHERE OrderID = o.OrderID) as total
        FROM 
            orders o
        JOIN 
            user u ON o.UserID = u.UserID
        WHERE 
            o.OrderID = :orderID
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
    $stmt->execute();
    $orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$orderDetails) {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>
                <strong class='font-bold'>Lỗi!</strong>
                <span class='block sm:inline'> Không tìm thấy đơn hàng này.</span>
              </div>";
        exit;
    }

    // Truy vấn chi tiết sản phẩm trong đơn hàng với thông tin danh mục và thương hiệu
    $queryOrderDetail = "
        SELECT 
            od.OrderID,
            ps.ProductID,
            p.ProductName,
            c.CategoryName,
            b.BrandName,
            ps.Size,
            od.Quantity,
            od.Subtotal,
            p.ImageURL
        FROM 
            orderdetail od
        JOIN 
            productsize ps ON od.ProductSizeID = ps.ProductSizeID
        JOIN 
            product p ON ps.ProductID = p.ProductID
        LEFT JOIN
            categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN
            brand b ON p.BrandID = b.BrandID
        WHERE 
            od.OrderID = :orderID
    ";

    $stmtOrderDetail = $pdo->prepare($queryOrderDetail);
    $stmtOrderDetail->bindParam(':orderID', $orderID, PDO::PARAM_INT);
    $stmtOrderDetail->execute();
    $orderItems = $stmtOrderDetail->fetchAll(PDO::FETCH_ASSOC);
    
    // Tính số lượng sản phẩm
    $totalItems = 0;
    foreach ($orderItems as $item) {
        $totalItems += $item['Quantity'];
    }
}

// Hàm hiển thị trạng thái đơn hàng với màu sắc tương ứng
function getStatusBadge($status) {
    switch(strtolower($status)) {
        case 'đã hoàn thành':
            return '<span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Đã hoàn thành</span>';
        case 'đang xử lý':
            return '<span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full">Đang xử lý</span>';
        case 'đã hủy':
            return '<span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Đã hủy</span>';
        case 'chờ xác nhận':
            return '<span class="px-2 py-1 font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full">Chờ xác nhận</span>';
        default:
            return '<span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">' . htmlspecialchars($status) . '</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?= $orderDetails['OrderID'] ?> - Bro Shoes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .print-section {
            display: none;
        }
        @media print {
            .no-print {
                display: none;
            }
            .print-section {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header với thông tin đơn hàng -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Chi tiết đơn hàng <span class="text-blue-600">#<?= $orderDetails['OrderID'] ?></span>
            </h1>
            <div class="space-x-2 no-print">
                
                <a href="admin.php?page=statistics&action=index" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại
                </a>
            </div>
        </div>

        <!-- Thông tin chính -->
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            <!-- Thông tin đơn hàng -->
            <div class="bg-white rounded-lg shadow-md p-6 flex-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Thông tin đơn hàng</h3>
                    <div>
                        <?= getStatusBadge($orderDetails['Status']) ?>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 mb-1">Mã đơn hàng:</p>
                        <p class="font-medium">#<?= $orderDetails['OrderID'] ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-1">Ngày đặt hàng:</p>
                        <p class="font-medium"><?= date('d/m/Y H:i', strtotime($orderDetails['OrderDate'])) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-1">Tổng sản phẩm:</p>
                        <p class="font-medium"><?= $totalItems ?> sản phẩm</p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-1">Tổng giá trị:</p>
                        <p class="font-medium text-blue-600"><?= number_format($orderDetails['total'], 0, ',', '.') ?> VND</p>
                    </div>
                </div>
            </div>

            <!-- Thông tin khách hàng -->
            <div class="bg-white rounded-lg shadow-md p-6 flex-1">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Thông tin khách hàng</h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <i class="fas fa-user text-blue-500 mr-3 mt-1 w-5"></i>
                        <div>
                            <p class="text-gray-600 text-sm">Họ tên:</p>
                            <p class="font-medium"><?= htmlspecialchars($orderDetails['Fullname']) ?></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-envelope text-blue-500 mr-3 mt-1 w-5"></i>
                        <div>
                            <p class="text-gray-600 text-sm">Email:</p>
                            <p class="font-medium"><?= htmlspecialchars($orderDetails['Email']) ?></p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-phone text-blue-500 mr-3 mt-1 w-5"></i>
                        <div>
                            <p class="text-gray-600 text-sm">Số điện thoại:</p>
                            <p class="font-medium"><?= htmlspecialchars($orderDetails['PhoneNumber']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chi tiết sản phẩm -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Chi tiết sản phẩm</h3>
            
            <!-- Sản phẩm dạng thẻ (card) -->
            <div class="grid grid-cols-1 gap-4">
                <?php foreach ($orderItems as $item): ?>
                <div class="border border-gray-200 rounded-lg p-4 flex flex-col md:flex-row">
                    <!-- Hình ảnh sản phẩm -->
                    <div class="w-full md:w-32 h-32 bg-gray-100 rounded-lg mb-4 md:mb-0 flex items-center justify-center overflow-hidden">
                        <?php if (!empty($item['ImageURL'])): ?>
                            <img src=".<?= htmlspecialchars($item['ImageURL']) ?>" alt="<?= htmlspecialchars($item['ProductName']) ?>" class="object-cover h-full w-full">
                        <?php else: ?>
                            <i class="fas fa-shoe-prints text-gray-400 text-4xl"></i>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Thông tin sản phẩm -->
                    <div class="md:ml-6 flex-1">
                        <div class="flex flex-col md:flex-row md:justify-between">
                            <div>
                                <h4 class="font-semibold text-lg"><?= htmlspecialchars($item['ProductName']) ?></h4>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Mã: <?= $item['ProductID'] ?></span>
                                    <?php if (!empty($item['CategoryName'])): ?>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded"><?= htmlspecialchars($item['CategoryName']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($item['BrandName'])): ?>
                                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded"><?= htmlspecialchars($item['BrandName']) ?></span>
                                    <?php endif; ?>
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Size: <?= $item['Size'] ?></span>
                                </div>
                            </div>
                            <div class="mt-3 md:mt-0 md:text-right">
                                <div class="text-sm text-gray-600">Số lượng: <span class="font-medium"><?= $item['Quantity'] ?></span></div>
                                <div class="font-semibold text-blue-600 mt-1"><?= number_format($item['Subtotal'], 0, ',', '.') ?> VND</div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Tổng cộng -->
            <div class="mt-6 border-t pt-4">
                <div class="flex justify-end">
                    <div class="w-full md:w-64">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Tổng số lượng:</span>
                            <span class="font-medium"><?= $totalItems ?> sản phẩm</span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Tổng thanh toán:</span>
                            <span class="text-blue-600"><?= number_format($orderDetails['total'], 0, ',', '.') ?> VND</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        

        
    </div>
</body>
</html>