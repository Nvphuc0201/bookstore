<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/TaiKhoan.php";

$model = new TaiKhoan();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    if ($model->insert($username, $pass, $role, $status)) {
        header("Location: taikhoan.php");
        exit;
    }
}
?>

<h2>THÊM TÀI KHOẢN</h2>
<form method="post">
    Tên đăng nhập: <input type="text" name="username" required><br><br>
    Mật khẩu: <input type="text" name="password" required><br><br>

    Vai trò:
    <select name="role">
        <option value="QuanLy">Quản lý</option>
        <option value="NhanVien">Nhân viên</option>
        <option value="KhachHang">Khách hàng</option>
    </select><br><br>

    Trạng thái:
    <select name="status">
        <option value="1">Hoạt động</option>
        <option value="0">Khoá</option>
    </select><br><br>

    <button type="submit">Lưu</button>
</form>
