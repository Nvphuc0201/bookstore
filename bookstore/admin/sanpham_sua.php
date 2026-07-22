<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/SanPham.php";
require_once "../app/config/db.php";

$model = new SanPham();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: sanpham.php");
    exit;
}

$sp = $model->getById($id);
$images = $model->getImages($id);

$danhMuc = $conn->query("SELECT * FROM DanhMuc");
$nxb = $conn->query("SELECT * FROM NhaXuatBan");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ten = $_POST['ten'];
    $gia = $_POST['gia'];
    $mota = $_POST['mota'];
    $madm = $_POST['madm'];
    $manxb = $_POST['manxb'];

    // ✅ UPDATE SẢN PHẨM
    $model->update($id, $ten, $gia, $mota, $madm, $manxb);

    // ✅ XOÁ ẢNH CŨ (NẾU CHỌN)
    if (!empty($_POST['xoa_anh'])) {
        foreach ($_POST['xoa_anh'] as $maHinh) {
            $model->deleteImage($maHinh);
        }
    }

    // ✅ THÊM ẢNH MỚI
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $i => $name) {
            $tmp = $_FILES['images']['tmp_name'][$i];
            $filename = time() . "_" . $name;
            move_uploaded_file($tmp, "../assets/images/products/" . $filename);
            $model->insertImage($id, $filename, 0);
        }
    }

    // ✅ SET LẠI ẢNH CHÍNH
    if (isset($_POST['anh_chinh'])) {
        $model->resetMainImage($id);

        $maHinh = (int)$_POST['anh_chinh'];
        $stmt = $conn->prepare("UPDATE HinhAnhSanPham SET LaAnhChinh=1 WHERE MaHinh=?");
        $stmt->bind_param("i", $maHinh);
        $stmt->execute();

        $stmt2 = $conn->prepare("SELECT DuongDan FROM HinhAnhSanPham WHERE MaHinh=?");
        $stmt2->bind_param("i", $maHinh);
        $stmt2->execute();
        $row = $stmt2->get_result()->fetch_assoc();
        if ($row) {
            $stmt3 = $conn->prepare("UPDATE SanPham SET HinhAnh=? WHERE MaSP=?");
            $stmt3->bind_param("si", $row['DuongDan'], $id);
            $stmt3->execute();
        }
    }

    header("Location: sanpham.php");
    exit;
}
?>

<h2>SỬA SẢN PHẨM</h2>

<form method="post" enctype="multipart/form-data">

Tên:
<input type="text" name="ten" value="<?= htmlspecialchars($sp['TenSP'] ?? '') ?>"><br><br>

Giá:
<input type="number" name="gia" value="<?= htmlspecialchars($sp['DonGia'] ?? '') ?>"><br><br>

Mô tả:
<textarea name="mota"><?= htmlspecialchars($sp['MoTa'] ?? '') ?></textarea><br><br>

Danh mục:
<select name="madm">
<?php while ($dm = $danhMuc->fetch_assoc()): ?>
<option value="<?= htmlspecialchars($dm['MaDM']) ?>" <?= $dm['MaDM']==$sp['MaDM']?'selected':'' ?>>
    <?= htmlspecialchars($dm['TenDM']) ?>
</option>
<?php endwhile; ?>
</select><br><br>

NXB:
<select name="manxb">
<?php while ($x = $nxb->fetch_assoc()): ?>
<option value="<?= htmlspecialchars($x['MaNXB']) ?>" <?= $x['MaNXB']==$sp['MaNXB']?'selected':'' ?>>
    <?= htmlspecialchars($x['TenNXB']) ?>
</option>
<?php endwhile; ?>
</select><br><br>

<h3>ẢNH HIỆN TẠI</h3>
<?php while ($img = $images->fetch_assoc()): ?>
    <img src="../assets/images/products/<?= htmlspecialchars($img['DuongDan']) ?>" width="90">
    <br>
    Xoá <input type="checkbox" name="xoa_anh[]" value="<?= htmlspecialchars($img['MaHinh']) ?>">
    Ảnh chính <input type="radio" name="anh_chinh" value="<?= htmlspecialchars($img['MaHinh']) ?>" <?= $img['LaAnhChinh']==1?'checked':'' ?>>
    <hr>
<?php endwhile; ?>

<h3>THÊM ẢNH MỚI</h3>
<input type="file" name="images[]" multiple>

<br><br>
<button type="submit">LƯU CẬP NHẬT</button>
</form>
