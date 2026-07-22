<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/KhachHang.php";

$kh = new KhachHang($conn);

// lấy danh sách tài khoản có VaiTro = KhachHang
$taikhoan = $conn->query("SELECT MaTK, TenDangNhap FROM TaiKhoan WHERE VaiTro='KhachHang'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kh->insert($_POST["HoTen"], $_POST["Email"], $_POST["SDT"], $_POST["DiaChi"], $_POST["MaTK"]);
    header("Location: khachhang_danhsach.php");
}
?>

<h2>THÊM KHÁCH HÀNG</h2>

<form method="POST">
    Họ tên: <input type="text" name="HoTen" required><br><br>
    Email: <input type="email" name="Email"><br><br>
    SDT: <input type="text" name="SDT"><br><br>
    Địa chỉ: <input type="text" name="DiaChi"><br><br>

    Tài khoản (MaTK):  
    <select name="MaTK">
        <option value="">--Không chọn--</option>
        <?php while ($row = $taikhoan->fetch_assoc()) : ?>
            <option value="<?= $row['MaTK'] ?>">
                <?= $row['TenDangNhap'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>
    <button type="submit">Lưu</button>
</form>
