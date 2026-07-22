<?php include __DIR__ . '/../layout/header.php'; ?>

<?php
// Ensure $customer is defined to avoid undefined/null offsets in the view.
if (!isset($customer) || !is_array($customer)) {
    $customer = [
        'TenDangNhap' => isset($_SESSION['user']['TenDangNhap']) ? $_SESSION['user']['TenDangNhap'] : '',
        'HoTen' => isset($_SESSION['user']['HoTen']) ? $_SESSION['user']['HoTen'] : '',
        'SDT' => '',
        'Email' => '',
        'DiaChi' => ''
    ];
}
// Chuẩn hóa mảng đơn hàng
if (!isset($currentOrders) && isset($orders)) {
    $currentOrders = $orders;
}
if (!isset($historyOrders)) {
    $historyOrders = [];
}
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <?php
                        $avatarImg = (isset($customer['AvatarURL']) && !empty($customer['AvatarURL'])) 
                            ? $customer['AvatarURL'] 
                            : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
                    ?>
                    <img id="sidebarAvatar" src="<?= htmlspecialchars($avatarImg) ?>" width="80" class="mb-3 rounded-circle border p-1">
                    <h5 class="fw-bold"><?= htmlspecialchars($customer['HoTen']) ?></h5>
                    <p class="text-muted small">Thành viên</p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action active-tab" onclick="switchTab(event, 'info')">
                        <i class="fa-solid fa-user me-2"></i> Thông tin tài khoản
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="switchTab(event, 'orders')">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i> Lịch sử đơn hàng
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="switchTab(event, 'notifications')">
                        <i class="fa-solid fa-bell me-2"></i> Thông báo
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="switchTab(event, 'password')">
                        <i class="fa-solid fa-key me-2"></i> Đổi mật khẩu
                    </a>
                    <a href="index.php?controller=auth&action=logout" class="list-group-item list-group-item-action text-danger">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            
            <div id="tab-info" class="content-section">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3 border-bottom d-flex justify-content-between align-items-center">
                        <span>CẬP NHẬT THÔNG TIN</span>
                        <button type="button" class="btn btn-sm btn-warning" id="editBtn" onclick="toggleEditMode()">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Chỉnh sửa
                        </button>
                    </div>
                    <div class="card-body">
                        <form action="index.php?controller=customer&action=updateInfo" method="POST">
                            <!-- Avatar Section -->
                            <div class="mb-4 text-center">
                                <div class="mb-3">
                                    <img id="avatarPreview" src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="100" class="rounded-circle border p-2 bg-light" alt="Avatar">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Tên đăng nhập</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($customer['TenDangNhap']) ?>" readonly>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Họ và tên</label>
                                    <input type="text" name="hoten" id="hotenInput" class="form-control" value="<?= htmlspecialchars($customer['HoTen']) ?>" readonly required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Số điện thoại</label>
                                    <input type="text" name="sdt" id="sdtInput" class="form-control" value="<?= htmlspecialchars($customer['SDT']) ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" name="email" id="emailInput" class="form-control" value="<?= htmlspecialchars($customer['Email']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Địa chỉ nhận hàng mặc định</label>
                                <textarea name="diachi" id="diachiInput" class="form-control" rows="2" readonly><?= htmlspecialchars($customer['DiaChi']) ?></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div id="actionButtons" class="mt-4">
                                <button type="submit" class="btn btn-success" id="saveBtn" style="display:none;">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancelBtn" style="display:none;" onclick="toggleEditMode()">
                                    <i class="fa-solid fa-times me-1"></i> Hủy
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="tab-orders" class="content-section d-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3 border-bottom">
                        LỊCH SỬ ĐƠN HÀNG
                    </div>
                    <div class="card-body">
                        <?php if(empty($currentOrders) && empty($historyOrders)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">Bạn chưa có đơn hàng nào.</p>
                                <a href="index.php?controller=sanpham&action=list" class="btn btn-alpha">Mua sắm ngay</a>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($currentOrders)): ?>
                                <h6 class="fw-bold mb-3 text-dark">Đơn hàng đang mua</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Mã đơn</th>
                                                <th>Ngày đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th class="text-center">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($currentOrders as $order): ?>
                                                <tr>
                                                    <td class="fw-bold">#<?= $order['MaDH'] ?></td>
                                                    <td><?= date('d/m/Y H:i', strtotime($order['NgayDat'])) ?></td>
                                                    <td class="text-danger fw-bold"><?= number_format($order['TongTien'], 0, ',', '.') ?> đ</td>
                                                    <td>
                                                        <?php 
                                                            $statusClass = 'bg-secondary';
                                                            if($order['TrangThai'] == 'ChoXacNhan') $statusClass = 'bg-warning text-dark';
                                                            if($order['TrangThai'] == 'DangGiao') $statusClass = 'bg-info text-white';
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>"><?= $order['TrangThai'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="index.php?controller=customer&action=orderDetail&id=<?= $order['MaDH'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                                            Xem
                                                        </a>
                                                        <?php if($order['TrangThai'] === 'ChoXacNhan'): ?>
                                                            <a href="index.php?controller=customer&action=cancelOrder&id=<?= $order['MaDH'] ?>"
                                                               class="btn btn-sm btn-outline-danger"
                                                               onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng #<?= $order['MaDH'] ?>?');">
                                                                Hủy đơn
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($historyOrders)): ?>
                                <h6 class="fw-bold mb-3 text-dark mt-4">Đơn hàng đã mua</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Mã đơn</th>
                                                <th>Ngày đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th>Chi tiết</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($historyOrders as $order): ?>
                                                <tr>
                                                    <td class="fw-bold">#<?= $order['MaDH'] ?></td>
                                                    <td><?= date('d/m/Y H:i', strtotime($order['NgayDat'])) ?></td>
                                                    <td class="text-danger fw-bold"><?= number_format($order['TongTien'], 0, ',', '.') ?> đ</td>
                                                    <td>
                                                        <?php 
                                                            $statusClass = 'bg-secondary';
                                                            if($order['TrangThai'] == 'DaGiao') $statusClass = 'bg-success';
                                                            if($order['TrangThai'] == 'DaHuy') $statusClass = 'bg-danger';
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>"><?= $order['TrangThai'] ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="index.php?controller=customer&action=orderDetail&id=<?= $order['MaDH'] ?>" class="btn btn-sm btn-outline-primary">
                                                            Xem
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="tab-notifications" class="content-section d-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3 border-bottom d-flex justify-content-between align-items-center">
                        <span>THÔNG BÁO CỦA BẠN</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($notifications)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">Hiện tại bạn chưa có thông báo nào.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($notifications as $tb): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-start <?= $tb['DaDoc'] ? '' : 'bg-light' ?>">
                                        <div>
                                            <div class="d-flex align-items-center mb-1">
                                                <strong><?= htmlspecialchars($tb['TieuDe']) ?></strong>
                                                <?php if (!$tb['DaDoc']): ?>
                                                    <span class="badge bg-danger ms-2">Mới</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small text-muted mb-1">
                                                <?= date('d/m/Y H:i', strtotime($tb['NgayGui'])) ?>
                                                <?php if ($tb['LoaiTB'] === 'KhuyenMai'): ?>
                                                    · <span class="badge bg-success">Khuyến mãi</span>
                                                <?php elseif ($tb['LoaiTB'] === 'DonHang'): ?>
                                                    · <span class="badge bg-primary">Đơn hàng</span>
                                                <?php elseif ($tb['LoaiTB'] === 'SanPhamMoi'): ?>
                                                    · <span class="badge bg-info text-dark">Sản phẩm</span>
                                                <?php else: ?>
                                                    · <span class="badge bg-secondary">Hệ thống</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="small">
                                                <?= nl2br(htmlspecialchars($tb['NoiDung'])) ?>
                                            </div>
                                            <?php if (!empty($tb['LienKet'])): ?>
                                                <div class="mt-1">
                                                    <a href="<?= htmlspecialchars($tb['LienKet']) ?>" class="small">Xem chi tiết »</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="tab-password" class="content-section d-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3 border-bottom">
                        ĐỔI MẬT KHẨU
                    </div>
                    <div class="card-body">
                        <form action="index.php?controller=customer&action=changePassword" method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Mật khẩu hiện tại</label>
                                <input type="password" name="old_pass" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Mật khẩu mới</label>
                                <input type="password" name="new_pass" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Xác nhận mật khẩu mới</label>
                                <input type="password" name="confirm_pass" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-alpha px-4">Đổi mật khẩu</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .active-tab {
        background-color: #fff1f2 !important;
        color: var(--alpha-red) !important;
        font-weight: bold;
        border-color: #fee2e2;
    }
