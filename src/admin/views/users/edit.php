<div class="max-w-5xl mx-auto p-6">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white">Chỉnh sửa người dùng</h2>
                <p class="text-blue-100 text-sm mt-1">Cập nhật thông tin chi tiết cho tài khoản: <span class="font-semibold text-white"><?= htmlspecialchars($user['Username']) ?></span></p>
            </div>
            <a href="admin.php?page=users&action=list"
                class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg backdrop-blur-md transition flex items-center text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <form method="POST" action="admin.php?page=users&action=edit&id=<?= $user['UserID'] ?>" class="p-8 space-y-8">
            <input type="hidden" name="UserID" value="<?= $user['UserID'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="space-y-5">
                    <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3 text-sm">
                            <i class="fas fa-user"></i>
                        </span>
                        Thông tin cá nhân
                    </h3>
                    <hr class="border-gray-100">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Họ tên <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <input type="text" name="Fullname" value="<?= htmlspecialchars($user['Fullname']) ?>" required
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <input type="text" name="PhoneNumber" value="<?= htmlspecialchars($user['PhoneNumber']) ?>" required
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input type="email" name="Email" value="<?= htmlspecialchars($user['Email']) ?>" required
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                            <select name="isActivate" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none">
                                <option value="1" <?= $user['isActivate'] == 1 ? 'selected' : '' ?>>🟢 Hoạt động</option>
                                <option value="0" <?= $user['isActivate'] == 0 ? 'selected' : '' ?>>🔴 Khóa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                        <span class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mr-3 text-sm">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        Địa chỉ cư trú
                    </h3>
                    <hr class="border-gray-100">

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Tỉnh/Thành <span class="text-red-500">*</span></label>
                                <select name="ProvinceID" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                    <?php if(!empty($provinces)) foreach($provinces as $p): ?>
                                        <option value="<?= $p['province_id'] ?>" <?= $user['ProvinceID'] == $p['province_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Quận/Huyện <span class="text-red-500">*</span></label>
                                <select name="DistrictID" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                    <?php if(!empty($districts)) foreach($districts as $d): ?>
                                        <option value="<?= $d['district_id'] ?>" <?= $user['DistrictID'] == $d['district_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($d['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Phường/Xã <span class="text-red-500">*</span></label>
                            <select name="WardID" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                <?php if(!empty($wards)) foreach($wards as $w): ?>
                                    <option value="<?= $w['wards_id'] ?>" <?= $user['WardID'] == $w['wards_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($w['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Địa chỉ cụ thể <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-home"></i>
                                </div>
                                <input type="text" name="Address" value="<?= htmlspecialchars($user['Address']) ?>" required
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-8 border-t border-gray-100 space-x-4">
                <button type="reset"
                    class="px-8 py-3 bg-gray-100 text-gray-600 font-semibold rounded-xl hover:bg-gray-200 transition-all flex items-center">
                    <i class="fas fa-undo mr-2"></i> Khôi phục
                </button>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-green-200 hover:shadow-green-300 hover:-translate-y-0.5 transition-all flex items-center">
                    <i class="fas fa-save mr-2"></i> Cập nhật ngay
                </button>
            </div>
        </form>
    </div>
</div>