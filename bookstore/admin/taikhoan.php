<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/TaiKhoan.php";

$model = new TaiKhoan();
$errors = [];
$success = false;
$editId = null;
$editData = null;

// Xử lý POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'KhachHang';
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if (empty($username)) {
        $errors[] = "Tên đăng nhập không được để trống.";
    }
    
    if (empty($errors)) {
        if ($id > 0) {
            // Sửa (chỉ cập nhật role và status)
            if ($model->update($id, $role, $status)) {
                $success = true;
            } else {
                $errors[] = "Cập nhật thất bại.";
            }
        } else {
            // Thêm mới
            if (empty($pass)) {
                $errors[] = "Mật khẩu không được để trống.";
            } else {
                if ($model->insert($username, $pass, $role, $status)) {
                    $success = true;
                } else {
                    $errors[] = "Thêm tài khoản thất bại. Có thể tên đăng nhập đã tồn tại.";
                }
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

$list = $model->getAll();
$totalTK = $list->num_rows;
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

    .role-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .role-admin { background: #fee2e2; color: #991b1b; }
    .role-staff { background: #dbeafe; color: #1e40af; }
    .role-customer { background: #e2e8f0; color: var(--text-secondary); }

    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-active { background: #d1fae5; color: #065f46; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-semibold" style="color: var(--text-primary); font-size: 1.75rem;">
                Quản Lý Tài Khoản
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong><?= $totalTK ?></strong> tài khoản</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTaiKhoan" onclick="openModalThem()">
            <i class="fas fa-plus me-2"></i>Thêm tài khoản mới
        </button>
    </div>

    <?php if ($list && $list->num_rows > 0): ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Mã TK</th>
                            <th width="20%">Tên đăng nhập</th>
                            <th width="15%">Vai trò</th>
                            <th width="12%">Trạng thái</th>
                            <th width="33%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stt = 1;
                        while ($row = $list->fetch_assoc()): 
                            $roleClass = match(strtolower($row['VaiTro'])) {
                                'quanly', 'admin' => 'role-admin',
                                'nhanvien' => 'role-staff',
                                default => 'role-customer'
                            };
                            $roleText = match(strtolower($row['VaiTro'])) {
                                'quanly', 'admin' => 'Quản lý',
                                'nhanvien' => 'Nhân viên',
                                default => 'Khách hàng'
                            };
                            $statusClass = $row['TrangThai'] == 1 ? 'status-active' : 'status-inactive';
                            $statusText = $row['TrangThai'] == 1 ? 'Hoạt động' : 'Bị khóa';
                        ?>
                            <tr>
                                <td class="text-muted fw-semibold"><?= $stt++ ?></td>
                                <td><strong style="color: var(--text-primary);">#<?= str_pad($row['MaTK'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                                <td>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars($row['TenDangNhap']) ?></strong>
                                </td>
                                <td>
                                    <span class="role-badge <?= $roleClass ?>"><?= $roleText ?></span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                           class="btn btn-warning btn-action"
                                           title="Sửa"
                                           onclick="openModalSua(<?= $row['MaTK'] ?>, <?= json_encode($row['TenDangNhap'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['VaiTro'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= $row['TrangThai'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="taikhoan_doi_mk.php?id=<?= $row['MaTK'] ?>"
                                           class="btn btn-action"
                                           style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;"
                                           title="Đổi mật khẩu">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        <a href="taikhoan_xoa.php?id=<?= $row['MaTK'] ?>"
                                           class="btn btn-action"
                                           style="background: #fee2e2; color: #991b1b; border-color: #fecaca;"
                                           onclick="return confirm('Xóa tài khoản \"<?= addslashes($row['TenDangNhap']) ?>\"?')"
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
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có tài khoản nào</h5>
            <p class="text-muted mb-3">Thêm tài khoản đầu tiên để bắt đầu quản lý!</p>
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTaiKhoan" onclick="openModalThem()">
                <i class="fas fa-plus me-2"></i>Thêm tài khoản
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Thêm/Sửa Tài Khoản -->
<div class="modal fade" id="modalTaiKhoan" tabindex="-1" aria-labelledby="modalTaiKhoanLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalTaiKhoanLabel">
                    <i class="fas fa-users-cog me-2 text-secondary"></i><span id="modalTitle">Thêm Tài Khoản Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <span><?= $editId ? 'Cập nhật' : 'Thêm' ?> tài khoản thành công!</span>
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
                    <form method="post" id="formTaiKhoan">
                        <input type="hidden" name="id" id="inputId" value="">
                        <div class="mb-3">
                            <label for="inputUsername" class="form-label fw-semibold text-dark">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="inputUsername" name="username" required 
                                   value="<?= htmlspecialchars($editData['TenDangNhap'] ?? '') ?>" 
                                   placeholder="Nhập tên đăng nhập" <?= $editId ? 'readonly' : '' ?>>
                            <?php if ($editId): ?>
                                <small class="text-muted">Không thể thay đổi tên đăng nhập</small>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3" id="passwordField">
                            <label for="inputPassword" class="form-label fw-semibold text-dark">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="inputPassword" name="password" 
                                   <?= $editId ? '' : 'required' ?> 
                                   placeholder="Nhập mật khẩu">
                            <?php if ($editId): ?>
                                <small class="text-muted">Để trống nếu không muốn đổi mật khẩu. Sử dụng chức năng "Đổi mật khẩu" để thay đổi.</small>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="inputRole" class="form-label fw-semibold text-dark">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="inputRole" name="role" required>
                                <option value="QuanLy" <?= ($editData['VaiTro'] ?? '') == 'QuanLy' ? 'selected' : '' ?>>Quản lý</option>
                                <option value="NhanVien" <?= ($editData['VaiTro'] ?? '') == 'NhanVien' ? 'selected' : '' ?>>Nhân viên</option>
                                <option value="KhachHang" <?= ($editData['VaiTro'] ?? '') == 'KhachHang' || !$editData ? 'selected' : '' ?>>Khách hàng</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="inputStatus" class="form-label fw-semibold text-dark">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="inputStatus" name="status" required>
                                <option value="1" <?= ($editData['TrangThai'] ?? 1) == 1 ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="0" <?= ($editData['TrangThai'] ?? 1) == 0 ? 'selected' : '' ?>>Bị khóa</option>
                            </select>
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
        border-color: #64748b;
        box-shadow: 0 0 0 0.2rem rgba(100, 116, 139, 0.15);
    }
</style>

<script>
    function openModalThem() {
        document.getElementById('modalTitle').textContent = 'Thêm Tài Khoản Mới';
        document.getElementById('btnSubmitText').textContent = 'Thêm mới';
        document.getElementById('inputId').value = '';
        document.getElementById('inputUsername').value = '';
        document.getElementById('inputPassword').value = '';
        document.getElementById('inputRole').value = 'KhachHang';
        document.getElementById('inputStatus').value = '1';
        document.getElementById('inputUsername').removeAttribute('readonly');
        document.getElementById('inputPassword').setAttribute('required', 'required');
        document.getElementById('formTaiKhoan').reset();
    }
    
    function openModalSua(id, username, role, status) {
        document.getElementById('modalTitle').textContent = 'Sửa Tài Khoản';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('inputId').value = id || '';
        document.getElementById('inputUsername').value = username || '';
        document.getElementById('inputPassword').value = '';
        document.getElementById('inputRole').value = role || 'KhachHang';
        document.getElementById('inputStatus').value = status !== undefined ? status : '1';
        document.getElementById('inputUsername').setAttribute('readonly', 'readonly');
        document.getElementById('inputPassword').removeAttribute('required');
        const modal = new bootstrap.Modal(document.getElementById('modalTaiKhoan'));
        modal.show();
    }
    
    // Auto open modal if edit mode
    <?php if ($editId): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalTaiKhoan'));
        modal.show();
    });
    <?php endif; ?>
    
    // Close modal when clicking outside
    document.getElementById('modalTaiKhoan').addEventListener('click', function(e) {
        if (e.target === this) {
            const modal = bootstrap.Modal.getInstance(this);
            modal.hide();
        }
    });
</script>

<?php include 'footer.php'; ?>
