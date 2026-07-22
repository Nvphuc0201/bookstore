<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/KhachHang.php";

$kh = new KhachHang($conn);

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: khachhang.php");
    exit;
}
$kh->delete($id);

header("Location: khachhang_danhsach.php");
?>
