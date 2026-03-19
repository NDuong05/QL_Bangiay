<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-200">Chi tiết tài khoản</h2>
    
    <form method="POST" action="admin.php?page=accounts&action=update" class="bg-white rounded-lg shadow-md p-6">
        <input type="hidden" name="id" value="<?= $account['id'] ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cột 1 -->
            <div class="space-y-4">
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                    <input type="text" name="username" value="<?= $account['username'] ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                    <input type="text" name="full_name" value="<?= $account['full_name'] ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="text" name="phone" value="<?= $account['phone'] ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
            </div>
            
            <!-- Cột 2 -->
            <div class="space-y-4">
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?= $account['email'] ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <input type="text" name="address" value="<?= $account['address'] ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="1" <?= $account['status'] ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= !$account['status'] ? 'selected' : '' ?>>Khóa</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="mt-8 flex justify-end space-x-3">
            <a href="admin.php?page=accounts&action=list" 
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                Quay lại
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                <i class="fas fa-save mr-2"></i>Lưu thay đổi
            </button>
        </div>
    </form>
</div>