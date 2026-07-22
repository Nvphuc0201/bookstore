<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/TacGia.php";
$model = new TacGia();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: tacgia.php");
    exit;
}
$data = $model->getById($id);

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

    $model->update($id, $ten, $ngaysinh, $quoctich, $mota, $anh);
    header("Location: tacgia.php");
}
?>

<h2>SỬA TÁC GIẢ</h2>

<form method="post" enctype="multipart/form-data">
    Tên: <input type="text" name="ten" value="<?= $data['TenTacGia'] ?>" required><br><br>
    Ngày sinh: <input type="date" name="ngaysinh" value="<?= $data['NgaySinh'] ?>"><br><br>
    Quốc tịch: <input type="text" name="quoctich" value="<?= $data['QuocTich'] ?>"><br><br>
    Mô tả: <textarea name="mota"><?= $data['MoTa'] ?></textarea><br><br>

    <?php if ($data['AnhDaiDien']): ?>
        <img src="../assets/images/tacgia/<?= $data['AnhDaiDien'] ?>" width="80"><br>
    <?php endif; ?>

    Ảnh mới: <input type="file" name="anh"><br><br>
    <button type="submit">Cập nhật</button>
</form>
