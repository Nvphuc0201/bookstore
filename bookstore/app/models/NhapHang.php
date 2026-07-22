<?php
require_once __DIR__ . "/../config/db.php";

class NhapHang {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Lấy danh sách phiếu nhập (kèm tên NCC)
    public function getAll() {
        $sql = "SELECT l.*, nc.TenNCC
                FROM LichSuNhapHang l
                LEFT JOIN NhaCungCap nc ON l.MaNCC = nc.MaNCC
                ORDER BY l.MaNhap DESC";
        return $this->conn->query($sql);
    }

    // Lấy 1 phiếu nhập
    public function getById($id) {
        $sql = "SELECT l.*, nc.TenNCC
                FROM LichSuNhapHang l
                LEFT JOIN NhaCungCap nc ON l.MaNCC = nc.MaNCC
                WHERE l.MaNhap = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Lấy chi tiết phiếu nhập
    public function getChiTiet($maNhap) {
        $sql = "SELECT c.MaSP, c.SoLuongNhap, c.DonGiaNhap, p.TenSP, (c.SoLuongNhap * c.DonGiaNhap) AS ThanhTien
                FROM ChiTietNhapHang c
                LEFT JOIN SanPham p ON c.MaSP = p.MaSP
                WHERE c.MaNhap = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maNhap);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Tạo phiếu nhập + chi tiết + cập nhật tồn kho
    // $maNCC: int, $ngayNhap: string (Y-m-d H:i:s) or null, $items: array of ['maSP'=>, 'soLuong'=>, 'donGia'=>]
    public function create($maNCC, $ngayNhap, $items) {
        // begin transaction
        $this->conn->begin_transaction();

        try {
            if (empty($ngayNhap)) {
                $sql = "INSERT INTO LichSuNhapHang (MaNCC, TongTienNhap) VALUES (?, 0)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $maNCC);
                $stmt->execute();
            } else {
                $sql = "INSERT INTO LichSuNhapHang (MaNCC, NgayNhap, TongTienNhap) VALUES (?, ?, 0)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("is", $maNCC, $ngayNhap);
                $stmt->execute();
            }

            $maNhap = $this->conn->insert_id;
            $tong = 0;

            $sqlInsertCT = "INSERT INTO ChiTietNhapHang (MaNhap, MaSP, SoLuongNhap, DonGiaNhap) VALUES (?, ?, ?, ?)";
            $stmtCT = $this->conn->prepare($sqlInsertCT);

            $sqlUpdateSP = "UPDATE SanPham SET SoLuong = SoLuong + ? WHERE MaSP = ?";
            $stmtSP = $this->conn->prepare($sqlUpdateSP);

            foreach ($items as $it) {
                $maSP = (int)$it['maSP'];
                $soLuong = (int)$it['soLuong'];
                $donGia = (float)$it['donGia'];
                if ($maSP <= 0 || $soLuong <= 0) continue;

                $stmtCT->bind_param("iiid", $maNhap, $maSP, $soLuong, $donGia);
                $stmtCT->execute();

                // update stock
                $stmtSP->bind_param("ii", $soLuong, $maSP);
                $stmtSP->execute();

                $tong += $soLuong * $donGia;
            }

            // cập nhật tổng tiền
            $sqlUpdTong = "UPDATE LichSuNhapHang SET TongTienNhap = ? WHERE MaNhap = ?";
            $stmtUpd = $this->conn->prepare($sqlUpdTong);
            $stmtUpd->bind_param("di", $tong, $maNhap);
            $stmtUpd->execute();

            $this->conn->commit();
            return $maNhap;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Xóa phiếu nhập và hoàn lại tồn kho (giảm SoLuong)
    public function delete($maNhap) {
        // begin transaction
        $this->conn->begin_transaction();
        try {
            // lấy chi tiết
            $sql = "SELECT MaSP, SoLuongNhap FROM ChiTietNhapHang WHERE MaNhap = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $maNhap);
            $stmt->execute();
            $res = $stmt->get_result();

            $sqlUpdateSP = "UPDATE SanPham SET SoLuong = GREATEST(SoLuong - ?, 0) WHERE MaSP = ?";
            $stmtSP = $this->conn->prepare($sqlUpdateSP);

            while ($r = $res->fetch_assoc()) {
                $stmtSP->bind_param("ii", $r['SoLuongNhap'], $r['MaSP']);
                $stmtSP->execute();
            }

            // xóa chi tiết
            $delCT = $this->conn->prepare("DELETE FROM ChiTietNhapHang WHERE MaNhap = ?");
            $delCT->bind_param("i", $maNhap);
            $delCT->execute();

            // xóa hóa đơn
            $del = $this->conn->prepare("DELETE FROM LichSuNhapHang WHERE MaNhap = ?");
            $del->bind_param("i", $maNhap);
            $del->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
