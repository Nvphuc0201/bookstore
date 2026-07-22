<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập và quyền admin/quản lý
if (!isset($_SESSION['user'])) {
    header("Location: /bookstore/index.php?page=auth&action=login");
    exit();
}

$vaiTro = strtolower(trim($_SESSION['user']['VaiTro'] ?? ''));
if ($vaiTro !== 'quanly' && $vaiTro !== 'admin') {
    header("Location: /bookstore");
    exit();
}
