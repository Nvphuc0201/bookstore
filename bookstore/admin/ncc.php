<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/NhaCungCap.php";

$model = new NhaCungCap();
$errors = [];
$success = false;
$editId = null;
$editData = null;

// Xử lý POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ten'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $diachi = trim($_POST['diachi'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($ten === '') {
        $errors[] = "Tên nhà cung cấp không được để trống.";
    }
    
    if (empty($errors)) {
        if ($id > 0) {
            // Sửa
            $ok = $model->update($id, $ten, $sdt, $diachi, $email);
            if ($ok) {
                $success = true;
            } else {
                $errors[] = "Cập nhật thất bại.";
            }
        } else {
            // Thêm mới
            $ok = $model->insert($ten, $sdt, $diachi, $email);
            if ($ok) {
                $success = true;
            } else {
                $errors[] = "Lưu dữ liệu thất bại.";
            }
        }
    }
}

// Lấy dữ liệu để sửa (nếu có)
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    if ($editId > 0) {
        $editData = $model->getById($editId);
        if (!$editData) {
            $editId = null;
            $editData = null;
        }
    }
}

$result = $model->getAll();
$totalNCC = $result->num_rows;
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
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Nhà Cung Cấp
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong><?= $totalNCC ?></strong> nhà cung cấp</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNCC" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp
        </button>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Mã NCC</th>
                            <th width="20%">Tên nhà cung cấp</th>
                            <th width="15%">Số điện thoại</th>
                            <th width="25%">Địa chỉ</th>
                            <th width="15%">Email</th>
                            <th width="10%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stt = 1;
                        while ($row = $result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td class="text-muted fw-semibold"><?= $stt++ ?></td>
                                <td><strong style="color: var(--text-primary);">#<?= str_pad($row['MaNCC'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                                <td>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars($row['TenNCC']) ?></strong>
                                </td>
                                <td>
                                    <?php if (!empty($row['SDT'])): ?>
                                        <a href="tel:<?= htmlspecialchars($row['SDT']) ?>" class="text-decoration-none text-muted">
                                            <?= htmlspecialchars($row['SDT']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($row['DiaChi'] ?? 'Chưa cập nhật') ?></td>
                                <td>
                                    <?php if (!empty($row['Email'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($row['Email']) ?>" class="text-decoration-none text-muted">
                                            <?= htmlspecialchars($row['Email']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                           class="btn btn-warning btn-action"
                                           title="Sửa"
                                           onclick="openModalSua(<?= $row['MaNCC'] ?>, <?= json_encode($row['TenNCC'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['SDT'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['DiaChi'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['Email'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="ncc_xoa.php?id=<?= $row['MaNCC'] ?>"
                                           class="btn btn-action"
                                           style="background: #fee2e2; color: #991b1b; border-color: #fecaca;"
                                           onclick="return confirm('Xóa nhà cung cấp \"<?= addslashes($row['TenNCC']) ?>\"?\nTất cả lịch sử nhập hàng sẽ bị ảnh hưởng!')"
                                           title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
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
            <i class="fas fa-truck-loading" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có nhà cung cấp nào</h5>
            <p class="text-muted mb-3">Thêm nhà cung cấp đầu tiên để bắt đầu nhập hàng!</p>
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNCC" onclick="openModalThem()">
                <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Thêm/Sửa Nhà Cung Cấp -->
<div class="modal fade" id="modalNCC" tabindex="-1" aria-labelledby="modalNCCLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalNCCLabel">
                    <i class="fas fa-truck me-2 text-success"></i><span id="modalTitle">Thêm Nhà Cung Cấp Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <span><?= $editId ? 'Cập nhật' : 'Thêm' ?> nhà cung cấp thành công!</span>
                    </div>
                    <script>
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    </script>
                <?php else: ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form method="post" id="formNCC">
                        <input type="hidden" name="id" id="inputId" value="">
                        <div class="mb-3">
                            <label for="inputTen" class="form-label fw-semibold text-dark">Tên nhà cung cấp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="inputTen" name="ten" required 
                                   value="<?= htmlspecialchars($editData['TenNCC'] ?? '') ?>" 
                                   placeholder="Nhập tên nhà cung cấp">
                        </div>
                        <div class="mb-3">
                            <label for="inputSDT" class="form-label fw-semibold text-dark">Số điện thoại</label>
                            <input type="text" class="form-control form-control-lg" id="inputSDT" name="sdt" 
                                   value="<?= htmlspecialchars($editData['SDT'] ?? '') ?>" 
                                   placeholder="Nhập số điện thoại">
                        </div>
                        <div class="mb-3">
                            <label for="inputDiaChi" class="form-label fw-semibold text-dark">Địa chỉ</label>
                            <input type="text" class="form-control form-control-lg" id="inputDiaChi" name="diachi" 
                                   value="<?= htmlspecialchars($editData['DiaChi'] ?? '') ?>" 
                                   placeholder="Nhập địa chỉ">
                        </div>
                        <div class="mb-3">
                            <label for="inputEmail" class="form-label fw-semibold text-dark">Email</label>
                            <input type="email" class="form-control form-control-lg" id="inputEmail" name="email" 
                                   value="<?= htmlspecialchars($editData['Email'] ?? '') ?>" 
                                   placeholder="Nhập email">
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Hủy
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-save me-2"></i><span id="btnSubmitText">Thêm mới</span>
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
        max-width: 600px;
    }
    .form-control:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15);
    }
</style>

<script>
    function openModalThem() {
        document.getElementById('modalTitle').textContent = 'Thêm Nhà Cung Cấp Mới';
        document.getElementById('btnSubmitText').textContent = 'Thêm mới';
        document.getElementById('inputId').value = '';
        document.getElementById('inputTen').value = '';
        document.getElementById('inputSDT').value = '';
        document.getElementById('inputDiaChi').value = '';
        document.getElementById('inputEmail').value = '';
        document.getElementById('formNCC').reset();
    }
    
    function openModalSua(id, ten, sdt, diachi, email) {
        document.getElementById('modalTitle').textContent = 'Sửa Nhà Cung Cấp';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('inputId').value = id || '';
        document.getElementById('inputTen').value = ten || '';
        document.getElementById('inputSDT').value = sdt || '';
        document.getElementById('inputDiaChi').value = diachi || '';
        document.getElementById('inputEmail').value = email || '';
        const modal = new bootstrap.Modal(document.getElementById('modalNCC'));
        modal.show();
    }
    
    // Auto open modal if edit mode
    <?php if ($editId): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalNCC'));
        modal.show();
    });
    <?php endif; ?>
    
    // Close modal when clicking outside
    document.getElementById('modalNCC').addEventListener('click', function(e) {
        if (e.target === this) {
            const modal = bootstrap.Modal.getInstance(this);
            modal.hide();
        }
    });
</script>

<?php include 'footer.php'; ?>
