<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Thêm sản phẩm mới</h2>
            <a href="admin.php?page=products&action=list"
                class="text-blue-600 hover:text-blue-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <strong>Lỗi!</strong> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin.php?page=products&action=add" enctype="multipart/form-data" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cột 1 -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm <span class="text-red-500">*</span></label>
                        <input type="text" name="ProductName" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="CategoryID" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none">
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['CategoryID'] ?>">
                                        <?= htmlspecialchars($category['CategoryName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thương hiệu <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="BrandID" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none">
                                <option value="">-- Chọn thương hiệu --</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['BrandID'] ?>">
                                        <?= htmlspecialchars($brand['BrandName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>

                    </div>
                    <!-- Size -->
                    <!-- <p class="mt-1"></p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Size <span class="text-red-500">*</span></label>
                        <input type="text" name="Size" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Nhập size (ví dụ: 40, 41...)">
                    </div> -->
                </div>

                <!-- Cột 2 -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="Gender" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                                <option value="Unisex">Unisex</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá <span class="text-red-500">*</span></label>
                        <input type="number" name="Price" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh sản phẩm <span class="text-red-500">*</span></label>
                        <div class="mt-1 flex items-center">
                            <div class="w-full">
                                <input type="file" name="product_image" id="product_image" accept="image/*" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <p class="mt-1 text-xs text-gray-500">
                                    Hình ảnh sẽ được lưu tại: ../view/layout/asset/img/catalogue/
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Stock Quantity -->
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng tồn kho <span class="text-red-500">*</span></label>
                        <input type="number" name="StockQuantity" required min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Nhập số lượng">
                    </div> -->
                </div>
                <!-- Xem trước ảnh -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Xem trước:</label>
                    <div class="mt-1 w-40 h-40 border border-gray-300 rounded-md overflow-hidden bg-gray-100 flex items-center justify-center">
                        <img id="preview_image" src="#" alt="Xem trước ảnh" class="max-h-full max-w-full hidden">
                        <span id="preview_placeholder" class="text-gray-400 text-sm">Chưa có ảnh</span>
                    </div>
                </div>


            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Size và số lượng tồn kho</label>
                <div id="size-stock-container" class="space-y-2">
                    <!-- Các dòng size/stock sẽ xuất hiện ở đây -->
                </div>
                <button type="button" onclick="addSizeStockRow2()"
                    class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                    + Thêm Size
                </button>
            </div>
            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="reset"
                    class="mr-3 px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-redo mr-2"></i> Nhập lại
                </button>
                <button type="submit"
                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i> Thêm sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Xem trước ảnh sau khi chọn
    document.getElementById('product_image').addEventListener('change', function(event) {
        const preview = document.getElementById('preview_image');
        const placeholder = document.getElementById('preview_placeholder');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    });
</script>