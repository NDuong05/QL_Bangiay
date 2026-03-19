<?php
require_once 'db_connection.php';
// // Kết nối CSDL
// $host = 'localhost'; 
// $dbname = 'bangiay_db3'; 
// $username = 'root'; 
// $password = ''; 

try {
    // $pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname;charset=utf8", $username, $password);
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lấy danh sách tỉnh
    $provinces = $pdo->query("SELECT * FROM province")->fetchAll(PDO::FETCH_ASSOC);

    // Lọc quận theo tỉnh nếu đã chọn
    $province_id = $_GET['province'] ?? null;
    $district_id = $_GET['district'] ?? null;

    if ($province_id && is_numeric($province_id)) {
        $stmt = $pdo->prepare("SELECT * FROM district WHERE province_id = ?");
        $stmt->execute([$province_id]);
        $districts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $districts = [];
    }

    // Lọc phường theo quận nếu đã chọn
    if ($district_id && is_numeric($district_id)) {
        $stmt = $pdo->prepare("SELECT * FROM wards WHERE district_id = ?");
        $stmt->execute([$district_id]);
        $wards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $wards = [];
    }

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Xử lý điều kiện lọc
$where = [];
$params = [];

if (!empty($_GET['province']) && is_numeric($_GET['province'])) {
    $where[] = "o.ProvinceID = :province";
    $params[':province'] = (int)$_GET['province'];
}
if (!empty($_GET['district']) && is_numeric($_GET['district'])) {
    $where[] = "o.DistrictID = :district";
    $params[':district'] = (int)$_GET['district'];
}
if (!empty($_GET['ward']) && is_numeric($_GET['ward'])) {
    $where[] = "o.WardID = :ward";
    $params[':ward'] = (int)$_GET['ward'];
}
if (!empty($_GET['date_from'])) {
    $where[] = "o.OrderDate >= :date_from";
    $params[':date_from'] = $_GET['date_from'] . ' 23:59:59';
}
if (!empty($_GET['date_to'])) {
    $where[] = "o.OrderDate <= :date_to";
    $params[':date_to'] = $_GET['date_to'] . ' 23:59:59';
}
if (!empty($_GET['status'])) {
    $where[] = "o.Status = :status";
    $params[':status'] = $_GET['status'];
}


// Tạo câu lệnh SQL
$sql = "SELECT o.*, u.Fullname AS UserFullname FROM orders o 
        LEFT JOIN user u ON o.UserID = u.UserID";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY OrderDate DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red;'>Lỗi SQL: " . $e->getMessage() . "</p>";
    $orders = [];
}
?>
<?php if (!$hasOrderViewPermission): ?>
    <div style="filter: blur(3px); pointer-events: none;">
<?php endif; ?>
<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-8">

        <!-- FORM FILTER -->
        <form method="GET" class="mb-6 flex flex-wrap gap-4" action="?page=orders&action=list">
            <input type="hidden" name="page" value="orders">
            <input type="hidden" name="action" value="list">

            <!-- Tỉnh -->
            <div>
                <label for="province">Tỉnh</label>
                <select name="province" id="province" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    <?php foreach ($provinces as $p): ?>
                        <option value="<?= $p['province_id'] ?>" <?= ($_GET['province'] ?? '') == $p['province_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Quận -->
            <div>
                <label for="district">Quận</label>
                <select name="district" id="district" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    <?php foreach ($districts as $d): ?>
                        <option value="<?= $d['district_id'] ?>" <?= ($_GET['district'] ?? '') == $d['district_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Phường -->
            <div>
                <label for="ward">Phường</label>
                <select name="ward" id="ward">
                    <option value="">Tất cả</option>
                    <?php foreach ($wards as $w): ?>
                        <option value="<?= $w['wards_id'] ?>" <?= ($_GET['ward'] ?? '') == $w['wards_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($w['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Ngày -->
            <div>
                <label for="date_from">Từ ngày</label>
                <input type="date" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>">
            </div>
            <div>
                <label for="date_to">Đến ngày</label>
                <input type="date" name="date_to" value="<?= $_GET['date_to'] ?? '' ?>">
            </div>

            <!-- Trạng thái -->
            <div>
                <label for="status">Trạng thái</label>
                <select name="status" id="status">
                    <option value="">Tất cả</option>
                    <option value="Pending" <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                    <option value="Processing" <?= ($_GET['status'] ?? '') === 'Processing' ? 'selected' : '' ?>>Đang giao hàng</option>
                    <option value="Completed" <?= ($_GET['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Đã giao hàng</option>
                    <option value="Cancelled" <?= ($_GET['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                    Lọc
                </button>
            </div>
        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <?php if (!empty($orders)): ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đặt</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $order['OrderID'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($order['UserFullname']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $order['OrderDate'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200">
                                        <?= $order['Status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="admin.php?page=orders&action=detail&id=<?= $order['OrderID'] ?>"
                                        class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye mr-1"></i> Chi tiết
                                    </a>                   
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="mt-4 text-gray-500 text-sm">Không có đơn hàng nào phù hợp.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if (!$hasOrderViewPermission): ?>
    </div>
    <div style="margin-top: 15px; color: red; font-weight: bold;">
        Bạn không được quyền xem nội dung này.
    </div>
<?php endif; ?>