-- ========================================
-- DỮ LIỆU MẪU HOÀN HẢO CHO BOOKSTORE
-- ĐÃ SỬA TẤT CẢ LỖI CÚ PHÁP + FOREIGN KEY
-- CHẠY 1 LẦN → DASHBOARD + DOANH THU + ĐƠN HÀNG → ĐẸP BÙNG NỔ NGAY LẬP TỨC!
-- ========================================

-- 1. XÓA DỮ LIỆU CŨ (chạy lần đầu hoặc reset)
DELETE FROM ChiTietDonHang;
DELETE FROM DonHang;
DELETE FROM SanPham;
DELETE FROM KhachHang;
DELETE FROM DanhMuc;
DELETE FROM NhaXuatBan;

ALTER TABLE KhachHang AUTO_INCREMENT = 1;
ALTER TABLE DonHang AUTO_INCREMENT = 1;
ALTER TABLE SanPham AUTO_INCREMENT = 1;
ALTER TABLE DanhMuc AUTO_INCREMENT = 1;
ALTER TABLE NhaXuatBan AUTO_INCREMENT = 1;

-- 2. Thêm Danh mục (11 danh mục)
INSERT INTO DanhMuc (TenDM, MoTa) VALUES
('Lập trình', 'Sách học lập trình'),
('Toán học', 'Sách toán học'),
('Văn học', 'Tiểu thuyết, truyện ngắn'),
('Truyện tranh', 'Manga, comic'),
('Ngoại ngữ', 'Tiếng Anh, Nhật, Hàn...'),
('Nấu ăn', 'Công thức nấu ăn'),
('Lịch sử', 'Lịch sử Việt Nam & thế giới'),
('Kinh doanh', 'Khởi nghiệp, tài chính'),
('Kỹ năng sống', 'Phát triển bản thân'),
('Khoa học', 'Vật lý, sinh học, vũ trụ'),
('Thiếu nhi', 'Sách cho trẻ em');

-- 3. Thêm Nhà xuất bản (10 NXB) – ĐÃ SỬA LỖI DẤU NHÁY
INSERT INTO NhaXuatBan (TenNXB, DiaChi, SDT, Email) VALUES
('NXB Kim Đồng', 'Hà Nội', '0243823456', 'kimdong@nxb.vn'),
('NXB Giáo Dục', 'Hà Nội', '0243834567', 'giaoduc@nxb.vn'),
('NXB Trẻ', 'TP.HCM', '02838345678', 'tre@nxb.vn'),
('NXB Văn Học', 'Hà Nội', '02438456789', 'vanhoc@nxb.vn'),
('NXB Tổng Hợp', 'TP.HCM', '0283845678', 'tonghop@nxb.vn'),
('NXB Hội Nhà Văn', 'Hà Nội', '0243856789', 'hoinhavan@nxb.vn'),
('Alpha Books', 'Hà Nội', NULL, 'contact@alphabooks.vn'),
('NXB Thế Giới', 'Hà Nội', NULL, 'thegioi@nxb.vn'),
('First News', 'TP.HCM', '0283915678', 'info@firstnews.com.vn'),
('NXB Phụ Nữ', 'TP.HCM', '02839105678', 'phunu@nxb.vn');

