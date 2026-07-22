<?php
require_once '../app/middleware/admin.php';
require_once '../app/config/db.php';

// Xử lý thời gian
$nam   = isset($_GET['nam']) ? (int)$_GET['nam'] : date('Y');
$thang = isset($_GET['thang']) ? (int)$_GET['thang'] : 0;

// 1. DOANH THU THEO THÁNG
$doanhthu_thang = [];
for ($i = 1; $i <= 12; $i++) {
    $sql = "SELECT COALESCE(SUM(TongTien),0) AS tong 
            FROM DonHang 
            WHERE YEAR(NgayDat)=? 
              AND MONTH(NgayDat)=?
              AND TrangThai IN ('DangGiao','DaGiao')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $nam, $i);
    $stmt->execute();
    $doanhthu_thang[] = $stmt->get_result()->fetch_assoc()['tong'];
}

// 2. NHẬP HÀNG THEO THÁNG
$nhaphang_thang = [];
for ($i = 1; $i <= 12; $i++) {
    $sql = "SELECT COALESCE(SUM(TongTienNhap),0) AS tong 
            FROM LichSuNhapHang
            WHERE YEAR(NgayNhap)=? 
              AND MONTH(NgayNhap)=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $nam, $i);
    $stmt->execute();
    $nhaphang_thang[] = $stmt->get_result()->fetch_assoc()['tong'];
}

// 3. TỔNG DOANH THU – NHẬP – LỢI NHUẬN
$tong_doanh_thu = array_sum($doanhthu_thang);
$tong_nhap      = array_sum($nhaphang_thang);
$loi_nhuan      = $tong_doanh_thu - $tong_nhap;

// 4. TOP 5 SẢN PHẨM BÁN CHẠY
$sql_topban = "
SELECT sp.TenSP, SUM(ct.SoLuong) AS SoLuongBan
FROM ChiTietDonHang ct
JOIN DonHang dh ON ct.MaDH = dh.MaDH
JOIN SanPham sp ON ct.MaSP = sp.MaSP
WHERE dh.TrangThai IN ('DangGiao','DaGiao')
GROUP BY ct.MaSP
ORDER BY SoLuongBan DESC
LIMIT 5";
$top_ban = $conn->query($sql_topban);

// 5. TOP 5 SẢN PHẨM NHẬP NHIỀU
$sql_topnhap = "
SELECT sp.TenSP, SUM(ct.SoLuongNhap) AS SoLuongNhap
FROM ChiTietNhapHang ct
JOIN SanPham sp ON ct.MaSP = sp.MaSP
GROUP BY ct.MaSP
ORDER BY SoLuongNhap DESC
LIMIT 5";
$top_nhap = $conn->query($sql_topnhap);
?>

<?php include 'sidebar.php'; ?>

<style>
    .stat-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        transition: all 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-color: var(--text-secondary);
    }

    .chart-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .chart-header {
        background: var(--bg-light);
        border-bottom: 1px solid var(--border-color);
        padding: 20px;
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

    .form-select {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 14px;
    }

    .form-select:focus {
        border-color: var(--text-primary);
        box-shadow: 0 0 0 3px rgba(30, 41, 59, 0.1);
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Báo Cáo Doanh Thu & Nhập Hàng
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Thống kê chi tiết theo năm</p>
        </div>
        <form method="get" class="d-flex align-items-center gap-2">
            <select name="nam" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <?php for($y=date('Y')-5; $y<=date('Y')+1; $y++): ?>
                    <option value="<?= $y ?>" <?= $y==$nam?'selected':'' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>

    <!-- Thống kê tổng -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 56px; height: 56px; background: #d1fae5; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="color: #065f46; font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted" style="font-size: 0.875rem;">Doanh thu năm <?= $nam ?></p>
                        <h3 class="mb-0 fw-semibold" style="color: var(--text-primary); font-size: 1.5rem;">
                            <?= number_format($tong_doanh_thu) ?>₫
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 56px; height: 56px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-truck" style="color: #92400e; font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted" style="font-size: 0.875rem;">Nhập hàng năm <?= $nam ?></p>
                        <h3 class="mb-0 fw-semibold" style="color: var(--text-primary); font-size: 1.5rem;">
                            <?= number_format($tong_nhap) ?>₫
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 56px; height: 56px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-coins" style="color: #1e40af; font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted" style="font-size: 0.875rem;">Lợi nhuận năm <?= $nam ?></p>
                        <h3 class="mb-0 fw-semibold" style="color: var(--text-primary); font-size: 1.5rem;">
                            <?= number_format($loi_nhuan) ?>₫
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="chart-card mb-4">
        <div class="chart-header">
            <h4 class="mb-0 fw-semibold" style="color: var(--text-primary);">
                <i class="fas fa-chart-bar me-2"></i>Biểu Đồ Doanh Thu & Nhập Hàng Theo Tháng
            </h4>
        </div>
        <div class="card-body p-4">
            <canvas id="chart1" height="100"></canvas>
        </div>
    </div>

    <!-- TOP Sản phẩm -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="table-card">
                <div class="chart-header">
                    <h5 class="mb-0 fw-semibold" style="color: var(--text-primary);">
                        <i class="fas fa-fire me-2"></i>TOP SẢN PHẨM BÁN CHẠY
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="10%">#</th>
                                <th>Tên sản phẩm</th>
                                <th width="25%" class="text-end">Số lượng bán</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stt = 1;
                            while($r = $top_ban->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td class="text-muted fw-semibold"><?= $stt++ ?></td>
                                    <td><strong style="color: var(--text-primary);"><?= htmlspecialchars($r['TenSP']) ?></strong></td>
                                    <td class="text-end">
                                        <span class="badge" style="background: #d1fae5; color: #065f46;">
                                            <?= number_format($r['SoLuongBan']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="table-card">
                <div class="chart-header">
                    <h5 class="mb-0 fw-semibold" style="color: var(--text-primary);">
                        <i class="fas fa-box me-2"></i>TOP SẢN PHẨM NHẬP NHIỀU
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="10%">#</th>
                                <th>Tên sản phẩm</th>
                                <th width="25%" class="text-end">Số lượng nhập</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stt = 1;
                            while($r = $top_nhap->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td class="text-muted fw-semibold"><?= $stt++ ?></td>
                                    <td><strong style="color: var(--text-primary);"><?= htmlspecialchars($r['TenSP']) ?></strong></td>
                                    <td class="text-end">
                                        <span class="badge" style="background: #fef3c7; color: #92400e;">
                                            <?= number_format($r['SoLuongNhap']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chart1'), {
    type: 'bar',
    data: {
        labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
        datasets: [
            {
                label: 'Doanh thu',
                data: <?= json_encode($doanhthu_thang) ?>,
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderColor: '#2563eb',
                borderWidth: 2
            },
            {
                label: 'Nhập hàng',
                data: <?= json_encode($nhaphang_thang) ?>,
                backgroundColor: 'rgba(146, 64, 14, 0.1)',
                borderColor: '#92400e',
                borderWidth: 2
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { 
                position: 'top',
                labels: {
                    color: '#1e293b',
                    font: { size: 12, weight: 500 }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + '₫';
                    },
                    color: '#64748b'
                },
                grid: {
                    color: '#e2e8f0'
                }
            },
            x: {
                ticks: {
                    color: '#64748b'
                },
                grid: {
                    color: '#e2e8f0'
                }
            }
        }
    }
});
</script>

<?php include 'footer.php'; ?>
