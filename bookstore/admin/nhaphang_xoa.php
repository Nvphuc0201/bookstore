<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/NhapHang.php";

if (!isset($_GET['id'])) {
    header("Location: nhaphang.php");
    exit;
}

$model = new NhapHang();
$id = (int)$_GET['id'];

$model->delete($id);

header("Location: nhaphang.php");
exit;
