<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/NhaXuatBan.php";
$model = new NhaXuatBan();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $diachi = $_POST['diachi'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];

    $model->insert($ten, $diachi, $sdt, $email);
    header("Location: nhaxuatban.php");
}
?>

<h2>THÊM NHÀ XUẤT BẢN</h2>

<form method="post">
    Tên NXB: <input type="text" name="ten" required><br><br>
    Địa chỉ: <input type="text" name="diachi"><br><br>
    SĐT: <input type="text" name="sdt"><br><br>
    Email: <input type="email" name="email"><br><br>
    <button type="submit">Lưu</button>
</form>
