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
$data = $kh->getById($id);

if (!$data) die("Không tìm thấy khách hàng");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kh->update($id, $_POST["HoTen"], $_POST["Email"], $_POST["SDT"], $_POST["DiaChi"]);
    header("Location: khachhang_danhsach.php");
}
?>

<h2>SỬA KHÁCH HÀNG</h2>

<form method="POST">
    Họ tên: <input type="text" name="HoTen" value="<?= $data['HoTen'] ?>" required><br><br>
    Email: <input type="email" name="Email" value="<?= $data['Email'] ?>"><br><br>
    SDT: <input type="text" name="SDT" value="<?= $data['SDT'] ?>"><br><br>
    Địa chỉ: <input type="text" name="DiaChi" value="<?= $data['DiaChi'] ?>"><br><br>

    <button type="submit">Cập nhật</button>
</form>
