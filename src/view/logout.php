<?php
session_start(); // Bắt đầu session nếu chưa có
session_unset(); // Xóa toàn bộ biến session
session_destroy(); // Hủy session hoàn toàn

// Xóa cookie phiên (nếu có sử dụng session cookie)
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Chuyển hướng về index.php
header("Location: ../index.php?pg=home");
exit();
?>
