<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/SanPham.php";

// Include hàm gửi thông báo (đã sửa lỗi fetch_assoc trên bool)
require_once "../includes/gui_thongbao.php";

$model = new SanPham();

// Lấy danh mục & NXB
$danhMuc = $conn->query("SELECT * FROM DanhMuc ORDER BY TenDM");
$nxb = $conn->query("SELECT * FROM NhaXuatBan ORDER BY TenNXB");

$errors = [];
$success = false;
$maSP = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten     = trim($_POST['ten']);
    $gia     = (float)$_POST['gia'];
    $mota    = trim($_POST['mota']);
    $madm    = (int)$_POST['madm'];
    $manxb   = (int)$_POST['manxb'];
    $anhChinhIndex = (int)$_POST['anh_chinh'];

    if (empty($ten) || $gia <= 0 || empty($_FILES['images']['name'][0])) {
        $errors[] = "Vui lòng điền đầy đủ thông tin bắt buộc!";
    } else {
        // THÊM SẢN PHẨM (chưa có ảnh)
        $maSP = $model->insert($ten, $gia, 0, $mota, "", $madm, $manxb);

        if ($maSP) {
            $anhChinhDaChon = false;

            // UPLOAD NHIỀU ẢNH
            foreach ($_FILES['images']['name'] as $index => $name) {
                if ($_FILES['images']['error'][$index] === 0) {
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $filename = $maSP . "_" . time() . "_" . $index . "." . $ext;
                    $path = "../assets/images/products/" . $filename;

                    if (move_uploaded_file($_FILES['images']['tmp_name'][$index], $path)) {
                        $isMain = ($index == $anhChinhIndex) ? 1 : 0;
                        $model->insertImage($maSP, $filename, $isMain);

                        if ($isMain == 1) {
                            $stmt = $conn->prepare("UPDATE SanPham SET HinhAnh=? WHERE MaSP=?");
                            $stmt->bind_param("si", $filename, $maSP);
                            $stmt->execute();
                            $anhChinhDaChon = true;
                        }
                    }
                }
            }

            // Nếu không chọn ảnh chính → tự động lấy ảnh đầu tiên
            if (!$anhChinhDaChon) {
                $stmt = $conn->prepare("SELECT DuongDan FROM HinhAnhSanPham WHERE MaSP = ? LIMIT 1");
                $stmt->bind_param("i", $maSP);
                $stmt->execute();
                $firstImg = $stmt->get_result()->fetch_assoc();
                if ($firstImg) {
                    $img = $firstImg['DuongDan'];
                    $stmt2 = $conn->prepare("UPDATE SanPham SET HinhAnh=? WHERE MaSP=?");
                    $stmt2->bind_param("si", $img, $maSP);
                    $stmt2->execute();
                }
            }

            $success = true;

            // TỰ ĐỘNG GỬI THÔNG BÁO CHO TẤT CẢ KHÁCH HÀNG (AN TOÀN 100%)
            $link = "chi-tiet-san-pham.php?id=$maSP"; // Thay link thật của bạn
            $tieu_de = "Sách mới vừa về: $ten";
            $noi_dung = "Chúng tôi vừa cập nhật sách mới:\n\n";
            $noi_dung .= "Tên sách: $ten\n";
            $noi_dung .= "Giá: " . number_format($gia) . "₫\n\n";
            $noi_dung .= "Xem ngay: $link";

            // Kiểm tra có khách hàng nào không trước khi gửi
            $check = $conn->query("SELECT 1 FROM KhachHang WHERE TrangThai = 1 LIMIT 1");
            if ($check && $check->num_rows > 0) {
                guiThongBaoTatCa($tieu_de, $noi_dung, 'SanPhamMoi', $link);
            }
            // Nếu không có khách → vẫn thành công, không lỗi!
        } else {
            $errors[] = "Thêm sản phẩm thất bại! Vui lòng thử lại.";
        }
    }
}
?>

<?php include 'sidebar.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-success text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Thêm Sản Phẩm Mới
                    </h3>
                </div>

                <div class="card-body p-5">
                    <?php if ($success): ?>
                        <div class="alert alert-success text-center py-5 rounded-4 border border-success border-3">
                            <i class="fas fa-check-circle fa-5x mb-4 text-success"></i>
                            <h2 class="text-success">THÊM SẢN PHẨM THÀNH CÔNG!</h2>
                            <p class="lead">
                                Thông báo về sách mới đã được gửi tự động đến 
                                <strong>tất cả khách hàng</strong> (nếu có).
                            </p>
                            <div class="d-flex justify-content-center gap-3 mt-4">
                                <a href="sanpham.php" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-list"></i> Xem danh sách
                                </a>
                                <a href="sanpham_sua.php?id=<?= $maSP ?>" class="btn btn-info btn-lg px-5">
                                    <i class="fas fa-edit"></i> Sửa ngay
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger rounded-4">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $e): ?>
                                        <li><strong><?= htmlspecialchars($e) ?></strong></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold text-success">Tên sản phẩm</label>
                                    <input type="text" name="ten" class="form-control form-control-lg" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-success">Giá bán</label>
                                    <input type="number" name="gia" class="form-control form-control-lg" min="1000" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-success">Mô tả sản phẩm</label>
                                    <textarea name="mota" class="form-control" rows="5" placeholder="Mô tả chi tiết về sách..."></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success">Danh mục</label>
                                    <select name="madm" class="form-select form-select-lg" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php $danhMuc->data_seek(0); while($dm = $danhMuc->fetch_assoc()): ?>
                                            <option value="<?= $dm['MaDM'] ?>"><?= htmlspecialchars($dm['TenDM']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success">Nhà xuất bản</label>
                                    <select name="manxb" class="form-select form-select-lg" required>
                                        <option value="">-- Chọn NXB --</option>
                                        <?php $nxb->data_seek(0); while($x = $nxb->fetch_assoc()): ?>
                                            <option value="<?= $x['MaNXB'] ?>"><?= htmlspecialchars($x['TenNXB']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-success">Ảnh sản phẩm (chọn nhiều)</label>
                                    <input type="file" name="images[]" class="form-control form-control-lg" multiple required accept="image/*">
                                    <small class="text-muted">Giữ Ctrl để chọn nhiều ảnh</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success">Ảnh chính là ảnh số mấy? (bắt đầu từ 0)</label>
                                    <input type="number" name="anh_chinh" class="form-control form-control-lg" value="0" min="0">
                                </div>

                                <div class="col-12 text-center mt-5">
                                    <button type="submit" class="btn btn-success btn-lg px-5 shadow-lg">
                                        <i class="fas fa-save"></i> Lưu sản phẩm & Gửi thông báo tự động
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card { border-radius: 1.5rem !important; }
    .card-header { border-radius: 1.5rem 1.5rem 0 0 !important; }
    .btn { border-radius: 1rem; }
</style>

<?php include 'footer.php'; ?>