<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/NhapHang.php";

if (!isset($_GET['id'])) {
    header("Location: nhaphang.php");
    exit;
}

$model = new NhapHang();
$id = (int)$_GET['id'];
$hd = $model->getById($id);
if (!$hd) {
    die("Không tìm thấy phiếu nhập");
}
$ct = $model->getChiTiet($id);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết phiếu nhập #<?= $id ?></title>
</head>
<body>
<h2>CHI TIẾT PHIẾU NHẬP #<?= $id ?></h2>

<p>Nhà cung cấp: <?= htmlspecialchars($hd['TenNCC']) ?></p>
<p>Ngày nhập: <?= $hd['NgayNhap'] ?></p>
<p>Tổng tiền: <?= number_format($hd['TongTienNhap'] ?? 0, 0, ',', '.') ?> ₫</p>

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
            <td><?= $r['SoLuongNhap'] ?></td>
            <td><?= number_format($r['DonGiaNhap'], 0, ',', '.') ?> ₫</td>
            <td><?= number_format($r['ThanhTien'], 0, ',', '.') ?> ₫</td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="nhaphang.php">⬅ Quay lại</a>
</body>
</html>
