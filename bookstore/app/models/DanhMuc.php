<?php
require_once __DIR__ . "/../config/db.php";

class DanhMuc {

    private $conn;
    public function __construct() { global $conn; $this->conn = $conn; }

    public function getAll() {
        global $conn;
        $sql = "SELECT * FROM DanhMuc ORDER BY MaDM DESC";
        return $conn->query($sql);
    }

    public function insert($ten, $mota) {
        global $conn;
        $sql = "INSERT INTO DanhMuc(TenDM, MoTa) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $ten, $mota);
        return $stmt->execute();
    }

    public function getById($id) {
        global $conn;
        $sql = "SELECT * FROM DanhMuc WHERE MaDM = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $ten, $mota) {
        global $conn;
        $sql = "UPDATE DanhMuc SET TenDM = ?, MoTa = ? WHERE MaDM = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $ten, $mota, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        global $conn;
        $sql = "DELETE FROM DanhMuc WHERE MaDM = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
