<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/DanhMuc.php";
$model = new DanhMuc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $mota = $_POST['mota'];

    $model->insert($ten, $mota);
    header("Location: danhmuc.php");
}
?>

<h2>THÊM DANH MỤC</h2>

<form method="post">
    Tên danh mục: <input type="text" name="ten" required><br><br>
    Mô tả: <textarea name="mota"></textarea><br><br>
    <button type="submit">Lưu</button>
</form>
