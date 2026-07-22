<?php
require_once __DIR__ . '/../config/db.php';

class AuthController {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    // Hiển thị form đăng nhập
    public function login() {
        // Nếu đã đăng nhập thì check quyền để chuyển hướng luôn
        if (isset($_SESSION['user'])) {
            $this->redirectUser($_SESSION['user']['VaiTro']);
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Xử lý đăng nhập (Hứng dữ liệu POST)
    public function handleLogin() {
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // 1. Tìm user trong DB
            $sql = "SELECT tk.*, kh.MaKH, kh.HoTen 
                    FROM TaiKhoan tk 
                    LEFT JOIN KhachHang kh ON tk.MaTK = kh.MaTK 
                    WHERE tk.TenDangNhap = ? AND tk.TrangThai = 1";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            // 2. Kiểm tra mật khẩu
            $authenticated = false;
            if ($user) {
                // Thử so khớp hash (mật khẩu đã mã hoá)
                if (password_verify($password, $user['MatKhau'])) {
                    $authenticated = true;
                } else {
                    // Fallback: nếu mật khẩu trong DB là plaintext (legacy), so sánh trực tiếp
                    if ($password === $user['MatKhau']) {
                        $authenticated = true;
                    }
                }
            }

            if ($authenticated) {
                // Lưu Session
                $_SESSION['user'] = [
                    'MaTK'        => $user['MaTK'],
                    'TenDangNhap' => $user['TenDangNhap'],
                    'VaiTro'      => $user['VaiTro'],
                    'MaKH'        => $user['MaKH'],
                    'HoTen'       => $user['HoTen']
                ];

                // 3. Chuyển hướng theo VaiTro
                $this->redirectUser($user['VaiTro']);
                
            } else {
                $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
                header("Location: /bookstore/index.php?controller=auth&action=login");
                exit;
            }
        }
    }

    // Hàm chuyển hướng riêng
    private function redirectUser($role) {
        $role = trim($role);
        
        // Nếu là Quản Lý hoặc Nhân Viên -> Vào trang Admin Dashboard
        if (strcasecmp($role, 'QuanLy') === 0 || strcasecmp($role, 'NhanVien') === 0) {
            header("Location: /bookstore/admin/dashboard.php");
        }
        // Nếu là Khách Hàng -> Về trang chủ
        elseif (strcasecmp($role, 'KhachHang') === 0) {
            header("Location: /bookstore/index.php");
        } else {
            // Fallback: chuyển về trang chủ
            header("Location: /bookstore/index.php");
        }
        exit;
    }

    // Hiển thị form đăng ký
    public function register() {
        // Nếu đã đăng nhập thì chuyển về trang chủ
        if (isset($_SESSION['user'])) {
            header("Location: /bookstore/index.php");
            exit;
        }

        $error = '';
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $conn;

            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            // Validation
            if (empty($username)) {
                $error = 'Tên đăng nhập không được để trống!';
            } elseif (empty($password)) {
                $error = 'Mật khẩu không được để trống!';
            } elseif (strlen($password) < 6) {
                $error = 'Mật khẩu phải ít nhất 6 ký tự!';
            } elseif ($password !== $confirm_password) {
                $error = 'Mật khẩu không khớp!';
            } elseif (empty($fullname)) {
                $error = 'Họ và tên không được để trống!';
            } else {
                // Kiểm tra tên đăng nhập đã tồn tại
                $checkStmt = $conn->prepare("SELECT MaTK FROM TaiKhoan WHERE TenDangNhap = ?");
                $checkStmt->bind_param("s", $username);
                $checkStmt->execute();
                
                if ($checkStmt->get_result()->num_rows > 0) {
                    $error = 'Tên đăng nhập này đã được sử dụng!';
                } else {
                    // Hash mật khẩu
                    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

                    // Sử dụng transaction
                    $conn->begin_transaction();
                    try {
                        // Thêm vào TaiKhoan
                        $insertTK = $conn->prepare("INSERT INTO TaiKhoan (TenDangNhap, MatKhau, VaiTro, TrangThai) VALUES (?, ?, 'KhachHang', 1)");
                        $insertTK->bind_param("ss", $username, $hashed_pass);
                        $insertTK->execute();
                        $maTK = $conn->insert_id;

                        // Thêm vào KhachHang
                        $insertKH = $conn->prepare("INSERT INTO KhachHang (HoTen, Email, SDT, MaTK, NgayDangKy) VALUES (?, ?, ?, ?, NOW())");
                        $insertKH->bind_param("sssi", $fullname, $email, $phone, $maTK);
                        $insertKH->execute();

                        $conn->commit();
                        $success = true;
                    } catch (Exception $e) {
                        $conn->rollback();
                        $error = 'Lỗi hệ thống: ' . $e->getMessage();
                    }
                }
            }
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }
    
    public function logout() {
        unset($_SESSION['user']);
        header("Location: /bookstore/index.php?controller=auth&action=login");
        exit;
    }
}
?>