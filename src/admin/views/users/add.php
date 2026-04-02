<div class="max-w-5xl mx-auto p-6">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white">Thêm người dùng mới</h2>
                <p class="text-blue-100 text-sm mt-1">Vui lòng điền đầy đủ thông tin chi tiết cho tài khoản mới.</p>
            </div>
            <a href="admin.php?page=users&action=list"
                class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg backdrop-blur-md transition flex items-center text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <form method="POST" action="admin.php?page=users&action=add" class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="space-y-5">
                    <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3 text-sm">
                            <i class="fas fa-user"></i>
                        </span>
                        Thông tin cơ bản
                    </h3>
                    <hr class="border-gray-100">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Họ tên khách hàng <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <input type="text" name="Fullname" required placeholder="Ví dụ: Nguyễn Văn A"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <input type="text" name="PhoneNumber" required placeholder="09xx xxx xxx"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Email liên hệ <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input type="email" name="Email" required placeholder="nva@example.com"
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Trạng thái tài khoản <span class="text-red-500">*</span></label>
                            <select name="isActivate" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                                <option value="1">🟢 Đang hoạt động</option>
                                <option value="0">🔴 Đang bị khóa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                        <span class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mr-3 text-sm">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        Địa chỉ & Đăng nhập
                    </h3>
                    <hr class="border-gray-100">

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Tỉnh/Thành <span class="text-red-500">*</span></label>
                                <select name="ProvinceID" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="">Chọn Tỉnh...</option>
                                    <?php if(!empty($provinces)) foreach($provinces as $p): ?>
                                        <option value="<?= $p['province_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Quận/Huyện <span class="text-red-500">*</span></label>
                                <select name="DistrictID" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="">Chọn Huyện...</option>
                                    <?php if(!empty($districts)) foreach($districts as $d): ?>
                                        <option value="<?= $d['district_id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Phường/Xã <span class="text-red-500">*</span></label>
                                <select name="WardID" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                                    <option value="">Chọn Xã...</option>
                                    <?php if(!empty($wards)) foreach($wards as $w): ?>
                                        <option value="<?= $w['wards_id'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Địa chỉ (Số nhà, tên đường) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <input type="text" name="Address" required placeholder="Ví dụ: 123 Đường ABC..."
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Tên đăng nhập <span class="text-red-500">*</span></label>
                                <input type="text" name="Username" required placeholder="user123"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                                <input type="password" name="Password" required placeholder="••••••••"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-8 border-t border-gray-100 space-x-4">
                <button type="reset"
                    class="px-8 py-3 bg-gray-100 text-gray-600 font-semibold rounded-xl hover:bg-gray-200 transition-all flex items-center">
                    <i class="fas fa-undo mr-2"></i> Làm mới
                </button>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-200 hover:shadow-blue-300 hover:-translate-y-0.5 transition-all flex items-center">
                    <i class="fas fa-save mr-2"></i> Xác nhận thêm
                </button>
            </div>
        </form>
    </div>
</div>