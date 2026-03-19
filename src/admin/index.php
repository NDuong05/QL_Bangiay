<?php
session_start();

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION['user'])) {
    // Nếu là admin, chuyển hướng đến admin.php
    if ($_SESSION['user']['is_admin'] == 1) {
        header('Location: admin.php');
        exit;
    } else {
        // Nếu không phải admin, hiển thị thông báo
        echo "Bạn không có quyền truy cập trang quản trị.";
    }
} else {
    // Chưa đăng nhập, chuyển hướng đến login.php
    header('Location: login.php');
    exit;
}
?>