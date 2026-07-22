<?php
require_once __DIR__ . "/../config/db.php";

class NhaXuatBan {

    public function getAll() {
        global $conn;
        $sql = "SELECT * FROM NhaXuatBan ORDER BY MaNXB DESC";
        return $conn->query($sql);
    }

    public function insert($ten, $diachi, $sdt, $email) {
        global $conn;
        $sql = "INSERT INTO NhaXuatBan(TenNXB, DiaChi, SDT, Email)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $ten, $diachi, $sdt, $email);
        return $stmt->execute();
    }

    public function getById($id) {
        global $conn;
        $sql = "SELECT * FROM NhaXuatBan WHERE MaNXB = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $ten, $diachi, $sdt, $email) {
        global $conn;
        $sql = "UPDATE NhaXuatBan 
                SET TenNXB=?, DiaChi=?, SDT=?, Email=? 
                WHERE MaNXB=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $ten, $diachi, $sdt, $email, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        global $conn;
        $sql = "DELETE FROM NhaXuatBan WHERE MaNXB = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
