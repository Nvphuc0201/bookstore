<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-4 mb-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white p-2 rounded shadow-sm small">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php?controller=sanpham&action=list" class="text-decoration-none text-muted">Sách</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page"><?= htmlspecialchars($product['TenSP']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="detail-img-box border rounded p-3 text-center mb-3">
                <img id="mainImage" src="<?= $product['HinhAnh'] ? '/bookstore/assets/images/products/' . htmlspecialchars($product['HinhAnh']) : 'https://via.placeholder.com/500x700' ?>" 
                     class="img-fluid" style="max-height: 450px; object-fit: contain;">
            </div>
            
            <?php if (!empty($images)): ?>
            <div class="d-flex gap-2 justify-content-center overflow-auto">
                <img src="<?= '/bookstore/assets/images/products/' . htmlspecialchars($product['HinhAnh']) ?>" 
                      class="img-thumbnail thumb-img active" width="60" style="cursor:pointer" onclick="changeImage(this.src)">
                <?php foreach($images as $img): ?>
                    <img src="/bookstore/assets/images/products/<?= htmlspecialchars($img['DuongDan']) ?>" 
                         class="img-thumbnail thumb-img" width="60" style="cursor:pointer" onclick="changeImage(this.src)">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-7">
            <h2 class="fw-bold text-dark mb-2"><?= htmlspecialchars($product['TenSP']) ?></h2>
            
            <div class="mb-3 text-muted small">
                <span><i class="fa-solid fa-pen-nib me-1"></i> Tác giả: 
                    <strong class="text-dark"><?= htmlspecialchars($product['TacGia'] ?? 'Đang cập nhật') ?></strong>
                </span>
                <span class="mx-2">|</span>
                <span><i class="fa-solid fa-book me-1"></i> Nhà xuất bản: 
                    <strong class="text-dark"><?= htmlspecialchars($product['TenNXB'] ?? 'Đang cập nhật') ?></strong>
                </span>
            </div>

            <div class="d-flex gap-3 mb-3">
                <span class="badge bg-warning text-dark"><i class="fa-solid fa-fire"></i> Đã bán: <?= $product['SoLuongDaBan'] ?></span>
                <?php if($product['SoLuong'] > 0): ?>
                    <span class="badge bg-success"><i class="fa-solid fa-box-open"></i> Còn hàng: <?= $product['SoLuong'] ?></span>
                <?php else: ?>
                    <span class="badge bg-secondary">Hết hàng</span>
                <?php endif; ?>
            </div>

            <div class="price-section bg-light p-3 rounded mb-4">
                <div class="d-flex align-items-end gap-3">
                    <span class="display-6 fw-bold text-danger"><?= number_format($product['DonGia'], 0, ',', '.') ?> đ</span>
                </div>
            </div>

            <ul class="list-unstyled mb-4 small text-secondary">
                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Bọc Plastic miễn phí</li>
                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Giao hàng miễn phí trong nội thành</li>
                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Đổi trả trong 30 ngày</li>
            </ul>

            <div class="d-flex align-items-center gap-3 mb-4">
                <?php if($product['SoLuong'] > 0): ?>
                    <div class="input-group" style="width: 130px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateQty(-1)">-</button>
                        <input type="number" id="qty" class="form-control text-center bg-white" value="1" min="1" max="<?= $product['SoLuong'] ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="updateQty(1)">+</button>
                    </div>
                    
                    <button onclick="addToCartDetail(<?= $product['MaSP'] ?>)" class="btn btn-danger btn-lg flex-grow-1 fw-bold">
                        <i class="fa-solid fa-cart-plus me-2"></i> THÊM VÀO GIỎ HÀNG
                    </button>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg flex-grow-1 fw-bold" disabled style="cursor: not-allowed; opacity: 0.6;">
                        <i class="fa-solid fa-ban me-2"></i> TẠM HẾT HÀNG
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active fw-bold text-dark" href="#">Giới thiệu sách</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="content-text" style="line-height: 1.8; color: #333;">
                        <?= nl2br(htmlspecialchars_decode($product['MoTa'] ?? 'Đang cập nhật mô tả...')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(!empty($relatedProducts)): ?>
    <div class="mt-5">
        <h4 class="section-title text-uppercase mb-4 border-bottom pb-2 border-danger d-inline-block">
            Sách cùng thể loại
        </h4>
        <div class="row g-3 row-cols-2 row-cols-md-4">
            <?php foreach($relatedProducts as $sp): ?>
            <div class="col">
                <div class="product-card h-100 d-flex flex-column" style="background:white; border:1px solid #eee; border-radius:8px; overflow:hidden; transition:0.3s;">
                    <div class="img-box position-relative text-center p-3" style="height:220px; background:#fff;">
                        <a href="index.php?controller=sanpham&action=detail&id=<?= $sp['MaSP'] ?>">
                            <img src="<?= $sp['HinhAnh'] ? '/bookstore/assets/images/products/' . htmlspecialchars($sp['HinhAnh']) : 'https://via.placeholder.com/400x600' ?>" 
                                 style="height:100%; object-fit:contain;">
                        </a>
                    </div>
                    <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                        <div>
                            <a href="index.php?controller=sanpham&action=detail&id=<?= $sp['MaSP'] ?>" 
                               class="text-decoration-none text-dark fw-bold" 
                               style="display:-webkit-box; -webkit-line-clamp:2; overflow:hidden; height:40px; font-size:14px;">
                                <?= htmlspecialchars($sp['TenSP']) ?>
                            </a>
                        </div>
                        <div class="mt-2">
                            <div class="text-danger fw-bold"><?= number_format($sp['DonGia'],0,',','.') ?> đ</div>
                            <a href="index.php?controller=cart&action=add&id=<?= $sp['MaSP'] ?>" class="btn btn-outline-danger btn-sm w-100 mt-2">Xem ngay</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
    function changeImage(src) {
        document.getElementById('mainImage').src = src;
    }

    // [UPDATED] Hàm cập nhật số lượng có kiểm tra Max (Tồn kho)
    function updateQty(change) {
        let qtyInput = document.getElementById('qty');
        let currentQty = parseInt(qtyInput.value);
        let maxQty = parseInt(qtyInput.getAttribute('max')); // Lấy giới hạn tồn kho
        
        let newQty = currentQty + change;
        
        // Chỉ cho phép trong khoảng [1, maxQty]
        if (newQty >= 1 && newQty <= maxQty) {
            qtyInput.value = newQty;
        } else if (newQty > maxQty) {
            alert("Bạn chỉ có thể mua tối đa " + maxQty + " cuốn!");
        }
    }

    function addToCartDetail(id) {
        let qty = document.getElementById('qty').value;
        // Gọi AJAX (Lưu ý: CartController cần trả về JSON như đã cập nhật ở bước trước)
        fetch(`index.php?controller=cart&action=add&id=${id}&qty=${qty}`)
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                if(confirm('Đã thêm sản phẩm vào giỏ! Bạn có muốn đến giỏ hàng ngay không?')) {
                    window.location.href = 'index.php?controller=cart';
                } else {
                    location.reload();
                }
            } else {
                alert(data.message); // Báo lỗi nếu backend chặn (hết hàng)
            }
        })
        .catch(err => console.error(err));
    }
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>