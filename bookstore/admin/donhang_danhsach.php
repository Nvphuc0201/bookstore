<?php
require_once "../app/config/db.php";
require_once "../app/models/DonHang.php";

if (!isset($_GET["id"])) {
    die("Không tìm thấy mã đơn hàng");
}

$MaDH = intval($_GET["id"]);

$donhangModel = new DonHang();
$dh = $donhangModel->getById($MaDH);
$ct = $donhangModel->getChiTiet($MaDH);
?>

<h2>CHI TIẾT ĐƠN HÀNG #<?php echo $dh["MaDH"]; ?></h2>

<p><strong>Khách hàng:</strong> <?php echo $dh["HoTen"]; ?></p>
<p><strong>Ngày đặt:</strong> <?php echo $dh["NgayDat"]; ?></p>
<p><strong>Tổng tiền:</strong> <?php echo number_format($dh["TongTien"]); ?> ₫</p>
<p><strong>Trạng thái:</strong> <?php echo $dh["TrangThai"]; ?></p>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Mã SP</th>
        <th>Tên SP</th>
        <th>Số lượng</th>
        <th>Đơn giá</th>
        <th>Thành tiền</th>
    </tr>

    <?php while ($row = $ct->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row["MaSP"]; ?></td>
            <td><?php echo $row["TenSP"]; ?></td>
            <td><?php echo $row["SoLuong"]; ?></td>
            <td><?php echo number_format($row["DonGia"]); ?> ₫</td>
            <td><?php echo number_format($row["ThanhTien"]); ?> ₫</td>
        </tr>
    <?php } ?>
</table>

<br>
<a href="donhang.php">⬅ Quay lại</a>
