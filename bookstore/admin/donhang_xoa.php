<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/DonHang.php";

if (!isset($_GET['id'])) {
    header("Location: donhang.php");
    exit;
}

$model = new DonHang();
$id = (int)$_GET['id'];

$model->delete($id);

header("Location: donhang.php");
exit;
