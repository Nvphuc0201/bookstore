<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu chưa đăng nhập → chuyển về login
if (!isset($_SESSION['user'])) {
    header("Location: /bookstore/index.php?controller=auth&action=login");
    exit();
}
