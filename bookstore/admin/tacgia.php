<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/TacGia.php";

$model = new TacGia();
$errors = [];
$success = false;
$editId = null;
$editData = null;

// Xử lý POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = trim($_POST['ten'] ?? '');
    $ngaysinh = trim($_POST['ngaysinh'] ?? '');
    $quoctich = trim($_POST['quoctich'] ?? '');
    $mota = trim($_POST['mota'] ?? '');
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if (empty($ten)) {
        $errors[] = "Tên tác giả không được để trống.";
    }
    
    $anh = "";
    if (!empty($_FILES['anh']['name'])) {
        $anh = time() . "_" . $_FILES['anh']['name'];
        move_uploaded_file(
            $_FILES['anh']['tmp_name'],
            "../assets/images/tacgia/" . $anh
        );
    }
    
    if (empty($errors)) {
        if ($id > 0) {
            // Sửa
            $model->update($id, $ten, $ngaysinh, $quoctich, $mota, $anh);
            $success = true;
        } else {
            // Thêm mới
            $model->insert($ten, $ngaysinh, $quoctich, $mota, $anh);
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
$totalTacGia = $list->num_rows;
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
                Quản Lý Tác Giả
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong><?= $totalTacGia ?></strong> tác giả</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTacGia" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm tác giả mới
        </button>
    </div>

    <?php if ($list->num_rows > 0): ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Mã TG</th>
                            <th width="20%">Tên tác giả</th>
                            <th width="12%">Ngày sinh</th>
                            <th width="12%">Quốc tịch</th>
                            <th width="12%">Ảnh đại diện</th>
                            <th width="19%">Mô tả</th>
                            <th width="10%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stt = 1;
                        while ($row = $list->fetch_assoc()): 
                        ?>
                            <tr>
                                <td class="text-muted fw-semibold"><?= $stt++ ?></td>
                                <td><strong style="color: var(--text-primary);">#TG<?= str_pad($row['MaTacGia'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                                <td>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars($row['TenTacGia']) ?></strong>
                                </td>
                                <td>
                                    <?php if (!empty($row['NgaySinh']) && $row['NgaySinh'] !== '0000-00-00'): ?>
                                        <span class="text-muted"><?= date('d/m/Y', strtotime($row['NgaySinh'])) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: #e2e8f0; color: var(--text-secondary);">
                                        <?= htmlspecialchars($row['QuocTich'] ?? 'Không rõ') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($row['AnhDaiDien'])): ?>
                                        <img src="../assets/images/tacgia/<?= htmlspecialchars($row['AnhDaiDien']) ?>"
                                             width="60" height="60" class="rounded-circle" style="object-fit: cover; border: 1px solid var(--border-color);">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width:60px;height:60px;background: var(--bg-light);border: 1px solid var(--border-color);">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= $row['MoTa'] ? htmlspecialchars(mb_substr($row['MoTa'], 0, 50, 'UTF-8')) . '...' : '—' ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                           class="btn btn-warning btn-action"
                                           title="Sửa"
                                           onclick="openModalSua(<?= $row['MaTacGia'] ?>, <?= json_encode($row['TenTacGia'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['NgaySinh'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['QuocTich'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['MoTa'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['AnhDaiDien'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="tacgia_xoa.php?id=<?= $row['MaTacGia'] ?>"
                                           class="btn btn-action"
                                           style="background: #fee2e2; color: #991b1b; border-color: #fecaca;"
                                           onclick="return confirm('Xóa tác giả \"<?= addslashes($row['TenTacGia']) ?>\"?\nTất cả sách liên quan sẽ mất thông tin tác giả!')"
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
            <i class="fas fa-user-slash" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có tác giả nào</h5>
            <p class="text-muted mb-3">Thêm tác giả đầu tiên để hoàn thiện thông tin sách!</p>
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTacGia" onclick="openModalThem()">
                <i class="fas fa-plus me-2"></i>Thêm tác giả
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Thêm/Sửa Tác Giả -->
<div class="modal fade" id="modalTacGia" tabindex="-1" aria-labelledby="modalTacGiaLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalTacGiaLabel">
                    <i class="fas fa-user-pen me-2 text-warning"></i><span id="modalTitle">Thêm Tác Giả Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <span><?= $editId ? 'Cập nhật' : 'Thêm' ?> tác giả thành công!</span>
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
                    <form method="post" id="formTacGia" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="inputId" value="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="inputTen" class="form-label fw-semibold text-dark">Tên tác giả <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" id="inputTen" name="ten" required 
                                       value="<?= htmlspecialchars($editData['TenTacGia'] ?? '') ?>" 
                                       placeholder="Nhập tên tác giả">
                            </div>
                            <div class="col-md-6">
                                <label for="inputNgaySinh" class="form-label fw-semibold text-dark">Ngày sinh</label>
                                <input type="date" class="form-control form-control-lg" id="inputNgaySinh" name="ngaysinh" 
                                       value="<?= $editData['NgaySinh'] ?? '' ?>" 
                                       placeholder="Chọn ngày sinh">
                            </div>
                            <div class="col-md-6">
                                <label for="inputQuocTich" class="form-label fw-semibold text-dark">Quốc tịch</label>
                                <input type="text" class="form-control form-control-lg" id="inputQuocTich" name="quoctich" 
                                       value="<?= htmlspecialchars($editData['QuocTich'] ?? '') ?>" 
                                       placeholder="Nhập quốc tịch">
                            </div>
                            <div class="col-md-6">
                                <label for="inputAnh" class="form-label fw-semibold text-dark">Ảnh đại diện</label>
                                <input type="file" class="form-control form-control-lg" id="inputAnh" name="anh" accept="image/*">
                                <small class="text-muted">Chỉ chấp nhận file ảnh</small>
                                <div id="previewAnh" class="mt-2"></div>
                                <?php if ($editData && !empty($editData['AnhDaiDien'])): ?>
                                    <div class="mt-2">
                                        <img src="../assets/images/tacgia/<?= htmlspecialchars($editData['AnhDaiDien']) ?>" 
                                             width="80" height="80" class="rounded-circle" style="object-fit: cover; border: 1px solid #e2e8f0;">
                                        <small class="text-muted d-block">Ảnh hiện tại</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label for="inputMota" class="form-label fw-semibold text-dark">Mô tả</label>
                                <textarea class="form-control" id="inputMota" name="mota" rows="4" 
                                          placeholder="Nhập mô tả về tác giả (tùy chọn)"><?= htmlspecialchars($editData['MoTa'] ?? '') ?></textarea>
                            </div>
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
        max-width: 800px;
    }
    .form-control:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.15);
    }
    #previewAnh img {
        max-width: 100px;
        max-height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }
</style>

<script>
    function openModalThem() {
        document.getElementById('modalTitle').textContent = 'Thêm Tác Giả Mới';
        document.getElementById('btnSubmitText').textContent = 'Thêm mới';
        document.getElementById('inputId').value = '';
        document.getElementById('inputTen').value = '';
        document.getElementById('inputNgaySinh').value = '';
        document.getElementById('inputQuocTich').value = '';
        document.getElementById('inputMota').value = '';
        document.getElementById('inputAnh').value = '';
        document.getElementById('previewAnh').innerHTML = '';
        document.getElementById('formTacGia').reset();
    }
    
    function openModalSua(id, ten, ngaysinh, quoctich, mota, anh) {
        document.getElementById('modalTitle').textContent = 'Sửa Tác Giả';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('inputId').value = id || '';
        document.getElementById('inputTen').value = ten || '';
        document.getElementById('inputNgaySinh').value = ngaysinh || '';
        document.getElementById('inputQuocTich').value = quoctich || '';
        document.getElementById('inputMota').value = mota || '';
        document.getElementById('inputAnh').value = '';
        document.getElementById('previewAnh').innerHTML = '';
        const modal = new bootstrap.Modal(document.getElementById('modalTacGia'));
        modal.show();
    }
    
    // Preview ảnh khi chọn
    document.getElementById('inputAnh')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewAnh').innerHTML = 
                    '<img src="' + e.target.result + '" class="mb-2">';
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Auto open modal if edit mode
    <?php if ($editId): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalTacGia'));
        modal.show();
    });
    <?php endif; ?>
    
    // Close modal when clicking outside
    document.getElementById('modalTacGia').addEventListener('click', function(e) {
        if (e.target === this) {
            const modal = bootstrap.Modal.getInstance(this);
            modal.hide();
        }
    });
</script>

<?php include 'footer.php'; ?>
