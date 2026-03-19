<?php if (!$hasProductViewPermission): ?>
    <div style="filter: blur(3px); pointer-events: none;">
    <?php endif; ?>
    <?php
    function renderButton($text, $href = '#', $iconClass = '', $itemclass = '', $disabled = false, $dltBtn = false)
    {
        // Xử lý class
        if ($disabled) {
            $itemclass .= 'cursor-not-allowed';
        }

        // Xử lý href
        $hrefAttribute = $disabled ? '' : 'href="' . htmlspecialchars($href) . '"';

        // Xử lý onclick xác nhận xóa
        $deleteBtn = '';
        if ($dltBtn) {
            $deleteBtn = 'onclick="return confirm(\'Bạn có chắc chắn muốn xóa sản phẩm này?\')"';
        }

        // In ra nút
        echo '<a ' . $hrefAttribute . ' class="' . trim($itemclass) . '" ' . $deleteBtn . '>';
        if (!empty($iconClass)) {
            echo '<i class="' . htmlspecialchars($iconClass) . ' mr-2"></i>';
        }
        echo htmlspecialchars($text);
        echo '</a>';
    }
    ?>



    <div class="max-w-7xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Danh sách sản phẩm</h2>
                <?php renderButton('Thêm sản phẩm', 'admin.php?page=products&action=add', 'fas fa-plus', 'inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700', !$hasProductAddPermission) ?>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hình ảnh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên sản phẩm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh mục</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thương hiệu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($products as $product): ?>
                            <tr class="hover:bg-gray-50 data-product-row">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $product['ProductID'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-16 w-16 flex-shrink-0">
                                        <?php if (!empty($product['ImageURL'])): ?>
                                            <img src=".<?= htmlspecialchars($product['ImageURL']) ?>"
                                                alt="<?= htmlspecialchars($product['ProductName']) ?>"
                                                class="h-16 w-16 rounded-md object-cover">
                                        <?php else: ?>
                                            <div class="h-16 w-16 rounded-md bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($product['ProductName']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($product['CategoryName']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($product['BrandName']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"><?= number_format($product['Price'], 0, ',', '.') ?> VND</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">

                                    <?php renderButton('Sửa', 'admin.php?page=products&action=edit&id= ' . $product['ProductID'] . '', 'fas fa-edit', 'text-yellow-600 hover:text-yellow-900', !$hasProductEditPermission) ?>

                                    <?= ($hasProductDeletePermission) ? '<a href="admin.php?page=products&action=delete&id=' . $product['ProductID'] . '" 
                                        class="text-red-600 hover:text-red-900" onclick="return confirm(\'Bạn chắc chắn muốn xóa sản phẩm này?\')">
                                            <i class="fas fa-trash-alt mr-1"></i> Xóa
                                        </a>' : '<a 
                                        class="text-red-600 hover:text-red-900 cursor-not-allowed">
                                            <i class="fas fa-trash-alt mr-1"></i> Xóa
                                        </a>' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="100%">
                                <div id="pagination" class="flex justify-center mt-4 space-x-2"></div>
                            </td>
                        </tr>
                    </tfoot>


                </table>
            </div>
        </div>
    </div>
    <?php if (!$hasProductViewPermission): ?>
    </div>
    <div style="margin-top: 15px; color: red; font-weight: bold;">
        Bạn không được quyền xem nội dung này.
    </div>
<?php endif; ?>