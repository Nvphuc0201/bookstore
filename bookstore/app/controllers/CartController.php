<?php
require_once __DIR__ . '/../models/SanPham.php';
require_once __DIR__ . '/../config/db.php';

class CartController {
    private $spModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->spModel = new SanPham();
    }

    // --- HIỂN THỊ GIỎ HÀNG (LẤY TỪ DB) ---
    public function index() {
        global $conn;
        
        // 1. Chặn nếu chưa đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $maKH = $_SESSION['user']['MaKH'];
        $cart = [];
        $totalPrice = 0;

        // 2. Lấy MaGH của khách
        $stmt = $conn->prepare("SELECT MaGH FROM GioHang WHERE MaKH = ?");
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $res = $stmt->get_result();
        $gh = $res->fetch_assoc();

        if ($gh) {
            $maGH = $gh['MaGH'];
            // 3. Lấy chi tiết sản phẩm
            $sql = "SELECT ct.SoLuong, ct.MaSP, sp.TenSP, sp.DonGia, sp.HinhAnh 
                    FROM ChiTietGioHang ct
                    JOIN SanPham sp ON ct.MaSP = sp.MaSP
                    WHERE ct.MaGH = ?";
            
            $stmtList = $conn->prepare($sql);
            $stmtList->bind_param("i", $maGH);
            $stmtList->execute();
            $resultList = $stmtList->get_result();

            while ($row = $resultList->fetch_assoc()) {
                $cart[$row['MaSP']] = [
                    'id'    => $row['MaSP'],
                    'name'  => $row['TenSP'],
                    'price' => $row['DonGia'],
                    'image' => $row['HinhAnh'],
                    'qty'   => $row['SoLuong']
                ];
                $totalPrice += $row['DonGia'] * $row['SoLuong'];
            }
        }

        require_once __DIR__ . '/../views/cart/index.php';
    }

    // --- THÊM VÀO GIỎ (AJAX) ---
    // Được router gọi: add($id, $qty)
    public function add(int $id = 0, int $qty = 1) {
        header('Content-Type: application/json');
        global $conn;

        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            echo json_encode(['status' => 'login_required', 'message' => 'Bạn cần đăng nhập để mua hàng!']);
            exit;
        }

        // Ưu tiên tham số truyền từ Router, fallback về $_GET (khi gọi trực tiếp)
        if ($id <= 0) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        }
        if ($qty <= 0) {
            $qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
        }
        $maKH = $_SESSION['user']['MaKH'];

        // 2. Kiểm tra sản phẩm
        $product = $this->spModel->getById($id); 
        if (!$product) {
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
            exit;
        }

        // 3. Tìm hoặc Tạo Giỏ Hàng
        $stmtGH = $conn->prepare("SELECT MaGH FROM GioHang WHERE MaKH = ?");
        $stmtGH->bind_param("i", $maKH);
        $stmtGH->execute();
        $resGH = $stmtGH->get_result();
        
        if ($rowGH = $resGH->fetch_assoc()) {
            $maGH = $rowGH['MaGH'];
        } else {
            $stmtNew = $conn->prepare("INSERT INTO GioHang (MaKH) VALUES (?)");
            $stmtNew->bind_param("i", $maKH);
            $stmtNew->execute();
            $maGH = $conn->insert_id;
        }

        // 4. Thêm hoặc Cập nhật Chi Tiết
        $stmtCheck = $conn->prepare("SELECT SoLuong FROM ChiTietGioHang WHERE MaGH = ? AND MaSP = ?");
        $stmtCheck->bind_param("ii", $maGH, $id);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();

        if ($rowItem = $resCheck->fetch_assoc()) {
            // Đã có -> Cộng thêm
            $newQty = $rowItem['SoLuong'] + $qty;
            if ($newQty > $product['SoLuong']) {
                echo json_encode(['status' => 'error', 'message' => 'Kho không đủ hàng!']);
                exit;
            }
            $stmtUpd = $conn->prepare("UPDATE ChiTietGioHang SET SoLuong = ? WHERE MaGH = ? AND MaSP = ?");
            $stmtUpd->bind_param("iii", $newQty, $maGH, $id);
            $stmtUpd->execute();
        } else {
            // Chưa có -> Thêm mới
            if ($qty > $product['SoLuong']) {
                echo json_encode(['status' => 'error', 'message' => 'Kho không đủ hàng!']);
                exit;
            }
            $price = $product['DonGia'];
            $stmtIns = $conn->prepare("INSERT INTO ChiTietGioHang (MaGH, MaSP, SoLuong, DonGiaTamTinh) VALUES (?, ?, ?, ?)");
            $stmtIns->bind_param("iiid", $maGH, $id, $qty, $price);
            $stmtIns->execute();
        }

        echo json_encode(['status' => 'success', 'message' => 'Đã thêm vào giỏ!']);
        exit;
    }

    // --- CẬP NHẬT SỐ LƯỢNG ---
    // Được router gọi: update($qtyArray)
    public function update(array $qtyArray = []) {
        global $conn;
        if (!isset($_SESSION['user'])) { header("Location: index.php?controller=auth&action=login"); exit; }
        
        $maKH = $_SESSION['user']['MaKH'];
        
        // Lấy MaGH
        $stmt = $conn->prepare("SELECT MaGH FROM GioHang WHERE MaKH = ?");
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $gh = $stmt->get_result()->fetch_assoc();
        
        // Ưu tiên mảng truyền từ Router, fallback về $_POST (khi gọi trực tiếp)
        if (empty($qtyArray) && isset($_POST['qty']) && is_array($_POST['qty'])) {
            $qtyArray = $_POST['qty'];
        }

        if ($gh && !empty($qtyArray)) {
            $maGH = $gh['MaGH'];
            foreach ($qtyArray as $maSP => $soLuong) {
                $maSP = intval($maSP);
                $soLuong = intval($soLuong);
                if ($soLuong <= 0) {
                    $conn->query("DELETE FROM ChiTietGioHang WHERE MaGH = $maGH AND MaSP = $maSP");
                } else {
                    $conn->query("UPDATE ChiTietGioHang SET SoLuong = $soLuong WHERE MaGH = $maGH AND MaSP = $maSP");
                }
            }
        }
        header("Location: index.php?controller=cart");
        exit;
    }

    // --- XÓA SẢN PHẨM ---
    // Được router gọi: remove($id)
    public function remove(int $id = 0) {
        global $conn;
        if (!isset($_SESSION['user'])) { header("Location: index.php?controller=auth&action=login"); exit; }
        
        $maKH = $_SESSION['user']['MaKH'];
        // Fallback nếu gọi trực tiếp qua URL
        if ($id <= 0) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        }

        $stmt = $conn->prepare("SELECT MaGH FROM GioHang WHERE MaKH = ?");
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $gh = $stmt->get_result()->fetch_assoc();

        if ($gh) {
            $stmtDel = $conn->prepare("DELETE FROM ChiTietGioHang WHERE MaGH = ? AND MaSP = ?");
            $stmtDel->bind_param("ii", $gh['MaGH'], $id);
            $stmtDel->execute();
        }
        header("Location: index.php?controller=cart");
        exit;
    }

    // --- XÓA TOÀN BỘ GIỎ HÀNG CỦA NGƯỜI DÙNG ---
    // Được router gọi: clear()
    public function clear() {
        global $conn;
        if (!isset($_SESSION['user'])) { header("Location: index.php?controller=auth&action=login"); exit; }

        $maKH = $_SESSION['user']['MaKH'];

        // Tìm MaGH của khách
        $stmt = $conn->prepare("SELECT MaGH FROM GioHang WHERE MaKH = ?");
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $gh = $stmt->get_result()->fetch_assoc();

        if ($gh) {
            $maGH = $gh['MaGH'];
            // Xóa toàn bộ chi tiết giỏ
            $stmtDel = $conn->prepare("DELETE FROM ChiTietGioHang WHERE MaGH = ?");
            $stmtDel->bind_param("i", $maGH);
            $stmtDel->execute();
        }

        header("Location: index.php?controller=cart");
        exit;
    }

    // --- THANH TOÁN (TẠO ĐƠN HÀNG) ---
    // Nhận dữ liệu POST từ trang checkout.php (thông tin KH + địa chỉ + phương thức thanh toán)
    public function checkout() {
        global $conn;
        if (!isset($_SESSION['user'])) { header("Location: index.php?controller=auth&action=login"); exit; }

        $maKH = $_SESSION['user']['MaKH'];
        $hoTen   = trim($_POST['fullname'] ?? '');
        $sdt     = trim($_POST['phone'] ?? '');
        $diaChi  = trim($_POST['address'] ?? '');
        $pttt    = $_POST['payment_method'] ?? 'TienMat';

        // Validate đơn giản
        if (empty($diaChi) || empty($hoTen) || empty($sdt)) {
            echo "<script>alert('Vui lòng nhập đầy đủ họ tên, SĐT và địa chỉ!'); window.history.back();</script>";
            exit;
        }

        // Cập nhật lại thông tin khách hàng vào bảng KhachHang (để lần sau tự đổ ra)
        $stmtUpdateKH = $conn->prepare("UPDATE KhachHang SET HoTen = ?, SDT = ?, DiaChi = ? WHERE MaKH = ?");
        if ($stmtUpdateKH) {
            $stmtUpdateKH->bind_param("sssi", $hoTen, $sdt, $diaChi, $maKH);
            $stmtUpdateKH->execute();
            $stmtUpdateKH->close();
        }

        // 1. Lấy thông tin giỏ hàng
        $stmt = $conn->prepare("SELECT MaGH FROM GioHang WHERE MaKH = ?");
        $stmt->bind_param("i", $maKH);
        $stmt->execute();
        $gh = $stmt->get_result()->fetch_assoc();

        if (!$gh) { header("Location: index.php?controller=cart"); exit; }
        $maGH = $gh['MaGH'];

        // Lấy items
        $sqlItems = "SELECT ct.*, sp.DonGia FROM ChiTietGioHang ct JOIN SanPham sp ON ct.MaSP = sp.MaSP WHERE ct.MaGH = ?";
        $stmtItems = $conn->prepare($sqlItems);
        $stmtItems->bind_param("i", $maGH);
        $stmtItems->execute();
        $items = $stmtItems->get_result()->fetch_all(MYSQLI_ASSOC);

        if (empty($items)) {
            echo "<script>alert('Giỏ hàng trống!'); window.location.href='index.php?controller=cart';</script>";
            exit;
        }

        // Tính tổng tiền
        $tongTien = 0;
        foreach($items as $it) $tongTien += $it['DonGia'] * $it['SoLuong'];

        // 2. Transaction: Tạo Đơn -> Copy chi tiết -> Xóa giỏ -> Trừ kho
        $conn->begin_transaction();
        try {
            // A. Tạo Đơn hàng
            $stmtDH = $conn->prepare("INSERT INTO DonHang (NgayDat, TongTien, TrangThai, PhuongThucThanhToan, MaKH, DiaChiGiaoHang) VALUES (NOW(), ?, 'ChoXacNhan', ?, ?, ?)");
            $stmtDH->bind_param("dsis", $tongTien, $pttt, $maKH, $diaChi);
            $stmtDH->execute();
            $maDH = $conn->insert_id;

            // B. Thêm ChiTietDonHang & Trừ Kho
            $stmtCT = $conn->prepare("INSERT INTO ChiTietDonHang (MaDH, MaSP, SoLuong, DonGia, ThanhTien) VALUES (?, ?, ?, ?, ?)");
            $stmtKho = $conn->prepare("UPDATE SanPham SET SoLuong = SoLuong - ?, SoLuongDaBan = SoLuongDaBan + ? WHERE MaSP = ?");

            foreach($items as $it) {
                $thanhTien = $it['DonGia'] * $it['SoLuong'];
                // Insert chi tiết
                $stmtCT->bind_param("iiidd", $maDH, $it['MaSP'], $it['SoLuong'], $it['DonGia'], $thanhTien);
                $stmtCT->execute();
                
                // Trừ kho
                $stmtKho->bind_param("iii", $it['SoLuong'], $it['SoLuong'], $it['MaSP']);
                $stmtKho->execute();
            }

            // C. Xóa sạch giỏ hàng trong DB
            $conn->query("DELETE FROM ChiTietGioHang WHERE MaGH = $maGH");

            $conn->commit();
            echo "<script>alert('Đặt hàng thành công! Mã đơn: #$maDH'); window.location.href='index.php?controller=customer';</script>";

        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.history.back();</script>";
        }
    }
    
    // Fallback
    // public function checkoutPage() { $this->index(); }

    // --- HIỂN THỊ TRANG THANH TOÁN RIÊNG ---
    public function checkoutPage() {
        global $conn;
        
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $maKH = $_SESSION['user']['MaKH'];
        
        // 2. Lấy thông tin mới nhất của Khách hàng từ DB (để điền sẵn vào form)
        $stmtUser = $conn->prepare("SELECT HoTen, SDT, DiaChi, Email FROM KhachHang WHERE MaKH = ?");
        $stmtUser->bind_param("i", $maKH);
        $stmtUser->execute();
        $userResult = $stmtUser->get_result();
        $userInfo = $userResult->fetch_assoc(); // Dữ liệu khách hàng

        // 3. Lấy dữ liệu Giỏ hàng (Logic giống hàm index)
        $cart = [];
        $totalPrice = 0;

        $stmtGH = $conn->prepare("SELECT MaGH FROM GioHang WHERE MaKH = ?");
        $stmtGH->bind_param("i", $maKH);
        $stmtGH->execute();
        $gh = $stmtGH->get_result()->fetch_assoc();

        if ($gh) {
            $maGH = $gh['MaGH'];
            $sql = "SELECT ct.SoLuong, ct.MaSP, sp.TenSP, sp.DonGia, sp.HinhAnh 
                    FROM ChiTietGioHang ct
                    JOIN SanPham sp ON ct.MaSP = sp.MaSP
                    WHERE ct.MaGH = ?";
            
            $stmtList = $conn->prepare($sql);
            $stmtList->bind_param("i", $maGH);
            $stmtList->execute();
            $resultList = $stmtList->get_result();

            while ($row = $resultList->fetch_assoc()) {
                $cart[$row['MaSP']] = [
                    'id'    => $row['MaSP'],
                    'name'  => $row['TenSP'],
                    'price' => $row['DonGia'],
                    'qty'   => $row['SoLuong']
                ];
                $totalPrice += $row['DonGia'] * $row['SoLuong'];
            }
        }

        // Nếu giỏ trống thì đá về trang giỏ hàng
        if (empty($cart)) {
            header("Location: index.php?controller=cart");
            exit;
        }

        // 4. Gọi View Checkout
        require_once __DIR__ . '/../views/cart/checkout.php';
    }
}
?>