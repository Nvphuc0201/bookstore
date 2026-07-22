<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/NhaXuatBan.php";
$model = new NhaXuatBan();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: nhaxuatban.php");
    exit;
}
$data = $model->getById($id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $diachi = $_POST['diachi'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];

    $model->update($id, $ten, $diachi, $sdt, $email);
    header("Location: nhaxuatban.php");
}
?>

<h2>SỬA NHÀ XUẤT BẢN</h2>

<form method="post">
    Tên NXB: <input type="text" name="ten" value="<?= $data['TenNXB'] ?>" required><br><br>
    Địa chỉ: <input type="text" name="diachi" value="<?= $data['DiaChi'] ?>"><br><br>
    SĐT: <input type="text" name="sdt" value="<?= $data['SDT'] ?>"><br><br>
    Email: <input type="email" name="email" value="<?= $data['Email'] ?>"><br><br>
    <button type="submit">Cập nhật</button>
</form>
