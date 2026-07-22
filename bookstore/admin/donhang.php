<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/DonHang.php";

$model = new DonHang();

// Xử lý filter và sort
$filterStatus = $_GET['status'] ?? 'all';
$sortBy = $_GET['sort'] ?? 'newest';

// Lấy danh sách đơn hàng
if ($filterStatus === 'pending') {
    $list = $model->getPendingOrders();
} elseif ($filterStatus !== 'all') {
    $orderBy = $sortBy === 'newest' ? 'NgayDat DESC' : 'NgayDat ASC';
    $list = $model->getByStatus($filterStatus, $orderBy);
} else {
    $orderBy = $sortBy === 'newest' ? 'NgayDat DESC' : 'NgayDat ASC';
    $list = $model->getByStatus(null, $orderBy);
}

// Đếm số đơn theo trạng thái
$tongDon = $conn->query("SELECT COUNT(*) FROM DonHang")->fetch_row()[0];
$donChoXacNhan = $conn->query("SELECT COUNT(*) FROM DonHang WHERE TrangThai = 'ChoXacNhan'")->fetch_row()[0];
$donDangGiao = $conn->query("SELECT COUNT(*) FROM DonHang WHERE TrangThai = 'DangGiao'")->fetch_row()[0];
$donDaGiao = $conn->query("SELECT COUNT(*) FROM DonHang WHERE TrangThai = 'DaGiao'")->fetch_row()[0];
$donDaHuy = $conn->query("SELECT COUNT(*) FROM DonHang WHERE TrangThai = 'DaHuy'")->fetch_row()[0];
?>

<?php include 'sidebar.php'; ?>

