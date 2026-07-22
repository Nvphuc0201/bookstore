<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-4 text-uppercase border-start border-4 border-danger ps-3">
                Xác nhận thanh toán
            </h4>

            <div class="row">
                <div class="col-md-7">
                    <h5 class="mb-3 fw-bold text-secondary">1. Thông tin giao hàng</h5>
                    
                    <form method="post" action="index.php?controller=cart&action=checkout">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Họ và tên người nhận</label>
                            <input type="text" name="fullname" class="form-control" 
                                   value="<?= htmlspecialchars($userInfo['HoTen'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="<?= htmlspecialchars($userInfo['SDT'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Địa chỉ giao hàng</label>
                            <textarea name="address" class="form-control" rows="3" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện..."><?= htmlspecialchars($userInfo['DiaChi'] ?? '') ?></textarea>
                            <div class="form-text text-muted">Chúng tôi sẽ giao hàng đến địa chỉ này.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Phương thức thanh toán</label>
                            <div class="card p-3 bg-light border-0">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay1" value="TienMat" checked>
                                    <label class="form-check-label" for="pay1">
                                        <i class="fa-solid fa-money-bill-wave text-success me-2"></i> Thanh toán khi nhận hàng (COD)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay2" value="ChuyenKhoan">
                                    <label class="form-check-label" for="pay2">
                                        <i class="fa-solid fa-building-columns text-primary me-2"></i> Chuyển khoản ngân hàng
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="index.php?controller=cart" class="text-decoration-none text-secondary">
                                <i class="fa-solid fa-arrow-left"></i> Quay lại giỏ hàng
                            </a>
                            <button type="submit" class="btn btn-alpha px-4 py-2 fw-bold text-uppercase">
                                Đặt hàng ngay
                            </button>
                        </div>
                    </form>
                </div>

                <div class="col-md-5 mt-4 mt-md-0">
                    <div class="bg-light p-4 rounded-3">
                        <h5 class="mb-3 fw-bold text-secondary">2. Đơn hàng của bạn</h5>
                        
                        <div class="list-group mb-3 shadow-sm">
                            <?php foreach ($cart as $item): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-white border-0 mb-1 rounded">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-secondary rounded-pill me-2"><?= $item['qty'] ?></span>
                                        <div>
                                            <div class="fw-bold small text-truncate" style="max-width: 180px;">
                                                <?= htmlspecialchars($item['name']) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fw-bold text-dark small">
                                        <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?> đ
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Tạm tính</span>
                                <strong><?= number_format($totalPrice, 0, ',', '.') ?> đ</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Phí vận chuyển</span>
                                <span class="text-success fw-bold">Miễn phí</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <span class="fw-bold fs-5">Tổng cộng</span>
                                <span class="fw-bold text-danger fs-4"><?= number_format($totalPrice, 0, ',', '.') ?> đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>