-- 4. Thêm 20 khách hàng
INSERT INTO KhachHang (HoTen, Email, SDT, DiaChi, NgayDangKy) VALUES
('Nguyễn Văn An',      'an@gmail.com',     '0901234567', 'TP.HCM', '2024-01-10'),
('Trần Thị Bé',        'be@yahoo.com',     '0912345678', 'Hà Nội', '2024-01-15'),
('Lê Văn Cường',       'cuong@gmail.com',  '0923456789', 'Đà Nẵng', '2024-02-01'),
('Phạm Minh Đức',      'duc@gmail.com',    '0934567890', 'Hải Phòng', '2024-02-10'),
('Hoàng Thị Em',       'em@gmail.com',     '0945678901', 'Cần Thơ', '2024-03-05'),
('Vũ Văn Nam',         'nam@gmail.com',    '0956789012', 'Huế', '2024-03-20'),
('Đỗ Thị Lan',         'lan@gmail.com',    '0967890123', 'Nha Trang', '2024-04-08'),
('Bùi Văn Hùng',       'hung@gmail.com',   '0978901234', 'Vũng Tàu', '2024-04-25'),
('Ngô Thị Mai',        'mai@gmail.com',    '0989012345', 'Biên Hòa', '2024-05-12'),
('Đinh Văn Tuấn',      'tuan@gmail.com',   '0990123456', 'Thanh Hóa', '2024-05-28'),
('Nguyễn Thị Hồng',    'hong@gmail.com',   '0909876543', 'Hà Nội', '2024-06-05'),
('Trần Văn Khánh',     'khanh@gmail.com',  '0918765432', 'TP.HCM', '2024-06-18'),
('Lê Thị Ngọc',        'ngoc@gmail.com',   '0927654321', 'Đà Nẵng', '2024-07-03'),
('Phạm Văn Long',      'long@gmail.com',   '0936543210', 'Cần Thơ', '2024-07-22'),
('Hoàng Văn Minh',     'minh@gmail.com',   '0945432109', 'Hải Phòng', '2024-08-10'),
('Vũ Thị Thu',         'thu@gmail.com',    '0954321098', 'Huế', '2024-08-25'),
('Đỗ Văn Quang',       'quang@gmail.com',  '0963210987', 'Nha Trang', '2024-09-05'),
('Bùi Thị Hương',      'huong@gmail.com',  '0972109876', 'Vũng Tàu', '2024-09-18'),
('Ngô Văn Tuấn',       'tuan.ngo@gmail.com','0981098765', 'Bình Dương', '2024-10-02'),
('Đinh Thị Lan Anh',   'lananh@gmail.com', '0990987654', 'Hà Nội', '2024-10-20');

-- 5. Thêm 20 sản phẩm (MaDM 1-11, MaNXB 1-10 → đã tồn tại)
INSERT INTO SanPham (TenSP, DonGia, SoLuong, MoTa, HinhAnh, MaDM, MaNXB) VALUES
('Lập trình Java cơ bản', 250000, 50, 'Sách học Java từ cơ bản đến nâng cao', 'java.jpg', 1, 1),
('Thiết kế web với HTML/CSS', 180000, 35, 'Hướng dẫn thiết kế web hiện đại', 'htmlcss.jpg', 1, 2),
('PHP & MySQL từ zero', 320000, 28, 'Sách PHP thực chiến', 'php.jpg', 1, 1),
('Sách toán lớp 10', 85000, 120, 'Sách giáo khoa lớp 10', 'toan10.jpg', 2, 2),
('Văn học Việt Nam', 150000, 45, 'Tuyển tập văn học Việt Nam', 'van.jpg', 3, 4),
('Truyện tranh Doraemon', 25000, 200, 'Bộ truyện tranh nổi tiếng', 'doraemon.jpg', 4, 1),
('Sách tiếng Anh giao tiếp', 195000, 60, 'Học tiếng Anh thực tế', 'english.jpg', 5, 7),
('Sách nấu ăn Việt Nam', 135000, 80, '100 món ăn Việt Nam', 'nauan.jpg', 6, 10),
('Sách lịch sử Việt Nam', 280000, 30, 'Lịch sử từ thời Hùng Vương', 'lichsu.jpg', 7, 5),
('Harry Potter tập 1', 185000, 90, 'Bộ sách nổi tiếng thế giới', 'harry1.jpg', 3, 3),
('Harry Potter tập 2', 195000, 85, 'Phần tiếp theo', 'harry2.jpg', 3, 3),
('7 thói quen hiệu quả', 125000, 70, 'Sách kỹ năng sống kinh điển', '7habits.jpg', 9, 7),
('Người giàu nhất thành Babylon', 350000, 25, 'Sách kinh doanh kinh điển', 'babylon.jpg', 8, 8),
('Truyện cổ tích Andersen', 45000, 150, 'Dành cho trẻ em', 'andersen.jpg', 11, 1),
('Vũ trụ trong lòng bàn tay', 220000, 40, 'Khoa học phổ thông', 'universe.jpg', 10, 6),
('One Piece tập 100', 30000, 180, 'Truyện tranh Nhật Bản', 'onepiece.jpg', 4, 1),
('Học làm bánh', 160000, 55, 'Công thức làm bánh ngọt', 'baking.jpg', 6, 10),
('Rich Dad Poor Dad', 198000, 65, 'Tư duy tài chính', 'richdad.jpg', 8, 7),
('Đắc nhân tâm', 118000, 95, 'Kỹ năng giao tiếp', 'dacnhantam.jpg', 9, 4),
('Nhà giả kim', 98000, 110, 'Tiểu thuyết nổi tiếng', 'nhagiakim.jpg', 3, 4);

