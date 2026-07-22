<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/SanPham.php";
require_once "../app/models/TacGia.php";
require_once "../app/models/SanPhamTacGia.php";

$model = new SanPham();
$tgModel = new TacGia();
$sptgModel = new SanPhamTacGia();

$successGan = false;
$errorsGan = [];

// Xử lý gán tác giả
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gan_tacgia'])) {
    $maSP = (int)$_POST['maSP'];
    $maTacGia = (int)$_POST['maTacGia'];
    $vaiTro = trim($_POST['vaiTro']) ?: 'Tác giả';
    
    // Kiểm tra trùng
    $stmt = $conn->prepare("SELECT 1 FROM SanPham_TacGia WHERE MaSP = ? AND MaTacGia = ?");
    $stmt->bind_param("ii", $maSP, $maTacGia);
    $stmt->execute();
    $check = $stmt->get_result();
    if ($check->num_rows == 0) {
        if ($sptgModel->insert($maSP, $maTacGia, $vaiTro)) {
            $successGan = true;
        } else {
            $errorsGan[] = "Gán tác giả thất bại.";
        }
    } else {
        $errorsGan[] = "Tác giả này đã được gán rồi!";
    }
}

$danhSachSanPham = $model->getAll();
$danhSachTacGia = $tgModel->getAll();
?>

<?php include 'sidebar.php'; ?>

<style>
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

    .quantity-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .quantity-high { background: #d1fae5; color: #065f46; }
    .quantity-medium { background: #fef3c7; color: #92400e; }
    .quantity-low { background: #fee2e2; color: #991b1b; }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Sản Phẩm
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong><?= count($danhSachSanPham) ?></strong> sản phẩm</p>
        </div>
        <a href="sanpham_them.php" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-plus me-2"></i>Thêm sản phẩm mới
        </a>
    </div>

    <?php if (!empty($danhSachSanPham)): ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Tên sách</th>
                            <th width="10%">Giá</th>
                            <th width="8%">Số lượng</th>
                            <th width="12%">Danh mục</th>
                            <th width="12%">NXB</th>
                            <th width="10%">Hình ảnh</th>
                            <th width="13%">Tác giả</th>
                            <th width="15%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachSanPham as $i => $sp): ?>
                            <tr>
                                <td class="text-muted fw-semibold"><?= $i + 1 ?></td>
                                <td>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars($sp['TenSP']) ?></strong>
                                </td>
                                <td>
                                    <strong style="color: var(--text-primary);">
                                        <?= number_format($sp['DonGia']) ?>₫
                                    </strong>
                                </td>
                                <td>
                                    <span class="quantity-badge <?= $sp['SoLuong'] > 10 ? 'quantity-high' : ($sp['SoLuong'] > 0 ? 'quantity-medium' : 'quantity-low') ?>">
                                        <?= $sp['SoLuong'] ?>
                                    </span>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($sp['TenDM'] ?? 'Chưa có') ?></td>
                                <td class="text-muted"><?= htmlspecialchars($sp['TenNXB'] ?? 'Chưa có') ?></td>
                                <td>
                                    <?php if (!empty($sp['HinhAnh'])): ?>
                                        <img src="../assets/images/products/<?= htmlspecialchars($sp['HinhAnh']) ?>"
                                             width="60" height="60" class="rounded" style="object-fit: cover; border: 1px solid var(--border-color);">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center rounded" style="width:60px;height:60px;background: var(--bg-light);border: 1px solid var(--border-color);">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($sp['TacGia'])): ?>
                                        <small class="text-muted"><?= htmlspecialchars($sp['TacGia']) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa gán</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="sanpham_sua.php?id=<?= $sp['MaSP'] ?>"
                                           class="btn btn-warning btn-action"
                                           title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="sanpham_xoa.php?id=<?= $sp['MaSP'] ?>"
                                           class="btn btn-action"
                                           style="background: #fee2e2; color: #991b1b; border-color: #fecaca;"
                                           onclick="return confirm('Xóa sách \"<?= addslashes($sp['TenSP']) ?>\" thật không?')"
                                           title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <button type="button"
                                           class="btn btn-info btn-action"
                                           title="Gán tác giả"
                                           onclick="openModalGanTacGia(<?= $sp['MaSP'] ?>, <?= json_encode($sp['TenSP'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="table-card text-center py-5">
            <i class="fas fa-box-open" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có sản phẩm nào</h5>
            <p class="text-muted mb-3">Hãy thêm sản phẩm đầu tiên ngay bây giờ!</p>
            <a href="sanpham_them.php" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-plus me-2"></i>Thêm sản phẩm
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Gán Tác Giả -->
<div class="modal fade" id="modalGanTacGia" tabindex="-1" aria-labelledby="modalGanTacGiaLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalGanTacGiaLabel">
                    <i class="fas fa-user-plus me-2 text-info"></i>Gán Tác Giả Cho Sản Phẩm
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <?php if ($successGan): ?>
                    <div class="alert alert-success d-flex align-items-center mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <span>Gán tác giả thành công!</span>
                    </div>
                    <script>
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    </script>
                <?php else: ?>
                    <?php if (!empty($errorsGan)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errorsGan as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form method="post" id="formGanTacGia">
                        <input type="hidden" name="maSP" id="inputMaSP" value="">
                        <input type="hidden" name="gan_tacgia" value="1">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Sản phẩm</label>
                            <input type="text" class="form-control form-control-lg" id="inputTenSP" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="inputMaTacGia" class="form-label fw-semibold text-dark">Chọn tác giả <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="inputMaTacGia" name="maTacGia" required>
                                <option value="">-- Chọn tác giả --</option>
                                <?php 
                                $danhSachTacGia->data_seek(0);
                                while ($tg = $danhSachTacGia->fetch_assoc()): 
                                ?>
                                    <option value="<?= $tg['MaTacGia'] ?>">
                                        <?= htmlspecialchars($tg['TenTacGia']) ?>
                                        <?= !empty($tg['QuocTich']) ? " (" . htmlspecialchars($tg['QuocTich']) . ")" : "" ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="inputVaiTro" class="form-label fw-semibold text-dark">Vai trò</label>
                            <input type="text" class="form-control form-control-lg" id="inputVaiTro" name="vaiTro" 
                                   value="Tác giả" placeholder="VD: Biên dịch, Minh họa...">
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Hủy
                            </button>
                            <button type="submit" class="btn btn-info btn-lg shadow-sm">
                                <i class="fas fa-user-plus me-2"></i>Gán tác giả
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
    }
    .modal-content {
        max-width: 700px;
    }
    .form-control:focus {
        border-color: #0dcaf0;
        box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.15);
    }
</style>

<script>
    function openModalGanTacGia(maSP, tenSP) {
        document.getElementById('inputMaSP').value = maSP || '';
        document.getElementById('inputTenSP').value = tenSP || '';
        document.getElementById('inputMaTacGia').value = '';
        document.getElementById('inputVaiTro').value = 'Tác giả';
        document.getElementById('formGanTacGia').reset();
        document.getElementById('inputMaSP').value = maSP || '';
        document.getElementById('inputTenSP').value = tenSP || '';
        document.getElementById('inputVaiTro').value = 'Tác giả';
        const modal = new bootstrap.Modal(document.getElementById('modalGanTacGia'));
        modal.show();
    }
    
    // Close modal when clicking outside
    document.getElementById('modalGanTacGia').addEventListener('click', function(e) {
        if (e.target === this) {
            const modal = bootstrap.Modal.getInstance(this);
            modal.hide();
        }
    });
</script>

<?php include 'footer.php'; ?>
