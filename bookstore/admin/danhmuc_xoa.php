<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/DanhMuc.php";
$model = new DanhMuc();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: danhmuc.php");
    exit;
}
$model->delete($id);

header("Location: danhmuc.php");
