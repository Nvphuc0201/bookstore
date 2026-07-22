<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/TacGia.php";
$model = new TacGia();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $ngaysinh = $_POST['ngaysinh'];
    $quoctich = $_POST['quoctich'];
    $mota = $_POST['mota'];

    $anh = "";
    if (!empty($_FILES['anh']['name'])) {
        $anh = time() . "_" . $_FILES['anh']['name'];
        move_uploaded_file(
            $_FILES['anh']['tmp_name'],
            "../assets/images/tacgia/" . $anh
        );
    }

    $model->insert($ten, $ngaysinh, $quoctich, $mota, $anh);
    header("Location: tacgia.php");
}
?>

<h2>THÊM TÁC GIẢ</h2>

<form method="post" enctype="multipart/form-data">
    Tên: <input type="text" name="ten" required><br><br>
    Ngày sinh: <input type="date" name="ngaysinh"><br><br>
    Quốc tịch: <input type="text" name="quoctich"><br><br>
    Mô tả: <textarea name="mota"></textarea><br><br>
    Ảnh đại diện: <input type="file" name="anh"><br><br>
    <button type="submit">Lưu</button>
</form>
