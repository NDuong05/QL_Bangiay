<div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">
    <div class="p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Cập nhật thông tin tài khoản</h2>
            <a href="admin.php?page=accounts&action=detail" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>
        
        <form method="POST" action="admin.php?page=accounts&action=update" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Họ tên -->
                <div>
                    <label for="Fullname" class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                    <input type="text" id="Fullname" name="Fullname" value="<?= $_SESSION['user']['Fullname'] ?>" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                </div>
                
                <!-- Email -->
                <div>
                    <label for="Email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="Email" name="Email" value="<?= $_SESSION['user']['Email'] ?>" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                </div>
                
                <!-- Số điện thoại -->
                <div>
                    <label for="PhoneNumber" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="text" id="PhoneNumber" name="PhoneNumber" value="<?= $_SESSION['user']['PhoneNumber'] ?>" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="reset" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-undo-alt mr-2"></i> Nhập lại
                </button>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>