<style>
    .filter-section {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .filter-btn {
        padding: 8px 16px;
        border: 1px solid var(--border-color);
        background: var(--bg-white);
        color: var(--text-secondary);
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }

    .filter-btn:hover {
        background: var(--bg-light);
        color: var(--text-primary);
        border-color: var(--text-secondary);
    }

    .filter-btn.active {
        background: var(--text-primary);
        color: #fff;
        border-color: var(--text-primary);
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

    .status-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-shipping {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-delivered {
        background: #d1fae5;
        color: #065f46;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

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

    /* Overlay chi tiết đơn hàng */
    .order-overlay {
        position: fixed;
        inset: 0;
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .order-overlay-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15,23,42,0.45);
    }
    .order-overlay-content {
        position: relative;
        max-width: 900px;
        width: 95%;
        max-height: 90vh;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        overflow: hidden;
        z-index: 1;
        display: flex;
        flex-direction: column;
    }
    .order-overlay-body {
        padding: 20px 24px;
        overflow-y: auto;
    }
    .order-overlay-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--bg-light);
    }
    .order-overlay-close {
        border: none;
        background: transparent;
        font-size: 1.2rem;
        cursor: pointer;
        color: var(--text-secondary);
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Đơn Hàng
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong><?= $tongDon ?></strong> đơn hàng</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="fw-semibold" style="color: var(--text-primary);">Lọc theo:</div>
            
            <a href="?status=all&sort=<?= $sortBy ?>" 
               class="filter-btn <?= $filterStatus === 'all' ? 'active' : '' ?>">
                Tất cả (<?= $tongDon ?>)
            </a>
            
            <a href="?status=pending&sort=<?= $sortBy ?>" 
               class="filter-btn <?= $filterStatus === 'pending' ? 'active' : '' ?>">
                Cần xử lý (<?= $donChoXacNhan ?>)
            </a>
            
            <a href="?status=DangGiao&sort=<?= $sortBy ?>" 
               class="filter-btn <?= $filterStatus === 'DangGiao' ? 'active' : '' ?>">
                Đang giao (<?= $donDangGiao ?>)
            </a>
            
            <a href="?status=DaGiao&sort=<?= $sortBy ?>" 
               class="filter-btn <?= $filterStatus === 'DaGiao' ? 'active' : '' ?>">
                Đã giao (<?= $donDaGiao ?>)
            </a>
            
            <a href="?status=DaHuy&sort=<?= $sortBy ?>" 
               class="filter-btn <?= $filterStatus === 'DaHuy' ? 'active' : '' ?>">
                Đã hủy (<?= $donDaHuy ?>)
            </a>

            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="text-muted small">Sắp xếp:</span>
                <a href="?status=<?= $filterStatus ?>&sort=newest" 
                   class="filter-btn <?= $sortBy === 'newest' ? 'active' : '' ?>">
                    <i class="fas fa-sort-amount-down me-1"></i> Mới nhất
                </a>
                <a href="?status=<?= $filterStatus ?>&sort=oldest" 
                   class="filter-btn <?= $sortBy === 'oldest' ? 'active' : '' ?>">
                    <i class="fas fa-sort-amount-up me-1"></i> Cũ nhất
                </a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <?php if ($list && $list->num_rows > 0): ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Mã đơn</th>
                            <th width="15%">Ngày đặt</th>
                            <th width="18%">Khách hàng</th>
                            <th width="12%">Tổng tiền</th>
                            <th width="12%">Trạng thái</th>
                            <th width="10%">Thanh toán</th>
                            <th width="18%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stt = 1;
                        while ($r = $list->fetch_assoc()): 
                            // Xử lý màu trạng thái
                            $statusClass = match($r['TrangThai']) {
                                'ChoXacNhan' => 'status-pending',
                                'DangGiao'   => 'status-shipping',
                                'DaGiao'     => 'status-delivered',
                                'DaHuy'      => 'status-cancelled',
                                default      => 'status-pending'
                            };
                            $statusText = match($r['TrangThai']) {
                                'ChoXacNhan' => 'Chờ xác nhận',
                                'DangGiao'   => 'Đang giao',
                                'DaGiao'     => 'Đã giao',
                                'DaHuy'      => 'Đã hủy',
                                default      => $r['TrangThai']
                            };
                        ?>
                            <tr>
                                <td class="text-muted fw-semibold"><?= $stt++ ?></td>
                                <td><strong style="color: var(--text-primary);">#<?= str_pad($r['MaDH'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                                <td>
                                    <div style="color: var(--text-primary); font-weight: 500;"><?= date('d/m/Y', strtotime($r['NgayDat'])) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($r['NgayDat'])) ?></small>
                                </td>
                                <td>
                                    <div style="color: var(--text-primary); font-weight: 500;"><?= htmlspecialchars($r['HoTen'] ?? 'Khách vãng lai') ?></div>
                                    <?php if (!empty($r['SDT'])): ?>
                                        <small class="text-muted"><i class="fas fa-phone me-1"></i><?= htmlspecialchars($r['SDT']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color: var(--text-primary); font-size: 1rem;">
                                        <?= number_format($r['TongTien'], 0, ',', '.') ?>₫
                                    </strong>
                                </td>
                                <td>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background: #e2e8f0; color: var(--text-secondary);">
                                        <?= $r['PhuongThucThanhToan'] == 'TienMat' ? 'Tiền mặt' : 'Chuyển khoản' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                           class="btn btn-action" 
                                           style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;"
                                           title="Xem chi tiết"
                                           onclick="openOrderDetail(<?= $r['MaDH'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="donhang_sua.php?id=<?= $r['MaDH'] ?>"
                                           class="btn btn-action"
                                           style="background: #fef3c7; color: #92400e; border-color: #fde68a;"
                                           title="Cập nhật trạng thái">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="donhang_xoa.php?id=<?= $r['MaDH'] ?>"
                                           class="btn btn-action"
                                           style="background: #fee2e2; color: #991b1b; border-color: #fecaca;"
                                           onclick="return confirm('⚠️ Xóa đơn hàng #<?= $r['MaDH'] ?>?\nTồn kho sẽ được hoàn lại nếu cần.')"
                                           title="Xóa đơn">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="table-card text-center py-5">
            <i class="fas fa-receipt" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có đơn hàng nào</h5>
            <p class="text-muted mb-0">Hệ thống đang chờ đơn hàng đầu tiên từ khách...</p>
        </div>
    <?php endif; ?>
</div>

<!-- Overlay chi tiết đơn hàng -->
<div id="orderDetailOverlay" class="order-overlay d-none">
    <div class="order-overlay-backdrop" onclick="closeOrderDetail()"></div>
    <div class="order-overlay-content">
        <div class="order-overlay-header">
            <h5 class="mb-0 fw-semibold" style="color: var(--text-primary);">
                Chi tiết đơn hàng
            </h5>
            <button type="button" class="order-overlay-close" onclick="closeOrderDetail()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="orderDetailBody" class="order-overlay-body">
            <div class="text-center py-4 text-muted small">
                Đang tải dữ liệu đơn hàng...
            </div>
        </div>
    </div>
</div>

<script>
    function openOrderDetail(id) {
        const overlay = document.getElementById('orderDetailOverlay');
        const body = document.getElementById('orderDetailBody');
        if (!overlay || !body) return;

        overlay.classList.remove('d-none');
        body.innerHTML = '<div class="text-center py-4 text-muted small">Đang tải dữ liệu đơn hàng...</div>';
        document.body.style.overflow = 'hidden';

        fetch('donhang_chitiet.php?id=' + encodeURIComponent(id) + '&ajax=1')
            .then(res => res.text())
            .then(html => {
                body.innerHTML = html;
            })
            .catch(() => {
                body.innerHTML = '<div class="text-danger small py-4 text-center">Không thể tải dữ liệu đơn hàng. Vui lòng thử lại.</div>';
            });
    }

    function closeOrderDetail() {
        const overlay = document.getElementById('orderDetailOverlay');
        const body = document.getElementById('orderDetailBody');
        if (!overlay || !body) return;
        overlay.classList.add('d-none');
        body.innerHTML = '';
        document.body.style.overflow = '';
    }
</script>

<?php include 'footer.php'; ?>
