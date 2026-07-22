<?php
require_once __DIR__ . "/../config/db.php";

class SanPhamTacGia {

    private $conn;

    public function __construct() {
        // Lấy biến $conn từ file db.php
        global $conn;
        $this->conn = $conn;
    }

    // ✅ Lấy tác giả theo sản phẩm (kèm vai trò)
    public function getTacGiaBySanPham($maSP) {
        $sql = "SELECT tg.MaTacGia, tg.TenTacGia, sptg.VaiTro
                FROM SanPham_TacGia sptg
                JOIN TacGia tg ON sptg.MaTacGia = tg.MaTacGia
                WHERE sptg.MaSP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        $stmt->execute();
        return $stmt->get_result();
    }

    // ✅ Gán tác giả cho sản phẩm (có vai trò)
    public function insert($maSP, $maTacGia, $vaiTro = 'TacGia') {
        $sql = "INSERT INTO SanPham_TacGia(MaSP, MaTacGia, VaiTro)
                VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $maSP, $maTacGia, $vaiTro);
        return $stmt->execute();
    }

    // ✅ Xóa tác giả khỏi sản phẩm
    public function delete($maSP, $maTacGia) {
        $sql = "DELETE FROM SanPham_TacGia WHERE MaSP = ? AND MaTacGia = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $maSP, $maTacGia);
        return $stmt->execute();
    }

    // ✅ Lấy tác giả (chỉ tên – để hiển thị trong bảng sản phẩm)
    public function getTacGiaOfSanPham($maSP) {
        $sql = "SELECT tg.TenTacGia 
                FROM SanPham_TacGia sptg 
                JOIN TacGia tg ON tg.MaTacGia = sptg.MaTacGia 
                WHERE sptg.MaSP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        $stmt->execute();
        return $stmt->get_result();
    }
}
