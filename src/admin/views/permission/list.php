<?php if (!$hasPermissionViewPermission): ?>
    <div style="filter: blur(3px); pointer-events: none;">
<?php endif; ?>
<?php
function renderButton($text, $href = '#', $iconClass = '', $itemclass= '',$disabled = false , )
{
    // $buttonClasses = 'inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white ';
    $itemclass .= $disabled ? 'cursor-not-allowed' : '';
    $hrefAttribute = $disabled ? '' : 'href="' . $href . '"';
    echo '<a ' . $hrefAttribute . ' class="' . $itemclass . '" >';
    if (!empty($iconClass)) {
        echo '<i class="' . $iconClass . ' mr-2"></i>';
    }
    echo $text;
    echo '</a>';
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Danh sách nhóm quyền</title>
    
</head>

<body>
    <div class="max-w-7xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Danh sách nhóm quyền</h2>
               
                <?php renderButton('Thêm nhóm quyền mới' , 'admin.php?page=decentralization&action=add' , 'fas fa-plus mr-2', 'inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700', !$hasPermissionAddPermission) ?>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên vai trò (nhóm quyền)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($permissions)): ?>
                            <?php foreach ($permissions as $permission): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($permission['id']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($permission['ten_vai_tro']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                        <a href="admin.php?page=decentralization&action=detail&id=<?php echo $permission['id']; ?>"
                                            class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye mr-1"></i> Xem
                                        </a>
                                        <?php renderButton('Sửa' , 'admin.php?page=decentralization&action=update&id='.$permission['id'].'' , 'fas fa-edit mr-1', 'text-yellow-600 hover:text-yellow-900', !$hasPermissionEditPermission) ?>
                                        
                                        <?= ($hasPermissionDeletePermission) ? '<a href="admin.php?page=decentralization&action=delete&id='.$permission['id'].'" 
                                        class="text-red-600 hover:text-red-900" onclick="return confirm(\'Bạn chắc chắn muốn xóa nhóm quyền này?\')">
                                            <i class="fas fa-trash-alt mr-1"></i> Xóa
                                        </a>' : '<a 
                                        class="text-red-600 hover:text-red-900 cursor-not-allowed" >
                                            <i class="fas fa-trash-alt mr-1"></i> Xóa
                                        </a>' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Không có nhóm quyền nào.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
<?php if (!$hasPermissionViewPermission): ?>
    </div>
    <div style="margin-top: 15px; color: red; font-weight: bold;">
        Bạn không được quyền xem nội dung này.
    </div>
<?php endif; ?>