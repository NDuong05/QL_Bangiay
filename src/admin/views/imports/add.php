<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Thêm phiếu nhập hàng</h2>
            <a href="admin.php?page=imports&action=list"
                class="text-blue-600 hover:text-blue-800 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <form method="POST" action="admin.php?page=imports&action=add" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- ID Nhân viên -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID Nhân viên <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="EmployeeID" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none">
                            <option value="">-- Chọn nhân viên --</option>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?= $employee['EmployeeID'] ?>">
                                    <?= htmlspecialchars($employee['Fullname']) ?> (ID: <?= $employee['EmployeeID'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <!-- Chi tiết sản phẩm nhập -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Chi tiết sản phẩm</label>

                    <div id="product-list" class="space-y-4">
                        <div class="product-item bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-12 gap-3">
                                <div class="col-span-3">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Sản phẩm & Size</label>
                                    <select name="productSizeID[]" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                        <option value="">-- Chọn sản phẩm --</option>
                                        <?php foreach ($productSizes as $ps): ?>
                                            <option value="<?= $ps['ProductSizeID'] ?>" data-product-id="<?= $ps['ProductID'] ?>">
                                                <?= htmlspecialchars($ps['ProductName']) ?> - Size: <?= htmlspecialchars($ps['Size']) ?>
                                            </option>

                                        <?php endforeach; ?>
                                    </select>


                                </div>

                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Số lượng</label>
                                    <input type="number" name="quantity[]" min="1" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm product-quantity"
                                        oninput="calculateSubtotal(this)">
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Giá nhập (₫)</label>
                                    <input type="number" name="unitPrice[]" min="1000" step="1000" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm product-price"
                                        oninput="calculateSubtotal(this)">
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Giá bán (₫)</label>
                                    <input type="number" name="sellprice[]" min="1000" step="1000" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                </div>

                                <div class="col-span-1 flex items-end">
                                    <button type="button" class="remove-product text-red-500 hover:text-red-700 px-1 py-1"
                                        onclick="removeProduct(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-2 text-right">
                                <span class="text-xs text-gray-500">Thành tiền:</span>
                                <span class="text-sm font-medium text-blue-600 subtotal-display">0 ₫</span>
                                <span class="hidden subtotal-value">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" id="add-product" onclick="addproduct()"
                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-plus mr-1"></i> Thêm sản phẩm
                        </button>
                    </div>

                    <div class="mt-4 pt-3 border-t flex justify-between">
                        <span class="font-medium text-gray-700">Tổng giá trị nhập:</span>
                        <span class="font-bold text-lg text-blue-600" id="total-amount">0 ₫</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="reset"
                    class="mr-3 px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-redo mr-2"></i> Nhập lại
                </button>
                <button type="submit"
                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus-circle mr-2"></i> Thêm phiếu nhập
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Thêm sản phẩm mới
        document.getElementById('add-product').addEventListener('click', function() {
            const container = document.getElementById('product-list');
            const template = container.querySelector('.product-item').cloneNode(true);

            // Reset các giá trị
            template.querySelectorAll('input').forEach(input => input.value = '');
            template.querySelector('select').selectedIndex = 0;
            template.querySelector('.subtotal-display').textContent = '0 ₫';
            template.querySelector('.subtotal-value').textContent = '0';

            container.appendChild(template);
        });
    });
    //lấy giá bán

    const select = document.getElementById('productSizeSelect');
    const hiddenInput = document.getElementById('productIDsJson');

    select.addEventListener('change', function() {
        const selectedOptions = Array.from(select.selectedOptions);
        const productIDs = selectedOptions.map(option => option.dataset.productId);
        hiddenInput.value = JSON.stringify(productIDs); 
    });

    select.dispatchEvent(new Event('change'));

    // Xóa dòng sản phẩm
    function removeProduct(button) {
        const items = document.querySelectorAll('.product-item');
        if (items.length > 1) {
            const item = button.closest('.product-item');
            item.remove();
            calculateTotal();
        }
    }

    // Tính thành tiền cho từng sản phẩm
    function calculateSubtotal(input) {
        const item = input.closest('.product-item');
        const quantity = parseInt(item.querySelector('.product-quantity').value) || 0;
        const price = parseInt(item.querySelector('.product-price').value) || 0;
        const subtotal = quantity * price;

        item.querySelector('.subtotal-display').textContent = formatCurrency(subtotal) + ' ₫';
        item.querySelector('.subtotal-value').textContent = subtotal;

        calculateTotal();
    }

    // Tính tổng giá trị
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-value').forEach(span => {
            total += parseInt(span.textContent) || 0;
        });

        document.getElementById('total-amount').textContent = formatCurrency(total) + ' ₫';
    }

    // Định dạng tiền tệ
    function formatCurrency(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>