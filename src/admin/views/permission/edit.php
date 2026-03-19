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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="function_ids[]"]');

        checkAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = checkAll.checked;
            });
        });
    });
</script>
<!DOCTYPE html>
<html>
<head>
    <title>Sửa nhóm quyền</title>
    <style>
        label { display: inline-block; width: 150px; }
        input[type="text"] { width: 300px; padding: 5px; }
        button { padding: 8px 15px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex justify-between items-center mb-8 border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Sửa nhóm quyền</h2>
                <a href="admin.php?page=decentralization&action=list"
                    class="text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
                </a>
            </div>

            <form method="POST" action="admin.php?page=decentralization&action=update">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div class="mb-4">
                    <label for="permission-name" class="block text-sm font-medium text-gray-700">Tên nhóm quyền <span class="text-red-500">*</span></label>
                    <input type="text" id="permission-name" name="name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md"
                        value="<?= htmlspecialchars($nameVaiTro) ?>">
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-medium mb-4 text-gray-900">Phân quyền chức năng</h3>
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
                                            <input type="checkbox"
                                                name="function_ids[]"
                                                value="<?= $perm['id'] ?>"
                                                <?= $checked ?>
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            <tr class="bg-blue-50">
                                    <td colspan="1" class="px-6 py-2 font-bold text-blue-900">Chọn tất cả</td>
                                    <td class="px-6 py-2">
                                        <input type="checkbox" id="checkAll"class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i> Lưu thay đổi
                    </button>
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
                    <?php endif; ?>
                </div>
                
            </form>
        </div>
    </div>
</body>
</html>
