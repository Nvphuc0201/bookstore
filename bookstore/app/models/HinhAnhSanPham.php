<?php
require_once __DIR__ . '/../config/db.php';

class HinhAnhSanPham {

    public function insert($maSP, $duongDan, $laChinh = 0) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO HinhAnhSanPham(MaSP,DuongDan,LaAnhChinh) VALUES(?,?,?)");
        $stmt->execute([$maSP, $duongDan, $laChinh]);
    }

    public function getBySanPham($maSP) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM HinhAnhSanPham WHERE MaSP=?");
        $stmt->execute([$maSP]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteBySanPham($maSP) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM HinhAnhSanPham WHERE MaSP=?");
        return $stmt->execute([$maSP]);
    }

    public function updateAnhChinh($maSP, $maHinh) {
        global $pdo;
        $pdo->prepare("UPDATE HinhAnhSanPham SET LaAnhChinh=0 WHERE MaSP=?")->execute([$maSP]);
        $pdo->prepare("UPDATE HinhAnhSanPham SET LaAnhChinh=1 WHERE MaHinh=?")->execute([$maHinh]);
    }
}
