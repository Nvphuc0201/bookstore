<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";

// Gửi thông báo
if (isset($_POST['gui'])) {
    $tieu_de  = trim($_POST['tieu_de']);
    $noi_dung = trim($_POST['noi_dung']);
    $loai     = $_POST['loai'];
    $lienket  = trim($_POST['lienket'] ?? '');
    $gui_cho  = $_POST['gui_cho'];

    $stmt = $conn->prepare("INSERT INTO ThongBao (MaKH, TieuDe, NoiDung, LoaiTB, LienKet) VALUES (?, ?, ?, ?, ?)");

    if ($gui_cho == 'all') {
        $khach = $conn->query("SELECT MaKH FROM KhachHang WHERE TrangThai = 1");
        while ($kh = $khach->fetch_assoc()) {
            $stmt->bind_param("issss", $kh['MaKH'], $tieu_de, $noi_dung, $loai, $lienket);
            $stmt->execute();
        }
    } elseif ($gui_cho == 'mot') {
        $makh = (int)$_POST['makh'];
        $stmt->bind_param("issss", $makh, $tieu_de, $noi_dung, $loai, $lienket);
        $stmt->execute();
    }

    echo "<script>alert('Gửi thông báo thành công!'); window.location='thongbao.php';</script>";
}

// Lấy danh sách khách hàng
$ds_khach = $conn->query("SELECT MaKH, HoTen FROM KhachHang WHERE TrangThai = 1 ORDER BY NgayDangKy DESC");
?>

<?php include 'sidebar.php'; ?>

<style>
    .form-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .form-header {
        background: var(--bg-light);
        border-bottom: 1px solid var(--border-color);
        padding: 20px;
    }

    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 14px;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--text-primary);
        box-shadow: 0 0 0 3px rgba(30, 41, 59, 0.1);
    }

    .table-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .table thead {
        background: var(--bg-light);
        border-bottom: 2px solid var(--border-color);
    }

    .table thead th {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px;
        border: none;
    }

    .table tbody td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
    }

    .table tbody tr:hover {
        background: var(--bg-light);
    }

    .type-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .type-promo { background: #fee2e2; color: #991b1b; }
    .type-order { background: #d1fae5; color: #065f46; }
    .type-product { background: #dbeafe; color: #1e40af; }
    .type-system { background: #e2e8f0; color: var(--text-secondary); }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
            Gửi Thông Báo Đến Khách Hàng
        </h2>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Soạn và gửi thông báo đến khách hàng</p>
    </div>

    <!-- Form Gửi Thông Báo -->
    <div class="form-card mb-4">
        <div class="form-header">
            <h4 class="mb-0 fw-semibold" style="color: var(--text-primary);">
                <i class="fas fa-paper-plane me-2"></i>Soạn Thông Báo Mới
            </h4>
        </div>
        <div class="card-body p-4">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Tiêu đề</label>
                        <input type="text" name="tieu_de" class="form-control" 
                               placeholder="VD: Black Friday giảm 50% toàn bộ!" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Loại thông báo</label>
                        <select name="loai" class="form-select" required>
                            <option value="KhuyenMai">Khuyến mãi mới</option>
                            <option value="DonHang">Cập nhật đơn hàng</option>
                            <option value="SanPhamMoi">Sản phẩm mới</option>
                            <option value="HeThong">Thông báo hệ thống</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Gửi cho</label>
                        <select name="gui_cho" class="form-select" onchange="toggleKhachHang(this.value)">
                            <option value="all">Tất cả khách hàng</option>
                            <option value="mot">Chỉ một người</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="khachhang_select" style="display:none;">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Chọn khách hàng</label>
                        <select name="makh" class="form-select">
                            <?php while($kh = $ds_khach->fetch_assoc()): ?>
                                <option value="<?= $kh['MaKH'] ?>"><?= htmlspecialchars($kh['HoTen']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Nội dung</label>
                        <textarea name="noi_dung" class="form-control" rows="5" 
                                  placeholder="Nội dung thông báo..." required></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Liên kết (tùy chọn)</label>
                        <input type="text" name="lienket" class="form-control" 
                               placeholder="VD: chi-tiet-san-pham.php?id=123 hoặc khuyenmai.php">
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" name="gui" class="btn" style="background: var(--text-primary); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500;">
                            <i class="fas fa-paper-plane me-2"></i>Gửi ngay
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách thông báo gần nhất -->
    <div class="table-card">
        <div class="form-header">
            <h5 class="mb-0 fw-semibold" style="color: var(--text-primary);">
                <i class="fas fa-history me-2"></i>10 Thông Báo Gần Nhất
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="15%">Thời gian</th>
                        <th width="15%">Loại</th>
                        <th width="40%">Tiêu đề</th>
                        <th width="30%">Gửi cho</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $recent = $conn->query("SELECT tb.*, kh.HoTen FROM ThongBao tb 
                                            LEFT JOIN KhachHang kh ON tb.MaKH = kh.MaKH 
                                            ORDER BY tb.NgayGui DESC LIMIT 10");
                    if ($recent && $recent->num_rows > 0):
                        while($tb = $recent->fetch_assoc()): 
                            $loaiClass = match($tb['LoaiTB']) {
                                'KhuyenMai' => 'type-promo',
                                'DonHang' => 'type-order', 
                                'SanPhamMoi' => 'type-product',
                                default => 'type-system'
                            };
                            $loaiText = match($tb['LoaiTB']) {
                                'KhuyenMai' => 'Khuyến mãi',
                                'DonHang' => 'Đơn hàng',
                                'SanPhamMoi' => 'Sản phẩm mới',
                                default => 'Hệ thống'
                            };
                    ?>
                        <tr>
                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($tb['NgayGui'])) ?></small></td>
                            <td>
                                <span class="type-badge <?= $loaiClass ?>"><?= $loaiText ?></span>
                            </td>
                            <td>
                                <strong style="color: var(--text-primary);"><?= htmlspecialchars($tb['TieuDe']) ?></strong>
                            </td>
                            <td class="text-muted"><?= $tb['HoTen'] ?? 'Tất cả khách' ?></td>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Chưa có thông báo nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleKhachHang(val) {
    document.getElementById('khachhang_select').style.display = val === 'mot' ? 'block' : 'none';
}
</script>

<?php include 'footer.php'; ?>
