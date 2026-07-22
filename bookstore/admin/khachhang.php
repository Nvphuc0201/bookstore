<?php
require_once "../app/middleware/admin.php";
require_once "../app/config/db.php";
require_once "../app/models/KhachHang.php";

$kh = new KhachHang($conn);
$errors = [];
$success = false;
$editId = null;
$editData = null;

// Lấy danh sách tài khoản
$taikhoan = $conn->query("SELECT MaTK, TenDangNhap FROM TaiKhoan WHERE VaiTro='KhachHang'");

// Xử lý POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $HoTen = trim($_POST['HoTen'] ?? '');
    $Email = trim($_POST['Email'] ?? '');
    $SDT = trim($_POST['SDT'] ?? '');
    $DiaChi = trim($_POST['DiaChi'] ?? '');
    $MaTK = !empty($_POST['MaTK']) ? (int)$_POST['MaTK'] : null;
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if (empty($HoTen)) {
        $errors[] = "Họ tên không được để trống.";
    }
    
    if (empty($errors)) {
        if ($id > 0) {
            // Sửa
            if ($kh->update($id, $HoTen, $Email, $SDT, $DiaChi)) {
                $success = true;
            } else {
                $errors[] = "Cập nhật thất bại.";
            }
        } else {
            // Thêm mới
            if ($kh->insert($HoTen, $Email, $SDT, $DiaChi, $MaTK)) {
                $success = true;
            } else {
                $errors[] = "Thêm khách hàng thất bại.";
            }
        }
    }
}

// Lấy dữ liệu để sửa (nếu có)
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    if ($editId > 0) {
        $editData = $kh->getById($editId);
        if (!$editData) {
            $editId = null;
            $editData = null;
        }
    }
}

$data = $kh->getAll();
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
                Quản Lý Khách Hàng
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tổng cộng: <strong><?= $data ? $data->num_rows : 0 ?></strong> khách hàng</p>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKhachHang" onclick="openModalThem()">
            <i class="fas fa-user-plus me-2"></i>Thêm khách hàng
        </button>
    </div>

    <?php if ($data && $data->num_rows > 0): ?>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Họ tên</th>
                            <th width="15%">Email</th>
                            <th width="12%">SĐT</th>
                            <th width="18%">Địa chỉ</th>
                            <th width="10%">Ngày đăng ký</th>
                            <th width="10%">Tài khoản</th>
                            <th width="8%">Vai trò</th>
                            <th width="7%">Trạng thái</th>
                            <th width="12%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stt = 1;
                        while ($row = $data->fetch_assoc()): 
                            $roleClass = $row['VaiTro'] == 'QuanLy' ? 'role-admin' : 
                                        ($row['VaiTro'] == 'NhanVien' ? 'role-staff' : 'role-customer');
                            $roleText = $row['VaiTro'] == 'QuanLy' ? 'Quản lý' : 
                                        ($row['VaiTro'] == 'NhanVien' ? 'Nhân viên' : 'Khách hàng');
                            $statusClass = $row['TrangThai'] == 1 ? 'status-active' : 'status-inactive';
                            $statusText = $row['TrangThai'] == 1 ? 'Hoạt động' : 'Bị khóa';
                        ?>
                            <tr>
                                <td class="text-muted fw-semibold"><?= $stt++ ?></td>
                                <td>
                                    <strong style="color: var(--text-primary);"><?= htmlspecialchars($row['HoTen'] ?? 'Chưa đặt tên') ?></strong>
                                </td>
                                <td>
                                    <a href="mailto:<?= $row['Email'] ?>" class="text-decoration-none text-muted">
                                        <?= htmlspecialchars($row['Email'] ?? '-') ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:<?= $row['SDT'] ?>" class="text-decoration-none text-muted">
                                        <?= htmlspecialchars($row['SDT'] ?? '-') ?>
                                    </a>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($row['DiaChi'] ?? 'Chưa cập nhật') ?></td>
                                <td>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($row['NgayDangKy'])) ?></small>
                                </td>
                                <td>
                                    <code class="text-muted" style="font-size: 0.85rem;"><?= htmlspecialchars($row['TenDangNhap'] ?? '-') ?></code>
                                </td>
                                <td>
                                    <span class="role-badge <?= $roleClass ?>">
                                        <?= $roleText ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                           class="btn btn-warning btn-action"
                                           title="Sửa"
                                           onclick="openModalSua(<?= $row['MaKH'] ?>, <?= json_encode($row['HoTen'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['Email'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['SDT'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($row['DiaChi'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="khachhang_xoa.php?id=<?= $row['MaKH'] ?>"
                                           class="btn btn-action"
                                           style="background: #fee2e2; color: #991b1b; border-color: #fecaca;"
                                           onclick="return confirm('Xóa khách hàng \"<?= addslashes($row['HoTen']) ?>\"?\nTất cả đơn hàng của họ sẽ bị ảnh hưởng!')"
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
            <h5 style="color: var(--text-secondary); margin-bottom: 0.5rem;">Chưa có khách hàng nào</h5>
            <p class="text-muted mb-3">Thêm khách hàng đầu tiên để bắt đầu quản lý!</p>
            <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKhachHang" onclick="openModalThem()">
                <i class="fas fa-user-plus me-2"></i>Thêm khách hàng
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Thêm/Sửa Khách Hàng -->
<div class="modal fade" id="modalKhachHang" tabindex="-1" aria-labelledby="modalKhachHangLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom border-light p-4">
                <h5 class="modal-title fw-bold text-dark" id="modalKhachHangLabel">
                    <i class="fas fa-users me-2 text-primary"></i><span id="modalTitle">Thêm Khách Hàng Mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <span><?= $editId ? 'Cập nhật' : 'Thêm' ?> khách hàng thành công!</span>
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
                    <form method="post" id="formKhachHang">
                        <input type="hidden" name="id" id="inputId" value="">
                        <div class="mb-3">
                            <label for="inputHoTen" class="form-label fw-semibold text-dark">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="inputHoTen" name="HoTen" required 
                                   value="<?= htmlspecialchars($editData['HoTen'] ?? '') ?>" 
                                   placeholder="Nhập họ tên khách hàng">
                        </div>
                        <div class="mb-3">
                            <label for="inputEmail" class="form-label fw-semibold text-dark">Email</label>
                            <input type="email" class="form-control form-control-lg" id="inputEmail" name="Email" 
                                   value="<?= htmlspecialchars($editData['Email'] ?? '') ?>" 
                                   placeholder="Nhập email">
                        </div>
                        <div class="mb-3">
                            <label for="inputSDT" class="form-label fw-semibold text-dark">Số điện thoại</label>
                            <input type="text" class="form-control form-control-lg" id="inputSDT" name="SDT" 
                                   value="<?= htmlspecialchars($editData['SDT'] ?? '') ?>" 
                                   placeholder="Nhập số điện thoại">
                        </div>
                        <div class="mb-3">
                            <label for="inputDiaChi" class="form-label fw-semibold text-dark">Địa chỉ</label>
                            <input type="text" class="form-control form-control-lg" id="inputDiaChi" name="DiaChi" 
                                   value="<?= htmlspecialchars($editData['DiaChi'] ?? '') ?>" 
                                   placeholder="Nhập địa chỉ">
                        </div>
                        <div class="mb-3" id="taiKhoanField">
                            <label for="inputMaTK" class="form-label fw-semibold text-dark">Tài khoản</label>
                            <select class="form-select form-select-lg" id="inputMaTK" name="MaTK">
                                <option value="">-- Không chọn --</option>
                                <?php 
                                $taikhoan->data_seek(0);
                                while ($tk = $taikhoan->fetch_assoc()): 
                                ?>
                                    <option value="<?= $tk['MaTK'] ?>" <?= ($editData['MaTK'] ?? '') == $tk['MaTK'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tk['TenDangNhap']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <small class="text-muted">Chọn tài khoản khách hàng (tùy chọn)</small>
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
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
    }
