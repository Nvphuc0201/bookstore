<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/SanPham.php";
require_once "../app/models/TacGia.php";
require_once "../app/models/SanPhamTacGia.php";

$spModel = new SanPham();
$tgModel = new TacGia();
$sptgModel = new SanPhamTacGia();

if (!isset($_GET['id'])) {
    header("Location: sanpham.php"); exit();
}

$maSP = (int)$_GET['id'];
$sanPham = $spModel->getById($maSP);

if (!$sanPham) {
    die("<div class='alert alert-danger'>Không tìm thấy sản phẩm!</div>");
}

$danhSachTacGia = $tgModel->getAll();

// LẤY TÁC GIẢ ĐÃ GÁN – ĐÃ JOIN ĐỂ CÓ THÔNG TIN QUỐC TỊCH
$stmt = $conn->prepare("
    SELECT tg.MaTacGia, tg.TenTacGia, tg.QuocTich, sptg.VaiTro 
    FROM SanPham_TacGia sptg
    JOIN TacGia tg ON sptg.MaTacGia = tg.MaTacGia
    WHERE sptg.MaSP = ?
    ORDER BY sptg.VaiTro
");
$stmt->bind_param("i", $maSP);
$stmt->execute();
$tacGiaDaGan = $stmt->get_result();

// XỬ LÝ GÁN TÁC GIẢ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gan'])) {
    $maTacGia = (int)$_POST['maTacGia'];
    $vaiTro = trim($_POST['vaiTro']) ?: 'Tác giả';

    // Kiểm tra trùng
    $stmt = $conn->prepare("SELECT 1 FROM SanPham_TacGia WHERE MaSP = ? AND MaTacGia = ?");
    $stmt->bind_param("ii", $maSP, $maTacGia);
    $stmt->execute();
    $check = $stmt->get_result();
    if ($check->num_rows == 0) {
        $sptgModel->insert($maSP, $maTacGia, $vaiTro);
        echo "<script>alert('Gán tác giả thành công!'); window.location.href='sanpham_gan_tacgia.php?id=$maSP';</script>";
    } else {
        echo "<script>alert('Tác giả này đã được gán rồi!');</script>";
    }
}
?>

<?php include 'sidebar.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Thông tin sản phẩm -->
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">
                        Gán Tác Giả Cho Sản Phẩm
                    </h3>
                </div>
                <div class="card-body text-center py-4">
                    <h4 class="text-dark fw-bold"><?= htmlspecialchars($sanPham['TenSP']) ?></h4>
                    <p class="text-muted">Mã sản phẩm: <strong>#<?= $maSP ?></strong></p>
                    <?php if ($sanPham['HinhAnh']): ?>
                        <img src="../assets/images/products/<?= htmlspecialchars($sanPham['HinhAnh']) ?>"
                             class="rounded shadow-sm" width="120" style="object-fit: cover;">
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-4">
                <!-- Form thêm tác giả -->
                <div class="col-lg-5">
                    <div class="card shadow border-0 rounded-4 h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Thêm Tác Giả Mới</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-success">Chọn tác giả</label>
                                    <select name="maTacGia" class="form-select form-select-lg" required>
                                        <option value="">-- Chọn tác giả --</option>
                                        <?php 
                                        $danhSachTacGia->data_seek(0);
                                        while ($tg = $danhSachTacGia->fetch_assoc()): 
                                            // Ẩn tác giả đã gán
                                            $daGan = false;
                                            $tacGiaDaGan->data_seek(0);
                                            while ($da = $tacGiaDaGan->fetch_assoc()) {
                                                if ($da['MaTacGia'] == $tg['MaTacGia']) {
                                                    $daGan = true;
                                                    break;
                                                }
                                            }
                                            if (!$daGan):
                                        ?>
                                            <option value="<?= $tg['MaTacGia'] ?>">
                                                <?= htmlspecialchars($tg['TenTacGia']) ?>
                                                <?= !empty($tg['QuocTich']) ? " (" . htmlspecialchars($tg['QuocTich']) . ")" : "" ?>
                                            </option>
                                        <?php endif; endwhile; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-success">Vai trò</label>
                                    <input type="text" name="vaiTro" class="form-control form-control-lg"
                                           value="Tác giả" placeholder="VD: Biên dịch, Minh họa...">
                                </div>

                                <div class="text-center">
                                    <button type="submit" name="gan" class="btn btn-success btn-lg px-5 shadow">
                                        Gán tác giả
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Danh sách đã gán -->
                <div class="col-lg-7">
                    <div class="card shadow border-0 rounded-4 h-100">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                Tác Giả Đã Gán 
                                <span class="badge bg-dark"><?= $tacGiaDaGan->num_rows ?></span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($tacGiaDaGan->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="10%">#</th>
                                                <th>Tên tác giả</th>
                                                <th>Vai trò</th>
                                                <th width="15%" class="text-center">Xóa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $stt = 1;
                                            $tacGiaDaGan->data_seek(0);
                                            while ($row = $tacGiaDaGan->fetch_assoc()): 
                                            ?>
                                                <tr>
                                                    <td class="text-center fw-bold"><?= $stt++ ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($row['TenTacGia']) ?></strong>
                                                        <?php if (!empty($row['QuocTich'])): ?>
                                                            <span class="text-muted small">(<?= htmlspecialchars($row['QuocTich']) ?>)</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info text-dark"><?= htmlspecialchars($row['VaiTro']) ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="sanpham_xoa_tacgia.php?sp=<?= $maSP ?>&tg=<?= $row['MaTacGia'] ?>"
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('Xóa tác giả \"<?= addslashes($row['TenTacGia']) ?>\" khỏi sách này?')">
                                                            Xóa
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">
                                    <p>Chưa có tác giả nào được gán</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-light text-center">
                            <a href="sanpham.php" class="btn btn-secondary btn-lg">
                                Quay lại danh sách sản phẩm
                            </a>
                        </div>
                    </div>
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