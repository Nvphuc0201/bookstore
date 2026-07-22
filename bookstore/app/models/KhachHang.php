<?php
class KhachHang {

    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        $sql = "
            SELECT KhachHang.*, TaiKhoan.TenDangNhap, TaiKhoan.VaiTro, TaiKhoan.TrangThai
            FROM KhachHang
            LEFT JOIN TaiKhoan ON KhachHang.MaTK = TaiKhoan.MaTK
            ORDER BY MaKH DESC
        ";
        return $this->conn->query($sql);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT KhachHang.*, TaiKhoan.TenDangNhap 
            FROM KhachHang
            LEFT JOIN TaiKhoan ON KhachHang.MaTK = TaiKhoan.MaTK
            WHERE MaKH = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function insert($HoTen, $Email, $SDT, $DiaChi, $MaTK) {
        $stmt = $this->conn->prepare("
            INSERT INTO KhachHang (HoTen, Email, SDT, DiaChi, MaTK)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssi", $HoTen, $Email, $SDT, $DiaChi, $MaTK);
        return $stmt->execute();
    }

    public function update($id, $HoTen, $Email, $SDT, $DiaChi) {
        $stmt = $this->conn->prepare("
            UPDATE KhachHang
            SET HoTen=?, Email=?, SDT=?, DiaChi=?
            WHERE MaKH=?
        ");
        $stmt->bind_param("ssssi", $HoTen, $Email, $SDT, $DiaChi, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM KhachHang WHERE MaKH = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
