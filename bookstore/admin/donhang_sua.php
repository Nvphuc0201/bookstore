<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/DonHang.php";

// Include hàm gửi thông báo tự động
require_once "../includes/gui_thongbao.php";

if (!isset($_GET['id'])) {
    header("Location: donhang.php");
    exit();
}

$model = new DonHang();
$id = (int)$_GET['id'];
$dh = $model->getById($id);

if (!$dh) {
    die("<div class='alert alert-danger'>Không tìm thấy đơn hàng!</div>");
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = $_POST['trangthai'] ?? $dh['TrangThai'];
    
    // Danh sách trạng thái hợp lệ
    $validStatus = ['ChoXacNhan', 'DangGiao', 'DaGiao', 'DaHuy'];
    if (!in_array($newStatus, $validStatus)) {
        $errors[] = "Trạng thái không hợp lệ!";
    } else {
        // Cập nhật trạng thái
        if ($model->updateStatus($id, $newStatus)) {
            $success = true;

            // TỰ ĐỘNG GỬI THÔNG BÁO CHO KHÁCH HÀNG
            $ten_trangthai = match($newStatus) {
                'ChoXacNhan' => 'Chờ xác nhận',
                'DangGiao'   => 'Đang giao hàng',
                'DaGiao'     => 'Đã giao thành công',
                'DaHuy'      => 'Đã bị hủy',
                default      => $newStatus
            };

            $noi_dung = "Đơn hàng #{$id} của bạn đã được cập nhật trạng thái:\n\n";
            $noi_dung .= "Trạng thái mới: {$ten_trangthai}\n";
            $noi_dung .= "Cập nhật lúc: " . date('d/m/Y H:i') . "\n\n";
            $noi_dung .= "Cảm ơn bạn đã mua sắm tại BookStore!";

            guiThongBao(
                $dh['MaKH'],
                "Đơn hàng #{$id} – {$ten_trangthai}",
                $noi_dung,
                'DonHang',
                "khachhang_donhang_chitiet.php?id={$id}" // link cho khách xem
            );

            // Nếu giao thành công → có thể cộng điểm, tặng voucher, v.v.
            if ($newStatus === 'DaGiao') {
                // Ví dụ: tặng 100 điểm tích lũy
                // $conn->query("UPDATE KhachHang SET DiemTichLuy = DiemTichLuy + 100 WHERE MaKH = {$dh['MaKH']}");
            }
        } else {
            $errors[] = "Cập nhật trạng thái thất bại. Vui lòng thử lại.";
        }
    }
}
?>

<?php include 'sidebar.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-sync-alt"></i> Cập Nhật Trạng Thái Đơn Hàng
                    </h3>
                    <p class="mb-0 mt-2 opacity-90">Mã đơn: <strong>#<?= str_pad($id, 5, '0', STR_PAD_LEFT) ?></strong></p>
                </div>

                <div class="card-body p-5">
                    <?php if ($success): ?>
                        <div class="alert alert-success text-center py-4 rounded-4">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h4>Cập nhật thành công!</h4>
                            <p>Thông báo đã được gửi tự động đến khách hàng.</p>
                            <a href="donhang_chitiet.php?id=<?= $id ?>" class="btn btn-success btn-lg mt-3">
                                <i class="fas fa-eye"></i> Xem chi tiết đơn hàng
                            </a>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $e): ?>
                                        <li><?= htmlspecialchars($e) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mb-4">
                            <h5>Thông tin khách hàng:</h5>
                            <p class="mb-1"><strong><?= htmlspecialchars($dh['HoTen'] ?? 'Khách lẻ') ?></strong></p>
                            <p class="text-muted">
                                <i class="fas fa-phone"></i> <?= $dh['SDT'] ?? '—' ?> | 
                                <i class="fas fa-envelope"></i> <?= $dh['Email'] ?? '—' ?>
                            </p>
                        </div>

                        <hr>

                        <form method="POST" class="mt-4">
                            <div class="mb-4 text-center">
                                <p class="mb-2"><strong>Trạng thái hiện tại:</strong></p>
                                <h4>
                                    <span class="badge 
                                        <?= $dh['TrangThai'] == 'ChoXacNhan' ? 'bg-warning' : 
                                           ($dh['TrangThai'] == 'DangGiao' ? 'bg-primary' : 
                                           ($dh['TrangThai'] == 'DaGiao' ? 'bg-success' : 'bg-danger')) ?>">
                                        <?= match($dh['TrangThai']) {
                                            'ChoXacNhan' => 'Chờ xác nhận',
                                            'DangGiao'   => 'Đang giao',
                                            'DaGiao'     => 'Đã giao',
                                            'DaHuy'      => 'Đã hủy',
                                            default      => $dh['TrangThai']
                                        } ?>
                                    </span>
                                </h4>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold fs-5">Chọn trạng thái mới:</label>
                                <select name="trangthai" class="form-select form-select-lg text-center" required>
                                    <option value="ChoXacNhan" <?= $dh['TrangThai']=='ChoXacNhan'?'selected':'' ?>>
                                        Chờ xác nhận
                                    </option>
                                    <option value="DangGiao" <?= $dh['TrangThai']=='DangGiao'?'selected':'' ?>>
                                        Đang giao hàng
                                    </option>
                                    <option value="DaGiao" <?= $dh['TrangThai']=='DaGiao'?'selected':'' ?>>
                                        Đã giao thành công
                                    </option>
                                    <option value="DaHuy" <?= $dh['TrangThai']=='DaHuy'?'selected':'' ?>>
                                        Đã hủy đơn
                                    </option>
                                </select>
                            </div>

                            <div class="d-grid gap-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Cập nhật trạng thái
                                </button>
                                <a href="donhang_chitiet.php?id=<?= $id ?>" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-left"></i> Quay lại chi tiết
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 1.5rem !important;
    }
    .card-header {
        border-radius: 1.5rem 1.5rem 0 0 !important;
    }
    .btn {
        border-radius: 1rem;
    }
</style>

<?php include 'footer.php'; ?>