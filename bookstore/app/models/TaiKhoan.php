<?php
require_once __DIR__ . "/../config/db.php";

class TaiKhoan {

    public function getAll() {
        global $conn;
        $sql = "SELECT * FROM TaiKhoan ORDER BY MaTK DESC";
        return $conn->query($sql);
    }

    public function getById($id) {
        global $conn;
        $sql = "SELECT * FROM TaiKhoan WHERE MaTK = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function insert($username, $password, $role, $status) {
        global $conn;
        $sql = "INSERT INTO TaiKhoan (TenDangNhap, MatKhau, VaiTro, TrangThai)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $role, $status);
        return $stmt->execute();
    }

    public function update($id, $role, $status) {
        global $conn;
        $sql = "UPDATE TaiKhoan SET VaiTro=?, TrangThai=? WHERE MaTK=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $role, $status, $id);
        return $stmt->execute();
    }

    public function updatePassword($id, $newPass) {
        global $conn;
        $sql = "UPDATE TaiKhoan SET MatKhau=? WHERE MaTK=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newPass, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        global $conn;
        $sql = "DELETE FROM TaiKhoan WHERE MaTK=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // LOGIN
    public static function login($tenDangNhap, $matKhau) {
        global $conn;

        $sql = "SELECT * FROM TaiKhoan WHERE TenDangNhap = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $tenDangNhap);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if ($matKhau === $user['MatKhau']) {
                return $user;
            }
        }
        return false;
    }
}
