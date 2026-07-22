<?php
require_once __DIR__ . "/../config/db.php";

class SanPham {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Lấy tất cả sản phẩm (kèm tên danh mục, tacgia)
    public function getAll() {
        $sql = "SELECT sp.*, dm.TenDM, nxb.TenNXB,
                    GROUP_CONCAT(tg.TenTacGia SEPARATOR ', ') AS TacGia
                FROM SanPham sp
                LEFT JOIN DanhMuc dm ON sp.MaDM = dm.MaDM
                LEFT JOIN NhaXuatBan nxb ON sp.MaNXB = nxb.MaNXB
                LEFT JOIN SanPham_TacGia sptg ON sp.MaSP = sptg.MaSP
                LEFT JOIN TacGia tg ON sptg.MaTacGia = tg.MaTacGia
                GROUP BY sp.MaSP
                ORDER BY sp.NgayCapNhat DESC";
        $result = $this->conn->query($sql);
        if (!$result) { die("Lỗi SQL getAll SanPham: " . $this->conn->error); }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy sản phẩm theo danh mục
    public function getByCategory($madm) {
        $sql = "SELECT sp.*, dm.TenDM, nxb.TenNXB,
                    GROUP_CONCAT(tg.TenTacGia SEPARATOR ', ') AS TacGia
                FROM SanPham sp
                LEFT JOIN DanhMuc dm ON sp.MaDM = dm.MaDM
                LEFT JOIN NhaXuatBan nxb ON sp.MaNXB = nxb.MaNXB
                LEFT JOIN SanPham_TacGia sptg ON sp.MaSP = sptg.MaSP
                LEFT JOIN TacGia tg ON sptg.MaTacGia = tg.MaTacGia
                WHERE sp.MaDM = ?
                GROUP BY sp.MaSP
                ORDER BY sp.NgayCapNhat DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { die("Lỗi chuẩn bị SQL getByCategory: " . $this->conn->error); }
        $stmt->bind_param("i", $madm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy sản phẩm bán chạy (theo SoLuongDaBan)
    public function getBestSeller($limit = 8) {
        $sql = "SELECT sp.*, dm.TenDM, nxb.TenNXB,
                    GROUP_CONCAT(tg.TenTacGia SEPARATOR ', ') AS TacGia
                FROM SanPham sp
                LEFT JOIN DanhMuc dm ON sp.MaDM = dm.MaDM
                LEFT JOIN NhaXuatBan nxb ON sp.MaNXB = nxb.MaNXB
                LEFT JOIN SanPham_TacGia sptg ON sp.MaSP = sptg.MaSP
                LEFT JOIN TacGia tg ON sptg.MaTacGia = tg.MaTacGia
                GROUP BY sp.MaSP
                ORDER BY sp.SoLuongDaBan DESC
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { die("Lỗi chuẩn bị SQL getBestSeller: " . $this->conn->error); }
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Các method còn lại (insert, insertImage, getById, getImages, update, deleteImage, resetMainImage, getAllImagesByProduct, deleteProduct)
    // --- giữ nguyên code bạn đã có ---
    public function insert($ten, $gia, $soluong, $mota, $hinhanh, $madm, $manxb) {
        $sql = "INSERT INTO SanPham (TenSP, DonGia, SoLuong, MoTa, HinhAnh, MaDM, MaNXB) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { die("LỖI SQL INSERT SanPham: " . $this->conn->error); }
        $stmt->bind_param("sdissii", $ten, $gia, $soluong, $mota, $hinhanh, $madm, $manxb);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function insertImage($maSP, $duongDan, $laAnhChinh) {
        $sql = "INSERT INTO HinhAnhSanPham (MaSP, DuongDan, LaAnhChinh) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { die("LỖI SQL INSERT HÌNH ẢNH: " . $this->conn->error); }
        $stmt->bind_param("isi", $maSP, $duongDan, $laAnhChinh);
        return $stmt->execute();
    }

    public function getById($id) {
        $sql = "SELECT sp.*, dm.TenDM, nxb.TenNXB,
                    GROUP_CONCAT(tg.TenTacGia SEPARATOR ', ') AS TacGia
                FROM SanPham sp
                LEFT JOIN DanhMuc dm ON sp.MaDM=dm.MaDM
                LEFT JOIN NhaXuatBan nxb ON sp.MaNXB=nxb.MaNXB
                LEFT JOIN SanPham_TacGia sptg ON sp.MaSP=sptg.MaSP
                LEFT JOIN TacGia tg ON sptg.MaTacGia = tg.MaTacGia
                WHERE sp.MaSP = ?
                GROUP BY sp.MaSP";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { die("LỖI SQL getById: " . $this->conn->error); }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getImages($maSP) {
        $sql = "SELECT * FROM HinhAnhSanPham WHERE MaSP = ? ORDER BY LaAnhChinh DESC, MaHinh ASC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { die("LỖI SQL getImages: " . $this->conn->error); }
        $stmt->bind_param("i", $maSP);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function update($id, $ten, $gia, $mota, $madm, $manxb) {
        $sql = "UPDATE SanPham SET TenSP=?, DonGia=?, MoTa=?, MaDM=?, MaNXB=? WHERE MaSP=?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { die("LỖI SQL update: " . $this->conn->error); }
        $stmt->bind_param("sdsiii", $ten, $gia, $mota, $madm, $manxb, $id);
        return $stmt->execute();
    }

    public function deleteImage($maHinh) {
        $sql = "DELETE FROM HinhAnhSanPham WHERE MaHinh=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maHinh);
        return $stmt->execute();
    }

    public function resetMainImage($maSP) {
        $sql = "UPDATE HinhAnhSanPham SET LaAnhChinh=0 WHERE MaSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        return $stmt->execute();
    }

    public function getAllImagesByProduct($maSP) {
        $sql = "SELECT * FROM HinhAnhSanPham WHERE MaSP=? ORDER BY LaAnhChinh DESC, MaHinh ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function deleteProduct($maSP) {
        $sql = "DELETE FROM SanPham WHERE MaSP=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $maSP);
        return $stmt->execute();
    }

    // Hàm tìm kiếm sản phẩm theo Tên sách hoặc Tên tác giả
    public function search($keyword) {
        $keyword = "%" . $keyword . "%"; // Thêm % để tìm kiếm tương đối

        $sql = "SELECT sp.*, dm.TenDM, nxb.TenNXB,
                    GROUP_CONCAT(tg.TenTacGia SEPARATOR ', ') AS TacGia
                FROM SanPham sp
                LEFT JOIN DanhMuc dm ON sp.MaDM = dm.MaDM
                LEFT JOIN NhaXuatBan nxb ON sp.MaNXB = nxb.MaNXB
                LEFT JOIN SanPham_TacGia sptg ON sp.MaSP = sptg.MaSP
                LEFT JOIN TacGia tg ON sptg.MaTacGia = tg.MaTacGia
                WHERE sp.TenSP LIKE ? OR tg.TenTacGia LIKE ?
                GROUP BY sp.MaSP
                ORDER BY sp.NgayCapNhat DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { die("Lỗi SQL search: " . $this->conn->error); }
        
        // Bind 2 tham số (cho TenSP và TenTacGia)
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
