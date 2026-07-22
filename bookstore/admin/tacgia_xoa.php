<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/TacGia.php";
$model = new TacGia();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: tacgia.php");
    exit;
}
$model->delete($id);

header("Location: tacgia.php");
