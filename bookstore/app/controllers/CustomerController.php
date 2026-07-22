<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/DonHang.php';

class CustomerController {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

    // Hiển thị trang hồ sơ (Mặc định)
    public function index() {
        global $conn;
        $maKH = $_SESSION['user']['MaKH'];
        $maTK = $_SESSION['user']['MaTK'];

        // 1. Lấy thông tin khách hàng + Tài khoản
        $sqlInfo = "SELECT kh.*, tk.TenDangNhap 
                    FROM KhachHang kh 
                    JOIN TaiKhoan tk ON kh.MaTK = tk.MaTK 
                    WHERE kh.MaKH = ?";
        $stmt = $conn->prepare($sqlInfo);
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();

        // 2. Lấy lịch sử đơn hàng
        $sqlOrders = "SELECT * FROM DonHang WHERE MaKH = ? ORDER BY NgayDat DESC";
        $stmt2 = $conn->prepare($sqlOrders);
        $stmt2->bind_param("i", $maKH);
        $stmt2->execute();
        $orders = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        // 2b. Tách đơn hàng: đang mua (Chờ xác nhận / Đang giao) và đã mua (Đã giao / Đã hủy)
        $currentOrders = [];
        $historyOrders = [];
        foreach ($orders as $od) {
            if (in_array($od['TrangThai'], ['ChoXacNhan', 'DangGiao'], true)) {
                $currentOrders[] = $od;
            } else {
                $historyOrders[] = $od;
            }
        }

        // 3. Lấy danh sách thông báo mới nhất của khách hàng
        $notifications = [];
        $sqlNoti = "SELECT * FROM ThongBao WHERE MaKH = ? ORDER BY NgayGui DESC LIMIT 20";
        if ($stmt3 = $conn->prepare($sqlNoti)) {
            $stmt3->bind_param("i", $maKH);
            $stmt3->execute();
            $notifications = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt3->close();
        }

        // 4. Đánh dấu tất cả thông báo là đã đọc khi vào trang khách hàng
        $stmtRead = $conn->prepare("UPDATE ThongBao SET DaDoc = 1 WHERE MaKH = ? AND DaDoc = 0");
        if ($stmtRead) {
            $stmtRead->bind_param("i", $maKH);
            $stmtRead->execute();
            $stmtRead->close();
        }

        require_once __DIR__ . '/../views/customer/profile.php';
    }

    // Khách hàng hủy đơn của chính mình (chỉ cho phép khi Chờ xác nhận)
    public function cancelOrder() {
        global $conn;
        $maKH = $_SESSION['user']['MaKH'];
        $maDH = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($maDH <= 0) {
            header("Location: index.php?controller=customer");
            exit;
        }

        $dhModel = new DonHang();
        $order = $dhModel->getById($maDH);

        // Chỉ xử lý nếu đơn thuộc về khách hiện tại
        if (!$order || (int)$order['MaKH'] !== (int)$maKH) {
            echo "<script>alert('Bạn không có quyền thao tác với đơn hàng này!'); window.location.href='index.php?controller=customer';</script>";
            exit;
        }

        // Chỉ cho phép hủy khi còn Chờ xác nhận
        if ($order['TrangThai'] !== 'ChoXacNhan') {
            echo "<script>alert('Chỉ có thể hủy đơn hàng ở trạng thái Chờ xác nhận!'); window.location.href='index.php?controller=customer';</script>";
            exit;
        }

        $ok = $dhModel->updateStatus($maDH, 'DaHuy');
        if ($ok) {
            echo "<script>alert('Đã hủy đơn hàng #{$maDH}'); window.location.href='index.php?controller=customer';</script>";
        } else {
            echo "<script>alert('Không thể hủy đơn hàng, vui lòng thử lại!'); window.location.href='index.php?controller=customer';</script>";
        }
    }

    // Xem chi tiết một đơn hàng (của chính khách hàng)
    public function orderDetail() {
        global $conn;
        $maKH = $_SESSION['user']['MaKH'];
        $maDH = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($maDH <= 0) {
            header("Location: index.php?controller=customer");
            exit;
        }

        $dhModel = new DonHang();
        $order = $dhModel->getById($maDH);

        // Chỉ cho xem đơn hàng của chính mình
        if (!$order || (int)$order['MaKH'] !== (int)$maKH) {
            header("Location: index.php?controller=customer");
            exit;
        }

        // Lấy chi tiết đơn hàng
        $itemsRes = $dhModel->getChiTiet($maDH);
        $items = [];
        if ($itemsRes) {
            while ($r = $itemsRes->fetch_assoc()) {
                $items[] = $r;
            }
        }

        require_once __DIR__ . '/../views/customer/order_detail.php';
    }

    // Cập nhật thông tin cá nhân
    public function updateInfo() {
        global $conn;
        $maKH = $_SESSION['user']['MaKH'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hoten  = trim($_POST['hoten']);
            $sdt    = trim($_POST['sdt']);
            $email  = trim($_POST['email']);
            $diachi = trim($_POST['diachi']);

            $sql = "UPDATE KhachHang SET HoTen = ?, SDT = ?, Email = ?, DiaChi = ? WHERE MaKH = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $hoten, $sdt, $email, $diachi, $maKH);

            if ($stmt->execute()) {
                // Cập nhật lại tên trong Session
                $_SESSION['user']['HoTen'] = $hoten;
                echo "<script>alert('Cập nhật thông tin thành công!'); window.location.href='index.php?controller=customer';</script>";
            } else {
                echo "<script>alert('Lỗi cập nhật!'); window.history.back();</script>";
            }
        }
    }

    // Đổi mật khẩu
    public function changePassword() {
        global $conn;
        $maTK = $_SESSION['user']['MaTK'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldPass = $_POST['old_pass'];
            $newPass = $_POST['new_pass'];
            $confirmPass = $_POST['confirm_pass'];

            // 1. Kiểm tra mật khẩu cũ
            $stmt = $conn->prepare("SELECT MatKhau FROM TaiKhoan WHERE MaTK = ?");
            $stmt->bind_param("i", $maTK);
            $stmt->execute();
            $acc = $stmt->get_result()->fetch_assoc();

            if (!password_verify($oldPass, $acc['MatKhau'])) {
                echo "<script>alert('Mật khẩu cũ không đúng!'); window.history.back();</script>";
                exit;
            }

            // 2. Kiểm tra xác nhận mật khẩu
            if ($newPass !== $confirmPass) {
                echo "<script>alert('Mật khẩu xác nhận không khớp!'); window.history.back();</script>";
                exit;
            }

            // 3. Cập nhật mật khẩu mới
            $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
            $stmtUpd = $conn->prepare("UPDATE TaiKhoan SET MatKhau = ? WHERE MaTK = ?");
            $stmtUpd->bind_param("si", $hashedPass, $maTK);
            
            if ($stmtUpd->execute()) {
                echo "<script>alert('Đổi mật khẩu thành công! Vui lòng đăng nhập lại.'); window.location.href='index.php?controller=auth&action=logout';</script>";
            } else {
                echo "<script>alert('Lỗi hệ thống!'); window.history.back();</script>";
            }
        }
    }
}
?>