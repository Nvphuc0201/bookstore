<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/SanPham.php";
require_once "../app/config/db.php";

$model = new SanPham();

if (!isset($_GET['id'])) {
    die("Thiếu ID sản phẩm");
}

$maSP = $_GET['id'];


// ✅ 1. LẤY TOÀN BỘ ẢNH TRONG DB
$listImages = $model->getAllImagesByProduct($maSP);


// ✅ 2. XOÁ ẢNH TRONG Ổ CỨNG
while ($img = $listImages->fetch_assoc()) {
    $path = "../assets/images/products/" . $img['DuongDan'];
    if (file_exists($path)) {
        unlink($path); // ✅ XOÁ FILE ẢNH
    }
}


// ✅ 3. XOÁ SẢN PHẨM (TỰ ĐỘNG XOÁ HinhAnhSanPham NHỜ CASCADE)
$model->deleteProduct($maSP);


// ✅ 4. QUAY VỀ TRANG DANH SÁCH
header("Location: sanpham.php");
exit;
