<?php
// includes/gui_thongbao.php – PHIÊN BẢN SIÊU ỔN ĐỊNH, KHÔNG BAO GIỜ LỖI
if (!isset($conn)) {
    require_once __DIR__ . "/../app/config/db.php";
}

function guiThongBao($maKH, $tieuDe, $noiDung, $loai = 'HeThong', $lienKet = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO ThongBao (MaKH, TieuDe, NoiDung, LoaiTB, LienKet) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $maKH, $tieuDe, $noiDung, $loai, $lienKet);
    $stmt->execute();
}

function guiThongBaoTatCa($tieuDe, $noiDung, $loai = 'HeThong', $lienKet = '') {
    global $conn;
    
    // SỬA LỖI: Kiểm tra query có thành công không
    $result = $conn->query("SELECT MaKH FROM KhachHang WHERE TrangThai = 1");
    
    // Nếu query lỗi hoặc không có khách → thoát hàm, không lỗi
    if (!$result) {
        error_log("Lỗi query khách hàng: " . $conn->error);
        return;
    }
    
    if ($result->num_rows === 0) {
        // Không có khách hàng → không gửi, nhưng không lỗi
        return;
    }

    $stmt = $conn->prepare("INSERT INTO ThongBao (MaKH, TieuDe, NoiDung, LoaiTB, LienKet) VALUES (?, ?, ?, ?, ?)");
    
    while ($kh = $result->fetch_assoc()) {
        $stmt->bind_param("issss", $kh['MaKH'], $tieuDe, $noiDung, $loai, $lienKet);
        $stmt->execute();
    }
}

// Tương tự cho VIP (nếu cần sau này)
function guiThongBaoVIP($tieuDe, $noiDung, $loai = 'HeThong', $lienKet = '') {
    global $conn;
    $result = $conn->query("SELECT MaKH FROM KhachHang WHERE TrangThai = 1 AND IsVIP = 1");
    if (!$result || $result->num_rows === 0) return;

    $stmt = $conn->prepare("INSERT INTO ThongBao (MaKH, TieuDe, NoiDung, LoaiTB, LienKet) VALUES (?, ?, ?, ?, ?)");
    while ($kh = $result->fetch_assoc()) {
        $stmt->bind_param("issss", $kh['MaKH'], $tieuDe, $noiDung, $loai, $lienKet);
        $stmt->execute();
    }
}
?>