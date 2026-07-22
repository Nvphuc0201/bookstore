<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";

// ================= XỬ LÝ THÊM KHUYẾN MÃI =================
if (isset($_POST['them'])) {
    $ten       = trim($_POST['ten']);
    $phantram  = (float)$_POST['phantram'];
    $ngaybd    = $_POST['ngaybd'];
    $ngaykt    = $_POST['ngaykt'];
    $dieukien  = trim($_POST['dieukien'] ?? '');
    $toithieu  = (float)str_replace(['.', ' '], '', $_POST['toithieu']);
    $loai      = $_POST['loai'];
    $madm      = ($loai == 'DanhMuc' && !empty($_POST['madm'])) ? (int)$_POST['madm'] : null;

    $stmt = $conn->prepare("INSERT INTO KhuyenMai 
        (TenKM, PhanTramGiam, NgayBatDau, NgayKetThuc, DieuKien, DieuKienToiThieu, LoaiKM, MaDM) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sdsssdsi", $ten, $phantram, $ngaybd, $ngaykt, $dieukien, $toithieu, $loai, $madm);
    $stmt->execute();
    echo "<script>alert('Tạo khuyến mãi thành công!'); window.location='khuyenmai.php';</script>";
    require_once "../includes/gui_thongbao.php";

    $link = "khuyenmai.php";
    $tieu_de = "Ưu đãi mới: " . $ten;
    $noi_dung = "Giảm {$phantram}% từ " . date('d/m/Y', strtotime($ngaybd)) . 
                " đến " . date('d/m/Y', strtotime($ngaykt)) . 
                ($toithieu > 0 ? " (đơn từ " . number_format($toithieu) . "₫)" : "");

    guiThongBaoTatCa($tieu_de, $noi_dung, 'KhuyenMai', $link);
}

// Xóa
if (isset($_GET['xoa'])) {
    $id = (int)$_GET['xoa'];
    $stmt = $conn->prepare("DELETE FROM KhuyenMai WHERE MaKM = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: khuyenmai.php");
    exit();
}

$list = $conn->query("SELECT km.*, dm.TenDM FROM KhuyenMai km 
                      LEFT JOIN DanhMuc dm ON km.MaDM = dm.MaDM 
                      ORDER BY km.NgayBatDau DESC");
$danhmuc_list = $conn->query("SELECT MaDM, TenDM FROM DanhMuc ORDER BY TenDM");
?>

<?php include 'sidebar.php'; ?>

<style>
    .promo-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        transition: all 0.2s;
        overflow: hidden;
    }

    .promo-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-color: var(--text-secondary);
    }

    .promo-card.active {
        border-color: #10b981;
        border-width: 2px;
    }

    .promo-header {
        background: var(--bg-light);
        border-bottom: 1px solid var(--border-color);
        padding: 16px;
    }

    .promo-percent {
        background: #fee2e2;
        color: #991b1b;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-upcoming { background: #e2e8f0; color: var(--text-secondary); }
    .status-active { background: #d1fae5; color: #065f46; }
    .status-ended { background: #f3f4f6; color: var(--text-secondary); }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        border: 1px solid var(--border-color);
        transition: all 0.2s;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .modal-content {
        border: 1px solid var(--border-color);
        border-radius: 12px;
    }

    .modal-header {
        background: var(--bg-light);
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Khuyến Mãi
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong><?= $list->num_rows ?></strong> chương trình</p>
        </div>
        <button class="btn" style="background: var(--text-primary); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;" data-bs-toggle="modal" data-bs-target="#modalThem">
            <i class="fas fa-plus me-2"></i>Tạo khuyến mãi mới
        </button>
    </div>

    <?php if ($list->num_rows > 0): ?>
        <div class="row g-3">
            <?php while ($km = $list->fetch_assoc()): 
                $today = date('Y-m-d');
                if ($today < $km['NgayBatDau']) {
                    $statusClass = 'status-upcoming';
                    $statusText = 'Sắp diễn ra';
                } elseif ($today > $km['NgayKetThuc']) {
                    $statusClass = 'status-ended';
                    $statusText = 'Đã kết thúc';
                } else {
                    $statusClass = 'status-active';
                    $statusText = 'Đang áp dụng';
                }
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="promo-card <?= $statusText == 'Đang áp dụng' ? 'active' : '' ?>">
                        <div class="promo-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold" style="color: var(--text-primary);">
                                <?= htmlspecialchars($km['TenKM']) ?>
                            </h5>
                            <span class="promo-percent"><?= $km['PhanTramGiam'] ?>%</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-2">
                                <span class="badge" style="background: #e2e8f0; color: var(--text-secondary);">
                                    <?= $km['LoaiKM'] == 'DanhMuc' ? 'Theo danh mục' : 'Toàn đơn hàng' ?>
                                </span>
                                <?php if ($km['LoaiKM'] == 'DanhMuc'): ?>
                                    <span class="text-muted small ms-2">→ <?= htmlspecialchars($km['TenDM'] ?? 'Không xác định') ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="mb-2 text-muted small">
                                <i class="far fa-calendar-alt me-1"></i>
                                <?= date('d/m/Y', strtotime($km['NgayBatDau'])) ?> → <?= date('d/m/Y', strtotime($km['NgayKetThuc'])) ?>
                            </p>
                            <?php if ($km['DieuKienToiThieu'] > 0): ?>
                                <p class="mb-2">
                                    <span class="badge" style="background: #fef3c7; color: #92400e;">
                                        <i class="fas fa-coins me-1"></i>Đơn từ <?= number_format($km['DieuKienToiThieu']) ?>₫
                                    </span>
                                </p>
                            <?php endif; ?>
                            <?php if ($km['DieuKien']): ?>
                                <p class="small text-muted mb-2"><strong>Ghi chú:</strong> <?= htmlspecialchars($km['DieuKien']) ?></p>
                            <?php endif; ?>
                            <div class="mb-3">
                                <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="khuyenmai_sua.php?id=<?= $km['MaKM'] ?>" 
                                   class="btn btn-action flex-fill"
                                   style="background: #fef3c7; color: #92400e; border-color: #fde68a;">
                                    <i class="fas fa-edit me-1"></i>Sửa
                                </a>
                                <a href="?xoa=<?= $km['MaKM'] ?>" 
                                   class="btn btn-action flex-fill"
                                   style="background: #fee2e2; color: #991b1b; border-color: #fecaca;"
                                   onclick="return confirm('Xóa khuyến mãi \"<?= addslashes($km['TenKM']) ?>\"?')">
                                    <i class="fas fa-trash me-1"></i>Xóa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="table-card text-center py-5">
            <i class="fas fa-gift" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có chương trình khuyến mãi nào</h5>
            <p class="text-muted mb-3">Hãy tạo ưu đãi đầu tiên để thu hút khách hàng!</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Tạo Khuyến Mãi -->
<div class="modal fade" id="modalThem">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" style="color: var(--text-primary);">
                    <i class="fas fa-gift me-2"></i>Tạo Khuyến Mãi Mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Tên chương trình</label>
                        <input type="text" name="ten" class="form-control" placeholder="VD: Black Friday" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Phần trăm giảm</label>
                        <input type="number" name="phantram" min="1" max="90" class="form-control text-center" placeholder="%" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Loại</label>
                        <select name="loai" class="form-select" onchange="document.getElementById('danhmuc_row').style.display = (this.value === 'DanhMuc') ? 'block' : 'none'">
                            <option value="ToanDon">Toàn đơn hàng</option>
                            <option value="DanhMuc">Theo danh mục</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="danhmuc_row" style="display:none;">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Danh mục</label>
                        <select name="madm" class="form-select">
                            <?php $danhmuc_list->data_seek(0); while($dm = $danhmuc_list->fetch_assoc()): ?>
                                <option value="<?= $dm['MaDM'] ?>"><?= htmlspecialchars($dm['TenDM']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Ngày bắt đầu</label>
                        <input type="date" name="ngaybd" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Ngày kết thúc</label>
                        <input type="date" name="ngaykt" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Đơn tối thiểu</label>
                        <div class="input-group">
                            <input type="text" name="toithieu" class="form-control text-end" placeholder="300000" value="0">
                            <span class="input-group-text">₫</span>
                        </div>
                        <small class="text-muted">0 = áp dụng mọi đơn hàng</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">Ghi chú</label>
                        <input type="text" name="dieukien" class="form-control" placeholder="Ghi chú thêm (không bắt buộc)">
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn" style="background: var(--bg-light); color: var(--text-secondary); border: 1px solid var(--border-color);" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" name="them" class="btn" style="background: var(--text-primary); color: #fff; border: none;">
                    <i class="fas fa-check me-2"></i>Tạo ngay
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
