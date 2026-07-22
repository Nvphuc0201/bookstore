<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/DonHang.php";

if (!isset($_GET['id'])) {
    header("Location: donhang.php");
    exit;
}

$model = new DonHang();
$id = (int)$_GET['id'];
$dh = $model->getById($id);
if (!$dh) die("Không tìm thấy đơn hàng");
$ct = $model->getChiTiet($id);

// Nếu gọi bằng AJAX (từ trang donhang.php) thì trả về HTML phần thân để nhúng vào overlay
if (isset($_GET['ajax']) && (int)$_GET['ajax'] === 1) {
    ?>
    <div>
        <div class="mb-3">
            <h5 class="fw-semibold mb-1" style="color: var(--text-primary);">
                Đơn hàng #<?= str_pad($dh['MaDH'], 5, '0', STR_PAD_LEFT) ?>
            </h5>
            <div class="small text-muted">
                Ngày đặt: <?= date('d/m/Y H:i', strtotime($dh['NgayDat'])) ?>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <p class="small text-muted mb-1">Khách hàng</p>
                <p class="mb-1 fw-semibold"><?= htmlspecialchars($dh['HoTen'] ?? 'Khách vãng lai') ?></p>
                <?php if (!empty($dh['SDT'])): ?>
                    <p class="mb-0 small text-muted"><i class="fas fa-phone me-1"></i><?= htmlspecialchars($dh['SDT']) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <p class="small text-muted mb-1">Trạng thái</p>
                <?php 
                    $statusClass = 'badge bg-secondary';
                    if($dh['TrangThai'] == 'ChoXacNhan') $statusClass = 'badge bg-warning text-dark';
                    if($dh['TrangThai'] == 'DangGiao')   $statusClass = 'badge bg-info text-white';
                    if($dh['TrangThai'] == 'DaGiao')     $statusClass = 'badge bg-success';
                    if($dh['TrangThai'] == 'DaHuy')      $statusClass = 'badge bg-danger';
                ?>
                <span class="<?= $statusClass ?>"><?= htmlspecialchars($dh['TrangThai']) ?></span>

                <p class="small text-muted mb-1 mt-3">Phương thức thanh toán</p>
                <p class="mb-0">
                    <?= $dh['PhuongThucThanhToan'] === 'ChuyenKhoan' ? 'Chuyển khoản' : 'Tiền mặt (COD)' ?>
                </p>
            </div>
        </div>

        <div class="mb-3">
            <p class="small text-muted mb-1">Địa chỉ giao hàng</p>
            <p class="mb-0"><?= nl2br(htmlspecialchars($dh['DiaChiGiaoHang'] ?? '')) ?></p>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="10%">Mã SP</th>
                        <th>Sản phẩm</th>
                        <th class="text-center" width="12%">Số lượng</th>
                        <th class="text-center" width="18%">Đơn giá</th>
                        <th class="text-center" width="18%">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $tongTien = 0;
                    while ($r = $ct->fetch_assoc()): 
                        $tongTien += $r['ThanhTien'];
                    ?>
                        <tr>
                            <td><?= $r['MaSP'] ?></td>
                            <td><?= htmlspecialchars($r['TenSP']) ?></td>
                            <td class="text-center"><?= (int)$r['SoLuong'] ?></td>
                            <td class="text-center"><?= number_format($r['DonGia'],0,',','.') ?> ₫</td>
                            <td class="text-center text-danger fw-semibold"><?= number_format($r['ThanhTien'],0,',','.') ?> ₫</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Tổng cộng</th>
                        <th class="text-center text-danger fw-bold"><?= number_format($tongTien,0,',','.') ?> ₫</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php
    exit;
}

// Fallback: nếu truy cập trực tiếp (không ajax) -> giữ trang đơn giản cũ
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn #<?= $id ?></title>
</head>
<body>
<h2>CHI TIẾT ĐƠN HÀNG #<?= $id ?></h2>

<p>Khách hàng: <?= htmlspecialchars($dh['HoTen'] ?? 'Khách vãng lai') ?></p>
<p>Ngày đặt: <?= $dh['NgayDat'] ?></p>
<p>Tổng tiền: <?= number_format($dh['TongTien'],0,',','.') ?> ₫</p>
<p>Trạng thái: <?= $dh['TrangThai'] ?></p>
<p>Địa chỉ giao hàng: <?= nl2br(htmlspecialchars($dh['DiaChiGiaoHang'] ?? '')) ?></p>

<table border="1" cellpadding="8">
    <tr>
        <th>MaSP</th>
        <th>Tên SP</th>
        <th>Số lượng</th>
        <th>Đơn giá</th>
        <th>Thành tiền</th>
    </tr>

    <?php while ($r = $ct->fetch_assoc()): ?>
        <tr>
            <td><?= $r['MaSP'] ?></td>
            <td><?= htmlspecialchars($r['TenSP']) ?></td>
            <td><?= $r['SoLuong'] ?></td>
            <td><?= number_format($r['DonGia'],0,',','.') ?> ₫</td>
            <td><?= number_format($r['ThanhTien'],0,',','.') ?> ₫</td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="donhang.php">⬅ Quay lại</a>
</body>
</html>
