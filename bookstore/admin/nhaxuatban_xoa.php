<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/NhaXuatBan.php";
$model = new NhaXuatBan();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: nhaxuatban.php");
    exit;
}
$model->delete($id);

header("Location: nhaxuatban.php");
