<?php
require_once __DIR__ . "/../config/db.php";

class TacGia {

    public function getAll() {
        global $conn;
        $sql = "SELECT * FROM TacGia ORDER BY MaTacGia DESC";
        return $conn->query($sql);
    }

    public function insert($ten, $ngaysinh, $quoctich, $mota, $anh) {
        global $conn;
        $sql = "INSERT INTO TacGia(TenTacGia, NgaySinh, QuocTich, MoTa, AnhDaiDien)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $ten, $ngaysinh, $quoctich, $mota, $anh);
        return $stmt->execute();
    }

    public function getById($id) {
        global $conn;
        $sql = "SELECT * FROM TacGia WHERE MaTacGia = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $ten, $ngaysinh, $quoctich, $mota, $anh) {
        global $conn;

        if ($anh != "") {
            $sql = "UPDATE TacGia 
                    SET TenTacGia=?, NgaySinh=?, QuocTich=?, MoTa=?, AnhDaiDien=? 
                    WHERE MaTacGia=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $ten, $ngaysinh, $quoctich, $mota, $anh, $id);
        } else {
            $sql = "UPDATE TacGia 
                    SET TenTacGia=?, NgaySinh=?, QuocTich=?, MoTa=? 
                    WHERE MaTacGia=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $ten, $ngaysinh, $quoctich, $mota, $id);
        }

        return $stmt->execute();
    }

    public function delete($id) {
        global $conn;
        $sql = "DELETE FROM TacGia WHERE MaTacGia = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
