<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Đảm bảo luôn có kết nối DB cho header
if (!isset($conn) || !($conn instanceof mysqli)) {
    @require_once __DIR__ . '/../../config/db.php';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore - Thế giới tri thức</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #e67e22;
            --text-color: #333;
            --bg-light: #f8f9fa;
        }
        body { font-family: 'Inter', sans-serif; margin: 0; color: var(--text-color); background: var(--bg-light); }
        
        /* Header Container */
        .main-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        /* Logo */
        .logo a {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-color);
            text-decoration: none;
            letter-spacing: -1px;
        }
        .logo span { color: var(--accent-color); }

        /* Search Bar */
        .search-box {
            flex: 0 1 400px;
            display: flex;
            background: var(--bg-light);
            border-radius: 20px;
            padding: 5px 15px;
            border: 1px solid transparent;
        }
        .search-box:focus-within { border-color: var(--accent-color); }
        .search-box input {
            border: none; background: none; outline: none; padding: 8px; width: 100%;
        }
        .search-box button {
            border: none; background: none; color: #888; cursor: pointer;
        }

        /* Navigation */
        .nav-menu { display: flex; gap: 20px; align-items: center; }
        .nav-menu a {
            text-decoration: none; color: var(--text-color); font-weight: 500; font-size: 14px; transition: 0.3s;
        }
        .nav-menu a:hover { color: var(--accent-color); }

        /* Icons & Utilities */
        .user-utilities { display: flex; gap: 15px; align-items: center; }
        .icon-link {
            position: relative; color: var(--primary-color); font-size: 18px; text-decoration: none; cursor: pointer;
        }
        .badge-cart {
            position: absolute; top: -8px; right: -10px;
            background: var(--accent-color); color: #fff;
            font-size: 10px; padding: 2px 5px; border-radius: 50%;
        }
    </style>
</head>
<body>
<header class="main-header">
    <div class="header-inner">
        <div class="logo">
            <a href="index.php">BOOK<span>STORE</span></a>
        </div>

        <form action="index.php" method="GET" class="search-box">
            <input type="hidden" name="controller" value="sanpham">
            <input type="hidden" name="action" value="search">
            <input type="text" name="keyword" placeholder="Tìm kiếm sách, tác giả...">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <nav class="nav-menu">
            <a href="index.php">Trang chủ</a>
            <a href="index.php?controller=sanpham&action=list">Cửa hàng</a>
            
            <div class="user-utilities">
                <?php
                // Đếm SỐ DÒNG SẢN PHẨM trong giỏ (không phải tổng SL),
                // ví dụ: 2 sản phẩm khác nhau => icon hiển thị số 2
                $cart_count = 0;
                if (isset($_SESSION['user'])) {
                    global $conn;
                    if ($conn instanceof mysqli) {
                        $maKH = (int)$_SESSION['user']['MaKH'];
                        $sqlCart = "
                            SELECT COALESCE(COUNT(ct.MaSP), 0) AS total
                            FROM giohang gh
                            LEFT JOIN chitietgiohang ct ON gh.MaGH = ct.MaGH
                            WHERE gh.MaKH = ?
                        ";
                        if ($stmtCart = $conn->prepare($sqlCart)) {
                            $stmtCart->bind_param('i', $maKH);
                            $stmtCart->execute();
                            $resultCart = $stmtCart->get_result();
                            if ($rowCart = $resultCart->fetch_assoc()) {
                                $cart_count = (int)$rowCart['total'];
                            }
                            $stmtCart->close();
                        }
                    }
                }
                ?>

                <a href="index.php?controller=cart" class="icon-link">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php if (isset($_SESSION['user'])): ?>
                        <span class="badge-cart"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>

                <?php if(isset($_SESSION['user'])): ?>
                    <a href="index.php?controller=customer" class="icon-link" title="Tài khoản của tôi">
                        <i class="fa-regular fa-user"></i>
                    </a>
                    <a href="index.php?controller=auth&action=logout" id="logoutBtn" class="btn btn-sm btn-outline-danger" style="padding:6px 10px; font-size:13px;" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">Đăng xuất</a>
                <?php else: ?>
                    <a href="index.php?controller=auth&action=login" class="btn btn-sm btn-outline-primary" style="padding:6px 10px; font-size:13px;">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('logoutBtn');
    if (!btn) return;
    btn.addEventListener('click', function(e){
        if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
            window.location.href = 'index.php?controller=auth&action=logout';
        }
    });
});
</script>

<main style="padding-bottom: 50px;">