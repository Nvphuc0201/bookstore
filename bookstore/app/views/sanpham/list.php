<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-header bg-white fw-bold text-uppercase" style="border-bottom: 2px solid #e67e22;">
                    <i class="fa-solid fa-list me-2"></i> Danh mục sách
                </div>
                <div class="list-group list-group-flush">
                    <a href="index.php?controller=sanpham&action=list" class="list-group-item list-group-item-action fw-bold text-danger">
                        Tất cả sách
                    </a>
                    <?php foreach ($categories as $dm): ?>
                        <a href="index.php?controller=sanpham&action=list&id=<?= $dm['MaDM'] ?>" class="list-group-item list-group-item-action text-secondary">
                            <?= htmlspecialchars($dm['TenDM']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                <h4 class="fw-bold text-dark m-0">
                    <?= $pageTitle ?> 
                    <span class="text-muted fs-6 fw-normal">(<?= count($products) ?> đầu sách)</span>
                </h4>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="100" alt="Not found" class="mb-3 opacity-50">
                    <p class="text-muted">Rất tiếc, không tìm thấy cuốn sách nào phù hợp.</p>
                    <a href="index.php?controller=sanpham&action=list" class="btn btn-primary">Xem tất cả sách</a>
                </div>
            <?php else: ?>
                <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-lg-4">
                    <?php foreach ($products as $sp): ?>
                        <div class="col">
                            <div class="product-card h-100 d-flex flex-column" style="background:#fff; border:1px solid #eee; border-radius:8px; overflow:hidden; transition:0.3s; cursor:pointer;" onmouseover="this.style.boxShadow='0 10px 20px rgba(0,0,0,0.1)'; this.style.borderColor='#e67e22';" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#eee';">
                                <div class="img-box position-relative" style="height:200px; padding:10px; display:flex; align-items:center; justify-content:center; background:#f8f9fa;">
                                    <a href="index.php?controller=sanpham&action=detail&id=<?= $sp['MaSP'] ?>" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                        <img src="<?= $sp['HinhAnh'] ? '/bookstore/assets/images/products/' . htmlspecialchars($sp['HinhAnh']) : 'https://via.placeholder.com/400x600' ?>" 
                                             style="max-height:100%; max-width:100%; object-fit:contain;" alt="<?= htmlspecialchars($sp['TenSP']) ?>">
                                    </a>
                                </div>
                                
                                <div class="card-details p-3 flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="text-muted small mb-1"><?= htmlspecialchars($sp['TenDM'] ?? 'Sách') ?></div>
                                        
                                        <a href="index.php?controller=sanpham&action=detail&id=<?= $sp['MaSP'] ?>" class="text-decoration-none text-dark fw-bold product-name-link" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; height:40px; font-size:14px;">
                                            <?= htmlspecialchars($sp['TenSP']) ?>
                                        </a>
                                        
                                        <div class="text-secondary small mt-1 text-truncate">
                                            <i class="fa-solid fa-pen-nib"></i> <?= htmlspecialchars($sp['TacGia'] ?? 'Đang cập nhật') ?>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <span class="text-danger fw-bold fs-5"><?= number_format($sp['DonGia'], 0, ',', '.') ?> đ</span>
                                                <div class="small text-muted">Còn: <?= isset($sp['SoLuong']) ? (int)$sp['SoLuong'] : '—' ?> | Đã bán: <?= isset($sp['SoLuongDaBan']) ? (int)$sp['SoLuongDaBan'] : '—' ?></div>
                                            </div>
                                        </div>
                                        <?php $outOfStock = isset($sp['SoLuong']) && (int)$sp['SoLuong'] <= 0; ?>
                                        <button type="button" onclick="addToCart(<?= $sp['MaSP'] ?>)" class="btn w-100" style="border:1px solid #e67e22; color:#e67e22; font-weight:600; <?= $outOfStock ? 'opacity:.6;cursor:not-allowed;' : '' ?>" <?= $outOfStock ? 'disabled' : '' ?> data-instock="<?= $outOfStock ? 0 : 1 ?>">
                                            <?= $outOfStock ? 'HẾT HÀNG' : 'THÊM VÀO GIỎ' ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function addToCart(id) {
    const btn = document.querySelector(`button[onclick="addToCart(${id})"]`);
    if (btn && btn.getAttribute('data-instock') === '0') {
        alert('Sản phẩm hiện đã hết hàng.');
        return;
    }

    fetch(`index.php?controller=cart&action=add&id=${id}&qty=1`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'login_required') {
                if (confirm('Bạn cần đăng nhập để mua hàng. Đến trang đăng nhập ngay?')) {
                    window.location.href = 'index.php?controller=auth&action=login';
                }
                return;
            }
            if (data.status === 'success') {
                alert('Đã thêm vào giỏ hàng!');
                location.reload();
            } else {
                alert(data.message || 'Thêm vào giỏ hàng thất bại.');
            }
        })
        .catch(() => alert('Có lỗi xảy ra, vui lòng thử lại.'));
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>