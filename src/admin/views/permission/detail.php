<?php

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$nameVaiTro = $pdo->prepare("SELECT ten_vai_tro FROM vai_tro WHERE id = ?");
$nameVaiTro->execute([$id]);
$nameVaiTro = $nameVaiTro->fetchColumn();

// Lấy tất cả quyền đã gán cho vai trò
$stmt = $pdo->prepare("SELECT id_quyen FROM chitiet_vaitro_quyen WHERE id_vaitro = ?");
$stmt->execute([$id]);
$groupedPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN); // danh sách ID quyền đã chọn

// Tạo bảng phân quyền theo nhóm chức năng
$groupedFunctions = [];
$chucNangList = $pdo->query("SELECT * FROM chuc_nang")->fetchAll(PDO::FETCH_ASSOC);

foreach ($chucNangList as $cn) {
    $quyenList = $pdo->prepare("SELECT * FROM quyen WHERE id_chuc_nang = ?");
    $quyenList->execute([$cn['id']]);
    $groupedFunctions[$cn['ten_chuc_nang']] = $quyenList->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Chi tiết nhóm quyền</title>

</head>

<body>
    <h1>Chi tiết nhóm quyền #<?php echo htmlspecialchars($role['id']); ?></h1>
        <?php if ($role): ?>
        <p><strong>Tên nhóm quyền:</strong> <?php echo htmlspecialchars($role['ten_vai_tro']); ?></p>

        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Chức năng</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Chọn</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php foreach ($groupedFunctions as $category => $permissions): ?>
                    <tr class="bg-blue-50">
                        <td colspan="2" class="px-6 py-2 font-bold text-blue-900"><?= htmlspecialchars($category) ?></td>
                    </tr>
                    <?php foreach ($permissions as $perm): ?>
                        <?php
                            $checked = in_array($perm['id'], $groupedPermissions) ? 'checked' : '';
                        ?>
                        <tr>
                            <td class="px-6 py-2 text-sm"><?= htmlspecialchars($perm['ten_quyen']) ?></td>
                            <td class="px-6 py-2">
                                <input disabled type="checkbox"
                                    name="function_ids[]"
                                    value="<?= $perm['id'] ?>"
                                    <?= $checked ?>
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Không tìm thấy nhóm quyền.</p>
    <?php endif; ?>
    <br>
    <a href="admin.php?page=decentralization&action=list">Quay lại danh sách</a>
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