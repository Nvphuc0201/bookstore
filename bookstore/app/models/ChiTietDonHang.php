<?php
class ChiTietDonHang {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getByDonHang($maDH) {
        $sql = "SELECT c.*, s.TenSP
                FROM ChiTietDonHang c
                JOIN SanPham s ON c.MaSP = s.MaSP
                WHERE c.MaDH = $maDH";
        return $this->conn->query($sql);
    }
}
?>
