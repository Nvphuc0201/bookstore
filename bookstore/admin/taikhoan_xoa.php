<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/TaiKhoan.php";

$model = new TaiKhoan();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $model->delete($id);
    }
}

header("Location: taikhoan.php");
exit;
