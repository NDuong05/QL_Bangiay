<!DOCTYPE html>
<html>

<head>
    <title>Sửa nhà cung cấp</title>
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
                <h2 class="text-2xl font-bold text-gray-800">
                    Sửa nhà cung cấp #<?php echo htmlspecialchars($supplier['SupplierID']); ?>
                </h2>
                <a href="admin.php?page=suppliers&action=list"
                    class="text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
                </a>
            </div>

            <?php if ($supplier): ?>
                <form method="POST" action="admin.php?page=suppliers&action=update" class="space-y-6">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($supplier['SupplierID']); ?>">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tên nhà cung cấp <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name"
                                value="<?php echo htmlspecialchars($supplier['SupplierName']); ?>"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-200">
                        <button type="reset"
                            class="mr-3 px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-redo mr-2"></i> Nhập lại
                        </button>
                        <button type="submit"
                            class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                Không tìm thấy nhà cung cấp.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>