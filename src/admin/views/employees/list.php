<?php if (!$hasStaffViewPermission): ?>
    <div style="filter: blur(3px); pointer-events: none;">
<?php endif; ?>
<?php
function renderButton($text, $href = '#', $iconClass = '', $itemclass= '',$disabled = false , $dltBtn = false)
{
    // $buttonClasses = 'inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white ';
    $itemclass .= $disabled ? 'bg-gray-400 cursor-not-allowed' : '';
    $hrefAttribute = $disabled ? '' : 'href="' . $href . '"';
    $deleteBtn = $dltBtn ? 'onclick="return confirm("Bạn có chắc chắn muốn xóa người dùng này?")"' : '';
    echo '<a ' . $hrefAttribute . ' class="' . $itemclass . '" '.$deleteBtn.'>';
    if (!empty($iconClass)) {
        echo '<i class="' . $iconClass . ' mr-2"></i>';
    }
    echo $text;
    echo '</a>';
}
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Danh sách nhân viên</h2>
        
        <?php renderButton('Thêm nhân viên','admin.php?page=employees&action=add','fas fa-user-plus mr-2','px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center',!$hasStaffAddPermission) ?>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên nhân viên</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($employees as $employee): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $employee['EmployeeID'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"><?= htmlspecialchars($employee['Fullname']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?= $employee['RoleID'] == 1 ? 'bg-purple-100 text-purple-800' : 
                                   ($employee['RoleID'] == 2 ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') ?>">
                                <?= htmlspecialchars($employee['ten_vai_tro']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($employee['Email']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            
                            <?php renderButton('Sửa','admin.php?page=employees&action=edit&id='.$employee['EmployeeID'].'','fas fa-edit mr-1','text-yellow-600 hover:text-yellow-900 mr-3',!$hasStaffEditPermission) ?>
                            <?= ($hasStaffDeletePermission) ? '<a href="admin.php?page=employees&action=delete&id='.$employee['EmployeeID'].'" 
                                        class="text-red-600 hover:text-red-900" onclick="return confirm(\'Bạn chắc chắn muốn xóa nhân viên này?\')">
                                            <i class="fas fa-trash-alt mr-1"></i> Xóa
                                        </a>' : '<a 
                                        class="text-red-600 hover:text-red-900 cursor-not-allowed" ">
                                            <i class="fas fa-trash-alt mr-1"></i> Xóa
                                        </a>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Phân trang -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Trước</a>
                <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Sau</a>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Hiển thị <span class="font-medium">1</span> đến <span class="font-medium">10</span> của <span class="font-medium"><?= count($employees) ?></span> kết quả
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Trước</span>
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="#" aria-current="page" class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">1</a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">2</a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">3</a>
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Sau</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (!$hasStaffViewPermission): ?>
    </div>
    <div style="margin-top: 15px; color: red; font-weight: bold;">
        Bạn không được quyền xem nội dung này.
    </div>
<?php endif; ?>