</style>

<script>
    function openModalThem() {
        document.getElementById('modalTitle').textContent = 'Thêm Khách Hàng Mới';
        document.getElementById('btnSubmitText').textContent = 'Thêm mới';
        document.getElementById('inputId').value = '';
        document.getElementById('inputHoTen').value = '';
        document.getElementById('inputEmail').value = '';
        document.getElementById('inputSDT').value = '';
        document.getElementById('inputDiaChi').value = '';
        document.getElementById('inputMaTK').value = '';
        document.getElementById('taiKhoanField').style.display = 'block';
        document.getElementById('formKhachHang').reset();
    }
    
    function openModalSua(id, hoTen, email, sdt, diaChi) {
        document.getElementById('modalTitle').textContent = 'Sửa Khách Hàng';
        document.getElementById('btnSubmitText').textContent = 'Cập nhật';
        document.getElementById('inputId').value = id || '';
        document.getElementById('inputHoTen').value = hoTen || '';
        document.getElementById('inputEmail').value = email || '';
        document.getElementById('inputSDT').value = sdt || '';
        document.getElementById('inputDiaChi').value = diaChi || '';
        document.getElementById('taiKhoanField').style.display = 'none'; // Ẩn khi sửa
        const modal = new bootstrap.Modal(document.getElementById('modalKhachHang'));
        modal.show();
    }
    
    // Auto open modal if edit mode
    <?php if ($editId): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalKhachHang'));
        modal.show();
    });
    <?php endif; ?>
    
    // Close modal when clicking outside
    document.getElementById('modalKhachHang').addEventListener('click', function(e) {
        if (e.target === this) {
            const modal = bootstrap.Modal.getInstance(this);
            modal.hide();
        }
    });
</script>

<?php include 'footer.php'; ?>
