<?php
// Gọi Header mới
include __DIR__ . '/../layout/header.php';

// Logic cũ: Lấy 5 sách bán chạy nhất
$bestSellers = array_slice($bestSellers ?? [], 0, 5);
?>

<style>
    /* CSS CĂN CHỈNH SẢN PHẨM */
    
    /* Hero Banner */
    .hero-section {
        background: linear-gradient(135deg, #2c3e50, #000000);
        color: white;
        padding: 60px 0;
        text-align: center;
        margin-bottom: 40px;
    }

    /* Tiêu đề section */
    .section-header {
        border-bottom: 2px solid #e67e22;
        margin-bottom: 20px;
        padding-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .section-title {
        font-weight: 800;
        color: #2c3e50;
        margin: 0;
        font-size: 1.2rem;
        text-transform: uppercase;
    }

    /* Card sản phẩm */
    .product-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        transition: 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .product-card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-color: #e67e22;
        transform: translateY(-5px);
    }

    /* Ảnh sản phẩm */
    .img-box {
        height: 220px;
        width: 100%;
        padding: 15px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .img-box img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    .badge-hot {
        position: absolute;
        top: 10px; right: 10px;
        background: #e74c3c; color: #fff;
        font-size: 10px; padding: 2px 6px; border-radius: 4px;
    }
    
    /* Nhãn hết hàng */
    .badge-out {
        position: absolute;
        top: 10px; left: 10px;
        background: #95a5a6; color: #fff;
        font-size: 10px; padding: 2px 6px; border-radius: 4px;
        font-weight: bold;
    }

    /* Nội dung card */
    .card-details {
        padding: 10px 15px 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .product-name {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 40px;
        text-decoration: none;
    }
    .product-name:hover { color: #e67e22; }
    
    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
    }
    .price { font-weight: bold; color: #c0392b; font-size: 16px; }

    /* Nút mua hàng */
    .btn-add {
        width: 100%;
        margin-top: 10px;
        background: white;
        color: #e67e22;
        border: 1px solid #e67e22;
        padding: 5px;
        font-weight: 600;
        border-radius: 4px;
        transition: 0.2s;
        text-decoration: none;
        display: block;
        text-align: center;
        font-size: 13px;
        cursor: pointer;
    }
    .btn-add:hover { background: #e67e22; color: white; }
    
    /* Nút hết hàng */
    .btn-disabled {
        background: #eee;
        color: #999;
        border-color: #ddd;
        cursor: not-allowed;
    }
    .btn-disabled:hover { background: #eee; color: #999; }

</style>

<section class="hero-section">
    <div class="container">
        <h1 class="fw-bold">BookStore Premium</h1>
        <p>Hành trình tri thức bắt đầu từ những trang sách</p>
    </div>
</section>

<div class="container">

    <section class="mb-5">
        <div class="section-header">
            <h3 class="section-title">🔥 SÁCH BÁN CHẠY</h3>
            <a href="index.php?controller=sanpham" style="color: #e67e22; text-decoration: none; font-size: 13px;">Xem tất cả ></a>
        </div>

        <div class="row g-3 row-cols-2 row-cols-md-4 row-cols-lg-5">
            <?php foreach($bestSellers as $sp): ?>
            <div class="col">
                <div class="product-card">
                    <div class="img-box">
                        <img src="<?= $sp['HinhAnh'] ? '/bookstore/assets/images/products/' . htmlspecialchars($sp['HinhAnh']) : 'https://via.placeholder.com/400x600' ?>">
                        <span class="badge-hot">HOT</span>
                        <?php if($sp['SoLuong'] <= 0): ?>
                             <span class="badge-out">HẾT HÀNG</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-details">
                        <a href="index.php?controller=sanpham&action=detail&id=<?= $sp['MaSP'] ?>" class="product-name">
                            <?= htmlspecialchars($sp['TenSP']) ?>
                        </a>
                        <div class="price-row">
                            <span class="price"><?= number_format($sp['DonGia'],0,',','.') ?> đ</span>
                            <small class="text-muted" style="font-size: 10px;">Đã bán: <?= $sp['SoLuongDaBan'] ?></small>
                        </div>
                        
                        <?php if($sp['SoLuong'] > 0): ?>
                            <a href="javascript:void(0);" onclick="addToCartIndex(<?= $sp['MaSP'] ?>)" class="btn-add">
                                THÊM VÀO GIỎ
                            </a>
                        <?php else: ?>
                            <a href="javascript:void(0);" onclick="alert('Xin lỗi, sản phẩm này tạm thời hết hàng!');" class="btn-add btn-disabled">
                                HẾT HÀNG
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php foreach($categories as $dm): 
        $catProducts = array_filter($products, function($p) use($dm){
            return $p['MaDM'] == $dm['MaDM'];
        });
        $catProducts = array_slice(array_values($catProducts), 0, 5); 
        if(empty($catProducts)) continue;
    ?>

    <section class="mb-5">
        <div class="section-header">
            <h3 class="section-title"><?= htmlspecialchars($dm['TenDM']) ?></h3>
        </div>

        <div class="row g-3 row-cols-2 row-cols-md-4 row-cols-lg-5">
            <?php foreach($catProducts as $sp): ?>
            <div class="col">
                <div class="product-card">
                    <div class="img-box">
                        <img src="<?= $sp['HinhAnh'] ? '/bookstore/assets/images/products/' . htmlspecialchars($sp['HinhAnh']) : 'https://via.placeholder.com/400x600' ?>">
                        <?php if($sp['SoLuong'] <= 0): ?>
                             <span class="badge-out">HẾT HÀNG</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-details">
                        <a href="index.php?controller=sanpham&action=detail&id=<?= $sp['MaSP'] ?>" class="product-name">
                            <?= htmlspecialchars($sp['TenSP']) ?>
                        </a>
                        <div class="price-row">
                            <span class="price"><?= number_format($sp['DonGia'],0,',','.') ?> đ</span>
                            <small class="text-muted" style="font-size: 10px;">Đã bán: <?= isset($sp['SoLuongDaBan']) ? $sp['SoLuongDaBan'] : 0 ?></small>
                        </div>
                        
                        <?php if($sp['SoLuong'] > 0): ?>
                            <a href="javascript:void(0);" onclick="addToCartIndex(<?= $sp['MaSP'] ?>)" class="btn-add">
                                THÊM VÀO GIỎ
                            </a>
                        <?php else: ?>
                            <a href="javascript:void(0);" onclick="alert('Xin lỗi, sản phẩm này tạm thời hết hàng!');" class="btn-add btn-disabled">
                                HẾT HÀNG
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endforeach; ?>

</div>

<script>
function addToCartIndex(id) {
    // Gọi AJAX đến Controller Cart
    fetch(`index.php?controller=cart&action=add&id=${id}&qty=1`)
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert('Đã thêm sản phẩm vào giỏ hàng!');
            // Load lại trang để cập nhật số lượng trên header
            location.reload(); 
        } else {
            // Trường hợp lỗi (ví dụ hết hàng mà controller trả về lỗi)
            alert(data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Có lỗi xảy ra, vui lòng thử lại.');
    });
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>