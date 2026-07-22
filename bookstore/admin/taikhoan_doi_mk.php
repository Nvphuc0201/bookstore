<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/TaiKhoan.php";

$model = new TaiKhoan();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: taikhoan.php");
    exit;
}
$tk = $model->getById($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $_POST['password'];
    $model->updatePassword($tk['MaTK'], $new);
    header("Location: taikhoan.php");
    exit;
}
?>

<h2>ĐỔI MẬT KHẨU</h2>

<form method="post">
    Mật khẩu mới: <input type="text" name="password" required><br><br>
    <button type="submit">Cập nhật</button>
</form>
