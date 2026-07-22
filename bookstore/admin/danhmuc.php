<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/DanhMuc.php";

$model = new DanhMuc();
$errors = [];
$success = false;
$editId = null;
$editData = null;

// Xử lý POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = trim($_POST['ten'] ?? '');
    $mota = trim($_POST['mota'] ?? '');
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if (empty($ten)) {
        $errors[] = "Tên danh mục không được để trống.";
    }
    
    if (empty($errors)) {
        if ($id > 0) {
            // Sửa
            $model->update($id, $ten, $mota);
            $success = true;
        } else {
            // Thêm mới
            $model->insert($ten, $mota);
            $success = true;
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

$list = $model->getAll();
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
                Quản Lý Danh Mục
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong><?= $list->num_rows ?></strong> danh mục</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDanhMuc" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm danh mục mới
        </button>
    </div>

    <?php if ($list->num_rows > 0): ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="8%" class="text-center">#</th>
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th width="15%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stt = 1;
                        while ($row = $list->fetch_assoc()): 
                        ?>
                            <tr>
                                <td class="text-center text-muted fw-semibold"><?= $stt++ ?></td>
                                <td>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars($row['TenDM']) ?></strong>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <?= $row['MoTa'] ? nl2br(htmlspecialchars($row['MoTa'])) : '<em>Chưa có mô tả</em>' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                           class="btn btn-warning btn-action"
                                           title="Sửa"
                                           onclick="openModalSua(<?= $row['MaDM'] ?>, <?= json_encode($row['TenDM'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['MoTa'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="danhmuc_xoa.php?id=<?= $row['MaDM'] ?>"
                                           class="btn btn-action"
                                           style="background: #fee2e2; color: #991b1b; border-color: #fecaca;"
                                           onclick="return confirm('Xóa danh mục \"<?= addslashes($row['TenDM']) ?>\"?\n⚠️ Tất cả sách thuộc danh mục này sẽ bị mất danh mục!')"
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
            <i class="fas fa-folder-open" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có danh mục nào</h5>
            <p class="text-muted mb-3">Hãy thêm danh mục đầu tiên ngay!</p>
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDanhMuc" onclick="openModalThem()">
                <i class="fas fa-plus me-2"></i>Thêm danh mục
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Thêm/Sửa Danh Mục -->
<div class="modal fade" id="modalDanhMuc" tabindex="-1" aria-labelledby="modalDanhMucLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalDanhMucLabel">
                    <i class="fas fa-tags me-2 text-info"></i><span id="modalTitle">Thêm Danh Mục Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <span><?= $editId ? 'Cập nhật' : 'Thêm' ?> danh mục thành công!</span>
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
                    <form method="post" id="formDanhMuc">
                        <input type="hidden" name="id" id="inputId" value="">
                        <div class="mb-3">
                            <label for="inputTen" class="form-label fw-semibold text-dark">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="inputTen" name="ten" required 
                                   value="<?= htmlspecialchars($editData['TenDM'] ?? '') ?>" 
                                   placeholder="Nhập tên danh mục">
                        </div>
                        <div class="mb-3">
                            <label for="inputMota" class="form-label fw-semibold text-dark">Mô tả</label>
                            <textarea class="form-control" id="inputMota" name="mota" rows="4" 
                                      placeholder="Nhập mô tả danh mục (tùy chọn)"><?= htmlspecialchars($editData['MoTa'] ?? '') ?></textarea>
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
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
    }
</style>

<script>
    function openModalThem() {
        document.getElementById('modalTitle').textContent = 'Thêm Danh Mục Mới';
        document.getElementById('btnSubmitText').textContent = 'Thêm mới';
        document.getElementById('inputId').value = '';
        document.getElementById('inputTen').value = '';
        document.getElementById('inputMota').value = '';
        document.getElementById('formDanhMuc').reset();
    }
    
    function openModalSua(id, ten, mota) {
        document.getElementById('modalTitle').textContent = 'Sửa Danh Mục';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('inputId').value = id || '';
        document.getElementById('inputTen').value = ten || '';
        document.getElementById('inputMota').value = mota || '';
        const modal = new bootstrap.Modal(document.getElementById('modalDanhMuc'));
        modal.show();
    }
    
    // Auto open modal if edit mode
    <?php if ($editId): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalDanhMuc'));
        modal.show();
    });
    <?php endif; ?>
    
    // Close modal when clicking outside
    document.getElementById('modalDanhMuc').addEventListener('click', function(e) {
        if (e.target === this) {
            const modal = bootstrap.Modal.getInstance(this);
            modal.hide();
        }
    });
</script>

<?php include 'footer.php'; ?>
