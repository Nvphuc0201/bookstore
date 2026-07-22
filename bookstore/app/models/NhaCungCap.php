<?php
require_once __DIR__ . "/../config/db.php";

class NhaCungCap {
    // Lấy tất cả NCC
    public function getAll() {
        global $conn;
        $sql = "SELECT * FROM NhaCungCap ORDER BY MaNCC DESC";
        return $conn->query($sql);
    }

    // Lấy 1 NCC theo ID
    public function getById($id) {
        global $conn;
        $sql = "SELECT * FROM NhaCungCap WHERE MaNCC = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Thêm NCC
    public function insert($ten, $sdt, $diachi, $email) {
        global $conn;
        $sql = "INSERT INTO NhaCungCap (TenNCC, SDT, DiaChi, Email) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $ten, $sdt, $diachi, $email);
        return $stmt->execute();
    }

    // Cập nhật NCC
    public function update($id, $ten, $sdt, $diachi, $email) {
        global $conn;
        $sql = "UPDATE NhaCungCap SET TenNCC = ?, SDT = ?, DiaChi = ?, Email = ? WHERE MaNCC = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $ten, $sdt, $diachi, $email, $id);
        return $stmt->execute();
    }

    // Xóa NCC
    public function delete($id) {
        global $conn;
        $sql = "DELETE FROM NhaCungCap WHERE MaNCC = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
