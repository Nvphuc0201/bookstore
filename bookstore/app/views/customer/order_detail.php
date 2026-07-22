<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-uppercase m-0 border-start border-4 border-danger ps-3">
            Chi tiết đơn hàng #<?= htmlspecialchars($order['MaDH']) ?>
        </h3>
        <a href="index.php?controller=customer" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-chevron-left"></i> Quay lại tài khoản
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom">
                    Thông tin đơn hàng
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Ngày đặt</p>
                            <p class="fw-bold"><?= date('d/m/Y H:i', strtotime($order['NgayDat'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Trạng thái</p>
                            <?php 
                                $statusClass = 'bg-secondary';
                                if($order['TrangThai'] == 'ChoXacNhan') $statusClass = 'bg-warning text-dark';
                                if($order['TrangThai'] == 'DangGiao') $statusClass = 'bg-info text-white';
                                if($order['TrangThai'] == 'DaGiao') $statusClass = 'bg-success';
                                if($order['TrangThai'] == 'DaHuy') $statusClass = 'bg-danger';
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($order['TrangThai']) ?></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Phương thức thanh toán</p>
                            <p class="fw-bold">
                                <?= $order['PhuongThucThanhToan'] === 'ChuyenKhoan' ? 'Chuyển khoản' : 'Thanh toán khi nhận hàng (COD)' ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Tổng tiền</p>
                            <p class="fw-bold text-danger fs-5"><?= number_format($order['TongTien'], 0, ',', '.') ?> đ</p>
                        </div>
                    </div>
                    <div>
                        <p class="mb-1 small text-muted">Địa chỉ giao hàng</p>
                        <p><?= nl2br(htmlspecialchars($order['DiaChiGiaoHang'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 border-bottom">
                    Tóm tắt
                </div>
                <div class="card-body">
                    <p class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tổng sản phẩm</span>
                        <span class="fw-bold"><?= count($items) ?></span>
                    </p>
                    <p class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tổng tiền hàng</span>
                        <span class="fw-bold"><?= number_format($order['TongTien'], 0, ',', '.') ?> đ</span>
                    </p>
                    <p class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phí vận chuyển</span>
                        <span class="fw-bold text-success">Miễn phí</span>
                    </p>
                    <hr>
                    <p class="d-flex justify-content-between mb-0">
                        <span class="fw-bold">Tổng thanh toán</span>
                        <span class="fw-bold text-danger fs-5"><?= number_format($order['TongTien'], 0, ',', '.') ?> đ</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-white fw-bold py-3 border-bottom">
            Sản phẩm trong đơn hàng
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-center">Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-center">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                        <tr>
                            <td><?= htmlspecialchars($it['TenSP']) ?></td>
                            <td class="text-center"><?= number_format($it['DonGia'], 0, ',', '.') ?> đ</td>
                            <td class="text-center"><?= (int)$it['SoLuong'] ?></td>
                            <td class="text-center text-danger fw-bold">
                                <?= number_format($it['ThanhTien'], 0, ',', '.') ?> đ
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>


