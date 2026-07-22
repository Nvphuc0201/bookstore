<?php
// 1. Khởi động Session
if (session_status() === PHP_SESSION_NONE) session_start();

// 2. Kết nối Cấu hình & Database
require_once __DIR__ . '/app/config/db.php';

// 3. Load tất cả Controllers
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/SanPhamController.php';
require_once __DIR__ . '/app/controllers/CartController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/CustomerController.php'; // <--- Thêm dòng này

// 4. Lấy Controller & Action từ URL
$controller = $_GET['controller'] ?? 'home';
$action     = $_GET['action'] ?? 'index';

// (No global auth guard here) Allow public access to home, product list, product detail, auth, etc.
// Controllers that need authentication (cart checkout, customer, admin pages) perform their own checks.

// ====================================================
// 6. ROUTER (ĐIỀU HƯỚNG)
// ====================================================
switch ($controller) {

    case 'home':
        $ctrl = new HomeController();
        $ctrl->index();
        break;

    case 'sanpham':
        $ctrl = new SanPhamController();
        if (method_exists($ctrl, $action)) {
            $ctrl->$action($_GET['id'] ?? null);
        } else {
            // Nếu action sai, mặc định về danh sách
            $ctrl->list(); 
        }
        break;

    case 'cart':
        $ctrl = new CartController();
        if ($action === 'add') {
            $ctrl->add((int)($_GET['id'] ?? 0), (int)($_GET['qty'] ?? 1));
        } elseif ($action === 'remove') {
            $ctrl->remove((int)($_GET['id'] ?? 0));
        } elseif ($action === 'update') {
            $ctrl->update($_POST['qty'] ?? []);
        } elseif ($action === 'checkoutPage') {
            // Hiển thị trang checkout riêng (lấy thông tin từ DB khách hàng)
            $ctrl->checkoutPage();
        } elseif ($action === 'checkout') {
            // Xử lý đặt hàng (submit từ trang checkout.php)
            $ctrl->checkout();
        } elseif ($action === 'clear') { 
            $ctrl->clear();
        } else {
            $ctrl->index();
        }
        break;

    // --- XỬ LÝ AUTH (ĐĂNG NHẬP/ĐĂNG KÝ) ---
    case 'auth':
        $ctrl = new AuthController();
        // Kiểm tra action có tồn tại trong Controller không
        if (method_exists($ctrl, $action)) {
            $ctrl->$action(); // Gọi hàm: login, handleLogin, register...
        } else {
            $ctrl->login(); // Mặc định hiện form login
        }
        break;

    // --- XỬ LÝ TÀI KHOẢN KHÁCH HÀNG ---
    case 'customer':
        $ctrl = new CustomerController();
        if (method_exists($ctrl, $action)) {
            $ctrl->$action();
        } else {
            $ctrl->index(); // Mặc định vào trang profile
        }
        break;

    default:
        // Nếu nhập controller linh tinh -> Về Home
        $ctrl = new HomeController();
        $ctrl->index();
        break;
}
?>