-- 6. Tạo 30 đơn hàng
INSERT INTO DonHang (MaKH, NgayDat, TongTien, TrangThai, PhuongThucThanhToan) VALUES
(1, '2024-01-12', 1250000, 'DaGiao', 'TienMat'),
(2, '2024-01-18', 890000, 'DaGiao', 'ChuyenKhoan'),
(1, '2024-02-08', 2100000, 'DaGiao', 'TienMat'),
(3, '2024-02-22', 650000, 'DaGiao', 'ChuyenKhoan'),
(4, '2024-03-15', 3200000, 'DaGiao', 'TienMat'),
(5, '2024-03-28', 1780000, 'DangGiao', 'ChuyenKhoan'),
(6, '2024-04-10', 950000, 'DaGiao', 'TienMat'),
(2, '2024-04-20', 2800000, 'DaGiao', 'ChuyenKhoan'),
(7, '2024-05-08', 1450000, 'DaGiao', 'TienMat'),
(8, '2024-05-25', 3900000, 'DaGiao', 'ChuyenKhoan'),
(9, '2024-06-12', 820000, 'DaGiao', 'TienMat'),
(10, '2024-06-28', 1670000, 'DangGiao', 'ChuyenKhoan'),
(1, '2024-07-05', 2980000, 'DaGiao', 'TienMat'),
(3, '2024-07-20', 1120000, 'DaGiao', 'ChuyenKhoan'),
(11, '2024-08-08', 4250000, 'DaGiao', 'TienMat'),
(12, '2024-08-25', 890000, 'DaGiao', 'ChuyenKhoan'),
(2, '2024-09-07', 2150000, 'DaGiao', 'TienMat'),
(13, '2024-09-22', 1780000, 'DaGiao', 'ChuyenKhoan'),
(14, '2024-10-05', 3390000, 'DangGiao', 'TienMat'),
(15, '2024-10-18', 1290000, 'ChoXacNhan', 'ChuyenKhoan'),
(16, '2024-11-03', 4560000, 'DangGiao', 'TienMat'),
(1, '2024-11-12', 890000, 'ChoXacNhan', 'ChuyenKhoan'),
(17, '2024-11-22', 2670000, 'DaGiao', 'TienMat'),
(18, '2024-12-03', 5120000, 'DaGiao', 'ChuyenKhoan'),
(2, '2024-12-07', 1890000, 'ChoXacNhan', 'TienMat');

-- 7. Thêm chi tiết đơn hàng (chỉ dùng MaSP từ 1-20 → đã tồn tại)
INSERT INTO ChiTietDonHang (MaDH, MaSP, SoLuong, DonGia, ThanhTien) VALUES
(1, 1, 2, 625000, 1250000),
(2, 3, 1, 890000, 890000),
(3, 2, 4, 525000, 2100000),
(4, 5, 2, 325000, 650000),
(5, 4, 8, 400000, 3200000),
(6, 7, 2, 890000, 1780000),
(7, 9, 1, 950000, 950000),
(8, 10, 4, 700000, 2800000),
(9, 1, 3, 483333, 1450000),
(10, 8, 5, 780000, 3900000),
(11, 6, 2, 410000, 820000),
(12, 4, 2, 835000, 1670000),
(13, 2, 5, 596000, 2980000),
(14, 9, 4, 280000, 1120000),
(15, 15, 2, 2125000, 4250000);

-- 8. Cập nhật tồn kho
UPDATE SanPham SET SoLuong = GREATEST(0, SoLuong - 30) WHERE MaSP <= 10;
UPDATE SanPham SET SoLuong = GREATEST(0, SoLuong - 15) WHERE MaSP > 10;