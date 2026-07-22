<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/DanhMuc.php";
$model = new DanhMuc();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: danhmuc.php");
    exit;
}
$data = $model->getById($id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $mota = $_POST['mota'];

    $model->update($id, $ten, $mota);
    header("Location: danhmuc.php");
}
?>

<h2>SỬA DANH MỤC</h2>

<form method="post">
    Tên danh mục: 
    <input type="text" name="ten" value="<?= $data['TenDM'] ?>" required><br><br>

    Mô tả:
    <textarea name="mota"><?= $data['MoTa'] ?></textarea><br><br>

    <button type="submit">Cập nhật</button>
</form>
