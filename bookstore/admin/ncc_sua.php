<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/NhaCungCap.php";

$model = new NhaCungCap();

if (!isset($_GET['id'])) {
    header("Location: ncc.php");
    exit;
}

$id = (int)$_GET['id'];
$item = $model->getById($id);
if (!$item) {
    die("Không tìm thấy nhà cung cấp");
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ten'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $diachi = trim($_POST['diachi'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($ten === '') $errors[] = "Tên nhà cung cấp không được để trống.";

    if (empty($errors)) {
        $ok = $model->update($id, $ten, $sdt, $diachi, $email);
        if ($ok) {
            header("Location: ncc.php");
            exit;
        } else {
            $errors[] = "Cập nhật thất bại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Nhà cung cấp</title>
</head>
<body>
<h2>SỬA NHÀ CUNG CẤP</h2>

<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
    </div>
<?php endif; ?>

<form method="post">
    Tên NCC:<br>
    <input type="text" name="ten" value="<?= htmlspecialchars($_POST['ten'] ?? $item['TenNCC']) ?>" required><br><br>

    SĐT:<br>
    <input type="text" name="sdt" value="<?= htmlspecialchars($_POST['sdt'] ?? $item['SDT']) ?>"><br><br>

    Địa chỉ:<br>
    <input type="text" name="diachi" value="<?= htmlspecialchars($_POST['diachi'] ?? $item['DiaChi']) ?>"><br><br>

    Email:<br>
    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $item['Email']) ?>"><br><br>

    <button type="submit">Lưu</button>
    <a href="ncc.php">Hủy</a>
</form>

</body>
</html>
