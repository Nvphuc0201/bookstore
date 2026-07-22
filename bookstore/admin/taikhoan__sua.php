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

if (!$tk) die("Không tìm thấy tài khoản");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $status = $_POST['status'];

    if ($model->update($tk['MaTK'], $role, $status)) {
        header("Location: taikhoan.php");
        exit;
    }
}
?>

<h2>SỬA TÀI KHOẢN</h2>

<form method="post">
    Tên đăng nhập: <b><?= $tk['TenDangNhap'] ?></b> <br><br>

    Vai trò:
    <select name="role">
        <option <?= $tk['VaiTro']=='QuanLy'?'selected':'' ?> value="QuanLy">Quản lý</option>
        <option <?= $tk['VaiTro']=='NhanVien'?'selected':'' ?> value="NhanVien">Nhân viên</option>
        <option <?= $tk['VaiTro']=='KhachHang'?'selected':'' ?> value="KhachHang">Khách hàng</option>
    </select><br><br>

    Trạng thái:
    <select name="status">
        <option <?= $tk['TrangThai']==1?'selected':'' ?> value="1">Hoạt động</option>
        <option <?= $tk['TrangThai']==0?'selected':'' ?> value="0">Khoá</option>
    </select><br><br>

    <button type="submit">Lưu</button>
</form>
