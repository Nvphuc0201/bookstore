<?php
require_once __DIR__ . '/../config/db.php';

class AuthController {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    /* =========================
        CHỨC NĂNG ĐĂNG NHẬP
    ========================= */
    public function login() {
        // Nếu đã đăng nhập, chuyển hướng về trang chủ
        if (isset($_SESSION['user'])) {
            header("Location: index.php");
            exit;
        }

        // Xử lý khi người dùng bấm nút Submit (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;
            
            // Lấy dữ liệu từ form (khớp với name="" trong view login.php)
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // 1. Tìm tài khoản trong DB (JOIN với KhachHang để lấy MaKH và HoTen)
            $sql = "SELECT tk.*, kh.MaKH, kh.HoTen 
                    FROM TaiKhoan tk 
                    LEFT JOIN KhachHang kh ON tk.MaTK = kh.MaTK 
                    WHERE tk.TenDangNhap = ? AND tk.TrangThai = 1";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            // 2. Kiểm tra mật khẩu (Dùng password_verify để so khớp hash)
            if ($user && password_verify($password, $user['MatKhau'])) {
                // Lưu thông tin vào Session
                $_SESSION['user'] = [
                    'MaTK'        => $user['MaTK'],
                    'TenDangNhap' => $user['TenDangNhap'],
                    'VaiTro'      => $user['VaiTro'],
                    'MaKH'        => $user['MaKH'], // Quan trọng để đặt hàng
                    'HoTen'       => $user['HoTen']
                ];

                // Kiểm tra vai trò để chuyển hướng
                $role = strtolower(trim($user['VaiTro']));
                $adminRoles = ['quanly', 'nhanvien', 'admin', 'administrator'];
                if (in_array($role, $adminRoles, true)) {
                    // Nếu là Admin/Nhân viên -> Vào trang quản trị
                    header("Location: admin/dashboard.php");
                } else {
                    // Nếu là Khách hàng -> Về trang chủ
                    header("Location: index.php");
                }
                exit;
            } else {
                // Đăng nhập thất bại
                $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
            }
        }

        // Load View
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /* =========================
        CHỨC NĂNG ĐĂNG KÝ
    ========================= */
    public function register() {
        if (isset($_SESSION['user'])) {
            header("Location: index.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;

            // Lấy dữ liệu từ form (khớp với view register.php)
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $fullname = trim($_POST['fullname']);
            $email    = trim($_POST['email']);
            $phone    = trim($_POST['phone']);

            // 1. Kiểm tra tên đăng nhập đã tồn tại chưa
            $check = $conn->prepare("SELECT MaTK FROM TaiKhoan WHERE TenDangNhap = ?");
            $check->bind_param("s", $username);
            $check->execute();
            
            if ($check->get_result()->num_rows > 0) {
                $error = "Tên đăng nhập này đã được sử dụng!";
            } else {
                // 2. Sử dụng Transaction để đảm bảo toàn vẹn dữ liệu
                $conn->begin_transaction();
                try {
                    // A. Mã hóa mật khẩu
                    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

                    // B. Thêm vào bảng TaiKhoan
                    $stmtTK = $conn->prepare("INSERT INTO TaiKhoan (TenDangNhap, MatKhau, VaiTro, TrangThai) VALUES (?, ?, 'KhachHang', 1)");
                    $stmtTK->bind_param("ss", $username, $hashed_pass);
                    $stmtTK->execute();
                    $maTK = $conn->insert_id; // Lấy ID vừa tạo

                    // C. Thêm vào bảng KhachHang
                    $stmtKH = $conn->prepare("INSERT INTO KhachHang (HoTen, Email, SDT, MaTK, NgayDangKy) VALUES (?, ?, ?, ?, NOW())");
                    $stmtKH->bind_param("sssi", $fullname, $email, $phone, $maTK);
                    $stmtKH->execute();

                    // Hoàn tất
                    $conn->commit();
                    
                    // Thông báo và chuyển về trang đăng nhập
                    echo "<script>
                        alert('Đăng ký tài khoản thành công! Vui lòng đăng nhập.'); 
                        window.location.href='index.php?controller=auth&action=login';
                    </script>";
                    exit;

                } catch (Exception $e) {
                    $conn->rollback(); // Hoàn tác nếu lỗi
                    $error = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }

    /* =========================
        CHỨC NĂNG ĐĂNG XUẤT
    ========================= */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // 1. Xóa session user
        // Unset all of the session variables.
        $_SESSION = [];

        // If it's desired to kill the session, also delete the session cookie.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();

        // Try to redirect to login page via header; if headers already sent, provide HTML/JS fallback
        $loginUrl = 'index.php?controller=auth&action=login';
        if (!headers_sent()) {
            header("Location: $loginUrl");
            exit;
        }

        // Fallback: output simple HTML that redirects via meta refresh and JS
        echo '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($loginUrl, ENT_QUOTES) . '">'
            . '<script>window.location.href = "' . addslashes($loginUrl) . '";</script></head><body></body></html>';
        exit;
    }

    // Hàm fallback mặc định
    public function index() {
        $this->login();
    }
}
?>