</style>

<script>
    function switchTab(event, tabName) {
        event.preventDefault();
        
        // 1. Ẩn tất cả nội dung tab
        document.querySelectorAll('.content-section').forEach(el => el.classList.add('d-none'));
        
        // 2. Hiện tab được chọn
        document.getElementById('tab-' + tabName).classList.remove('d-none');
        
        // 3. Xử lý active class cho menu bên trái
        document.querySelectorAll('.list-group-item').forEach(el => el.classList.remove('active-tab'));
        event.target.closest('a').classList.add('active-tab');

        // 4. Nếu là tab-info, reset edit mode
        if (tabName === 'info') {
            toggleEditMode(true); // Reset to view mode
        }
    }

    function toggleEditMode(forceReset = false) {
        const editBtn = document.getElementById('editBtn');
        const saveBtn = document.getElementById('saveBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const inputs = ['hotenInput', 'sdtInput', 'emailInput', 'diachiInput'];

        // Check if currently in view mode (edit button visible) or edit mode (save button visible)
        const isViewMode = editBtn.style.display !== 'none';

        if (forceReset || isViewMode) {
            // Enter Edit Mode
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-block';
            cancelBtn.style.display = 'inline-block';
            inputs.forEach(id => {
                const input = document.getElementById(id);
                input.readOnly = false;
                input.classList.add('border-primary');
            });
        } else {
            // Reset to View Mode
            editBtn.style.display = 'inline-block';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            inputs.forEach(id => {
                const input = document.getElementById(id);
                input.readOnly = true;
                input.classList.remove('border-primary');
            });
        }
    }
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>