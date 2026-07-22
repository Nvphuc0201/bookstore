<?php
require_once __DIR__ . "/../config/db.php";

class DonHang {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Lấy tất cả đơn hàng (kèm tên khách hàng nếu có)
    public function getAll() {
        $sql = "SELECT dh.*, kh.HoTen, kh.SDT
                FROM DonHang dh
                LEFT JOIN KhachHang kh ON dh.MaKH = kh.MaKH
                ORDER BY dh.NgayDat DESC, dh.MaDH DESC";
        return $this->conn->query($sql);
    }

    // Lấy đơn hàng theo trạng thái
    public function getByStatus($trangThai = null, $orderBy = 'NgayDat DESC') {
        $sql = "SELECT dh.*, kh.HoTen, kh.SDT
                FROM DonHang dh
                LEFT JOIN KhachHang kh ON dh.MaKH = kh.MaKH";
        
        if ($trangThai !== null && $trangThai !== 'all') {
            $sql .= " WHERE dh.TrangThai = ?";
        }
        
        $sql .= " ORDER BY " . ($orderBy ?: 'dh.NgayDat DESC, dh.MaDH DESC');
        
        if ($trangThai !== null && $trangThai !== 'all') {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $trangThai);
            $stmt->execute();
            return $stmt->get_result();
        } else {
            return $this->conn->query($sql);
        }
    }

    // Lấy đơn hàng cần xử lý (Chờ xác nhận)
    public function getPendingOrders() {
        return $this->getByStatus('ChoXacNhan', 'NgayDat DESC');
    }

    // Lấy 1 đơn hàng
    public function getById($maDH) {
        $sql = "SELECT dh.*, kh.HoTen
                FROM DonHang dh
                LEFT JOIN KhachHang kh ON dh.MaKH = kh.MaKH
                WHERE dh.MaDH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDH);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Lấy chi tiết đơn hàng
    public function getChiTiet($maDH) {
        $sql = "SELECT ct.MaSP, ct.SoLuong, ct.DonGia, ct.ThanhTien, sp.TenSP
                FROM ChiTietDonHang ct
                LEFT JOIN SanPham sp ON ct.MaSP = sp.MaSP
                WHERE ct.MaDH = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maDH);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Cập nhật trạng thái đơn hàng (và xử lý tồn kho nếu cần)
    // $maDH: int, $newStatus: string
    public function updateStatus($maDH, $newStatus) {
        // bắt transaction
        $this->conn->begin_transaction();
        try {
            // lấy trạng thái cũ
            $stmt = $this->conn->prepare("SELECT TrangThai FROM DonHang WHERE MaDH = ? FOR UPDATE");
            $stmt->bind_param("i", $maDH);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows == 0) {
                $this->conn->rollback();
                return false;
            }
            $row = $res->fetch_assoc();
            $oldStatus = $row['TrangThai'];

            // nếu chuyển sang Đã hủy và cũ chưa phải Đã hủy -> hoàn trả kho
            if ($newStatus === 'DaHuy' && $oldStatus !== 'DaHuy') {
                // lấy chi tiết
                $ct = $this->getChiTiet($maDH);
                $stmtUp = $this->conn->prepare("UPDATE SanPham SET SoLuong = SoLuong + ? WHERE MaSP = ?");
                while ($r = $ct->fetch_assoc()) {
                    $so = (int)$r['SoLuong'];
                    $maSP = (int)$r['MaSP'];
                    if ($so > 0 && $maSP > 0) {
                        $stmtUp->bind_param("ii", $so, $maSP);
                        $stmtUp->execute();
                    }
                }
            }

            // nếu chuyển từ DaHuy -> trạng thái khác (ví dụ khôi phục), ta KHÔNG tự trừ kho vì khi tạo đơn ban đầu có thể đã trừ.
            // (Nếu bạn muốn logic khác — nói mình điều chỉnh.)

            // Cập nhật trạng thái
            $upd = $this->conn->prepare("UPDATE DonHang SET TrangThai = ? WHERE MaDH = ?");
            $upd->bind_param("si", $newStatus, $maDH);
            $upd->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // (Optional) Xóa đơn hàng hoàn chỉnh (và hoàn lại kho nếu đơn chưa bị hủy)
    public function delete($maDH) {
        $this->conn->begin_transaction();
        try {
            // lấy trạng thái
            $stmt = $this->conn->prepare("SELECT TrangThai FROM DonHang WHERE MaDH = ? FOR UPDATE");
            $stmt->bind_param("i", $maDH);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows == 0) {
                $this->conn->rollback();
                return false;
            }
            $row = $res->fetch_assoc();
            $oldStatus = $row['TrangThai'];

            // nếu trạng thái != DaHuy, thực hiện hoàn kho trước khi xóa
            if ($oldStatus !== 'DaHuy') {
                $ct = $this->getChiTiet($maDH);
                $stmtUp = $this->conn->prepare("UPDATE SanPham SET SoLuong = SoLuong + ? WHERE MaSP = ?");
                while ($r = $ct->fetch_assoc()) {
                    $so = (int)$r['SoLuong'];
                    $maSP = (int)$r['MaSP'];
                    if ($so > 0 && $maSP > 0) {
                        $stmtUp->bind_param("ii", $so, $maSP);
                        $stmtUp->execute();
                    }
                }
            }

            // xóa chi tiết
            $delCT = $this->conn->prepare("DELETE FROM ChiTietDonHang WHERE MaDH = ?");
            $delCT->bind_param("i", $maDH);
            $delCT->execute();

            // xóa donhang
            $del = $this->conn->prepare("DELETE FROM DonHang WHERE MaDH = ?");
            $del->bind_param("i", $maDH);
            $del->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
