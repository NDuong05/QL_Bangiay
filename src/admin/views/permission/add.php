<?php
require_once 'db_connection.php';
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

$groupedFunctions = [];

// Nhóm theo danh mục nếu có
foreach ($allFunctions as $function) {
    $category = $function['ten_chuc_nang'] ?? 'Chung';
    $PermissionList = $pdo->query("SELECT * FROM quyen WHERE id_chuc_nang = {$function['id']}")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($PermissionList as $permission) {
        extract($permission);
        $groupedFunctions[$category][]= $permission;
    }
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
    <title>Thêm nhóm quyền mới</title>
    <style>
        label {
            display: inline-block;
            width: 150px;
        }
        input[type="text"] {
            width: 300px;
            padding: 5px;
        }
        button {
            padding: 8px 15px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex justify-between items-center mb-8 border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Thêm nhóm quyền mới</h2>
                <a href="admin.php?page=decentralization&action=list"
                    class="text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
                </a>
            </div>

            <form method="POST" action="admin.php?page=decentralization&action=add">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div class="mb-4">
                    <label for="role-name" class="block text-sm font-medium text-gray-700">
                        Tên nhóm quyền <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="role-name" name="name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md" placeholder="VD: Quản trị viên">
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-medium mb-2">Phân quyền chức năng</h3>
                    <table class="min-w-full border border-gray-200 rounded-lg divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Chức năng</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Chọn</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php 
                            $str = '';
                            foreach ($groupedFunctions as $category => $permissions){
                                $str .= '<tr class="bg-blue-50">
                                    <td colspan="2" class="px-6 py-2 font-bold text-blue-900">'.$category.'</td>
                                </tr>';
                                foreach ($permissions as $permission){
                                    extract($permission); 
                                    $str .= '<tr>
                                        <td class="px-6 py-2 text-sm">'.($ten_quyen).'</td>
                                        <td class="px-6 py-2">
                                            <input type="checkbox"
                                                name="function_ids[]"
                                                value="'.$id.'"
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                        </td>
                                    </tr>';
                                }}
                                echo $str;
                                    ?> 
                            <tr class="bg-blue-50">
                                    <td colspan="1" class="px-6 py-2 font-bold text-blue-900">Chọn tất cả</td>
                                    <td class="px-6 py-2">
                                        <input type="checkbox" id="checkAll"class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="admin.php?page=decentralization&action=list"
                        class="px-6 py-2 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50">Hủy</a>
                    <button type="submit"
                        class="px-6 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                        Thêm nhóm quyền
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
