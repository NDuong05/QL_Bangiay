<?php if (!$hasUserViewPermission): ?>
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
<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Danh sách người dùng</h2>
            <!-- <a href="admin.php?page=users&action=add"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Thêm người dùng
            </a> -->
            <?php renderButton('Thêm người dùng' , 'admin.php?page=users&action=add' , 'fas fa-plus mr-2' , 'inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700', !$hasUserAddPermission ) ?>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $user['UserID'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($user['Fullname']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($user['Email']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($user['isActivate']): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Hoạt động
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Khóa
                                    </span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                
                                <?php renderButton('Sửa' , 'admin.php?page=users&action=edit&id='.$user['UserID'].'' , 'fas fa-edit mr-1' ,'text-yellow-600 hover:text-yellow-900', !$hasUserEditPermission ) ?>
                                
                                <?= ($hasUserDeletePermission) ? '<a href="admin.php?page=users&action=delete&id='.$user['UserID'].'" 
                                        class="text-red-600 hover:text-red-900" onclick="return confirm(\'Bạn chắc chắn muốn xóa người dùng này?\')">
                                            <i class="fas fa-trash-alt mr-1"></i> Xóa
                                        </a>' : '<a 
                                        class="text-red-600 hover:text-red-900 cursor-not-allowed" >
                                            <i class="fas fa-trash-alt mr-1"></i> Xóa
                                        </a>' ?>
                                                                    </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if (!$hasUserViewPermission): ?>
    </div>
    <div style="margin-top: 15px; color: red; font-weight: bold;">
        Bạn không được quyền xem nội dung này.
    </div>
<?php endif; ?>