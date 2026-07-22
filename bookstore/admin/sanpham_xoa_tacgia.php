<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/SanPhamTacGia.php";

$model = new SanPhamTacGia();

$maSP = (int)($_GET['sp'] ?? 0);
$maTacGia = (int)($_GET['tg'] ?? 0);
if ($maSP <= 0 || $maTacGia <= 0) {
    header("Location: sanpham.php");
    exit;
}

$model->delete($maSP, $maTacGia);

header("Location: sanpham_gan_tacgia.php?id=" . $maSP);
