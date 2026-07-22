-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 18, 2025 lúc 07:11 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `bookstore_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonhang`
--

DROP TABLE IF EXISTS `chitietdonhang`;
CREATE TABLE `chitietdonhang` (
  `MaDH` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `DonGia` decimal(10,2) DEFAULT NULL,
  `ThanhTien` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`MaDH`, `MaSP`, `SoLuong`, `DonGia`, `ThanhTien`) VALUES
(1, 1, 2, 625000.00, 1250000.00),
(2, 3, 1, 890000.00, 890000.00),
(3, 2, 4, 525000.00, 2100000.00),
(4, 5, 2, 325000.00, 650000.00),
(5, 4, 8, 400000.00, 3200000.00),
(6, 7, 2, 890000.00, 1780000.00),
(7, 9, 1, 950000.00, 950000.00),
(8, 10, 4, 700000.00, 2800000.00),
(9, 1, 3, 483333.00, 1450000.00),
(10, 8, 5, 780000.00, 3900000.00),
(11, 6, 2, 410000.00, 820000.00),
(12, 4, 2, 835000.00, 1670000.00),
(13, 2, 5, 596000.00, 2980000.00),
(14, 9, 4, 280000.00, 1120000.00),
(15, 15, 2, 2125000.00, 4250000.00),
(26, 4, 1, 85000.00, 85000.00),
(26, 15, 1, 220000.00, 220000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietgiohang`
--

DROP TABLE IF EXISTS `chitietgiohang`;
CREATE TABLE `chitietgiohang` (
  `MaGH` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `DonGiaTamTinh` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietgiohang`
--

INSERT INTO `chitietgiohang` (`MaGH`, `MaSP`, `SoLuong`, `DonGiaTamTinh`) VALUES
(2, 1, 1, 250000.00),
(2, 15, 1, 220000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietnhaphang`
--

DROP TABLE IF EXISTS `chitietnhaphang`;
CREATE TABLE `chitietnhaphang` (
  `MaNhap` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `SoLuongNhap` int(11) DEFAULT NULL,
  `DonGiaNhap` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietnhaphang`
--

INSERT INTO `chitietnhaphang` (`MaNhap`, `MaSP`, `SoLuongNhap`, `DonGiaNhap`) VALUES
(4, 2, 10, 50000.00),
(4, 10, 15, 0.00),
(5, 1, 50, 50000.00),
(7, 2, 100, 50000.00),
(8, 22, 1, 1000000.00),
(9, 22, 10, 100000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

DROP TABLE IF EXISTS `danhmuc`;
CREATE TABLE `danhmuc` (
  `MaDM` int(11) NOT NULL,
  `TenDM` varchar(100) NOT NULL,
  `MoTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc`
--

INSERT INTO `danhmuc` (`MaDM`, `TenDM`, `MoTa`) VALUES
(1, 'Lập trình', 'Sách học lập trình'),
(2, 'Toán học', 'Sách toán học'),
(3, 'Văn học', 'Tiểu thuyết, truyện ngắn'),
(4, 'Truyện tranh', 'Manga, comic'),
(5, 'Ngoại ngữ', 'Tiếng Anh, Nhật, Hàn...'),
(6, 'Nấu ăn', 'Công thức nấu ăn'),
(7, 'Lịch sử', 'Lịch sử Việt Nam & thế giới'),
(8, 'Kinh doanh', 'Khởi nghiệp, tài chính'),
(9, 'Kỹ năng sống', 'Phát triển bản thân'),
(10, 'Khoa học', 'Vật lý, sinh học, vũ trụ'),
(11, 'Thiếu nhi', 'Sách cho trẻ em');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doanhthu`
--

DROP TABLE IF EXISTS `doanhthu`;
CREATE TABLE `doanhthu` (
  `MaBC` int(11) NOT NULL,
  `Thang` int(11) DEFAULT NULL,
  `Nam` int(11) DEFAULT NULL,
  `TongDoanhThu` decimal(12,2) DEFAULT NULL,
  `LoiNhuan` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `doanhthu`
--

INSERT INTO `doanhthu` (`MaBC`, `Thang`, `Nam`, `TongDoanhThu`, `LoiNhuan`) VALUES
(1, 6, 2025, 10000000.00, 3000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

DROP TABLE IF EXISTS `donhang`;
CREATE TABLE `donhang` (
  `MaDH` int(11) NOT NULL,
  `NgayDat` datetime DEFAULT current_timestamp(),
  `TongTien` decimal(12,2) DEFAULT NULL,
  `TrangThai` enum('ChoXacNhan','DangGiao','DaGiao','DaHuy') DEFAULT 'ChoXacNhan',
  `PhuongThucThanhToan` enum('TienMat','ChuyenKhoan') DEFAULT 'TienMat',
  `MaKH` int(11) DEFAULT NULL,
  `DiaChiGiaoHang` varchar(255) NOT NULL,
  `MaKM` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `donhang`
--

INSERT INTO `donhang` (`MaDH`, `NgayDat`, `TongTien`, `TrangThai`, `PhuongThucThanhToan`, `MaKH`, `DiaChiGiaoHang`, `MaKM`) VALUES
(1, '2024-01-12 00:00:00', 1250000.00, 'DaGiao', 'TienMat', 1, '', NULL),
(2, '2024-01-18 00:00:00', 890000.00, 'DaGiao', 'ChuyenKhoan', 2, '', NULL),
(3, '2024-02-08 00:00:00', 2100000.00, 'DaGiao', 'TienMat', 1, '', NULL),
(4, '2024-02-22 00:00:00', 650000.00, 'DaGiao', 'ChuyenKhoan', 3, '', NULL),
(5, '2024-03-15 00:00:00', 3200000.00, 'DaGiao', 'TienMat', 4, '', NULL),
(6, '2024-03-28 00:00:00', 1780000.00, 'DangGiao', 'ChuyenKhoan', 5, '', NULL),
(7, '2024-04-10 00:00:00', 950000.00, 'DaGiao', 'TienMat', 6, '', NULL),
(8, '2024-04-20 00:00:00', 2800000.00, 'DaGiao', 'ChuyenKhoan', 2, '', NULL),
(9, '2024-05-08 00:00:00', 1450000.00, 'DaGiao', 'TienMat', 7, '', NULL),
(10, '2024-05-25 00:00:00', 3900000.00, 'DaGiao', 'ChuyenKhoan', 8, '', NULL),
(11, '2024-06-12 00:00:00', 820000.00, 'DaGiao', 'TienMat', 9, '', NULL),
(12, '2024-06-28 00:00:00', 1670000.00, 'DangGiao', 'ChuyenKhoan', 10, '', NULL),
(13, '2024-07-05 00:00:00', 2980000.00, 'DaGiao', 'TienMat', 1, '', NULL),
(14, '2024-07-20 00:00:00', 1120000.00, 'DaGiao', 'ChuyenKhoan', 3, '', NULL),
(15, '2024-08-08 00:00:00', 4250000.00, 'DaGiao', 'TienMat', 11, '', NULL),
(16, '2024-08-25 00:00:00', 890000.00, 'DaGiao', 'ChuyenKhoan', 12, '', NULL),
(17, '2024-09-07 00:00:00', 2150000.00, 'DaGiao', 'TienMat', 2, '', NULL),
(18, '2024-09-22 00:00:00', 1780000.00, 'DaGiao', 'ChuyenKhoan', 13, '', NULL),
(19, '2024-10-05 00:00:00', 3390000.00, 'DangGiao', 'TienMat', 14, '', NULL),
(20, '2024-10-18 00:00:00', 1290000.00, 'ChoXacNhan', 'ChuyenKhoan', 15, '', NULL),
(21, '2024-11-03 00:00:00', 4560000.00, 'DangGiao', 'TienMat', 16, '', NULL),
(22, '2024-11-12 00:00:00', 890000.00, 'ChoXacNhan', 'ChuyenKhoan', 1, '', NULL),
(23, '2024-11-22 00:00:00', 2670000.00, 'DaGiao', 'TienMat', 17, '', NULL),
(24, '2024-12-03 00:00:00', 5120000.00, 'DaGiao', 'ChuyenKhoan', 18, '', NULL),
(25, '2024-12-07 00:00:00', 1890000.00, 'ChoXacNhan', 'TienMat', 2, '', NULL),
(26, '2025-12-17 19:55:54', 305000.00, 'ChoXacNhan', 'TienMat', NULL, '', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giohang`
--

DROP TABLE IF EXISTS `giohang`;
CREATE TABLE `giohang` (
  `MaGH` int(11) NOT NULL,
  `MaKH` int(11) DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `giohang`
--

INSERT INTO `giohang` (`MaGH`, `MaKH`, `NgayTao`) VALUES
(2, 21, '2025-12-18 10:17:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinhanhsanpham`
--

DROP TABLE IF EXISTS `hinhanhsanpham`;
CREATE TABLE `hinhanhsanpham` (
  `MaHinh` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `DuongDan` varchar(255) NOT NULL,
  `LaAnhChinh` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hinhanhsanpham`
--

INSERT INTO `hinhanhsanpham` (`MaHinh`, `MaSP`, `DuongDan`, `LaAnhChinh`) VALUES
(24, 22, '22_1765964718_0.JPG', 1),
(25, 22, '22_1765964718_1.JPG', 0),
(27, 23, '23_1765964737_0.JPG', 1),
(28, 23, '23_1765964737_1.JPG', 0),
(29, 23, '23_1765964737_2.JPG', 0),
(30, 23, '23_1765964737_3.JPG', 0),
(31, 1, '1765965143_0.png', 1),
(32, 1, '1765965179_20240116_57jvPK0Fr0.jpeg', 0),
(33, 1, '1765965179_20240725_AEL14wwuzA.jpeg', 0),
(34, 1, '1765965179_20240725_ceQoz6ZtfV.jpeg', 0),
(35, 1, '1765965179_20240725_ILimDjGOw9.jpeg', 0),
(36, 1, '1765965179_20240725_IxF54EpLpq.jpeg', 0),
(37, 1, '1765965179_20240725_kirFD6lp2L.jpeg', 0),
(38, 2, '1765965188_0.png', 0),
(39, 2, '1765965188_20240116_57jvPK0Fr0.jpeg', 1),
(40, 2, '1765965188_20240725_AEL14wwuzA.jpeg', 0),
(41, 2, '1765965188_20240725_ceQoz6ZtfV.jpeg', 0),
(42, 2, '1765965188_20240725_ILimDjGOw9.jpeg', 0),
(43, 2, '1765965188_20240725_IxF54EpLpq.jpeg', 0),
(44, 2, '1765965188_20240725_kirFD6lp2L.jpeg', 0),
(45, 3, '1765965203_0.png', 0),
(46, 3, '1765965203_20240116_57jvPK0Fr0.jpeg', 0),
(47, 3, '1765965203_20240725_AEL14wwuzA.jpeg', 0),
(48, 3, '1765965203_20240725_ceQoz6ZtfV.jpeg', 1),
(49, 3, '1765965203_20240725_ILimDjGOw9.jpeg', 0),
(50, 3, '1765965203_20240725_IxF54EpLpq.jpeg', 0),
(51, 3, '1765965203_20240725_kirFD6lp2L.jpeg', 0),
(52, 4, '1765965216_0.png', 1),
(53, 4, '1765965216_20240116_57jvPK0Fr0.jpeg', 0),
(54, 4, '1765965216_20240725_AEL14wwuzA.jpeg', 0),
(55, 4, '1765965216_20240725_ceQoz6ZtfV.jpeg', 0),
(56, 4, '1765965216_20240725_ILimDjGOw9.jpeg', 0),
(57, 4, '1765965216_20240725_IxF54EpLpq.jpeg', 0),
(58, 4, '1765965216_20240725_kirFD6lp2L.jpeg', 0),
(59, 5, '1765965242_0.png', 1),
(60, 5, '1765965242_20240116_57jvPK0Fr0.jpeg', 0),
(61, 5, '1765965242_20240725_AEL14wwuzA.jpeg', 0),
(62, 5, '1765965242_20240725_ceQoz6ZtfV.jpeg', 0),
(63, 5, '1765965242_20240725_ILimDjGOw9.jpeg', 0),
(64, 5, '1765965242_20240725_IxF54EpLpq.jpeg', 0),
(65, 5, '1765965242_20240725_kirFD6lp2L.jpeg', 0),
(66, 6, '1765965255_0.png', 1),
(67, 6, '1765965255_20240116_57jvPK0Fr0.jpeg', 0),
(68, 6, '1765965255_20240725_AEL14wwuzA.jpeg', 0),
(69, 6, '1765965255_20240725_ceQoz6ZtfV.jpeg', 0),
(70, 6, '1765965255_20240725_ILimDjGOw9.jpeg', 0),
(71, 6, '1765965255_20240725_IxF54EpLpq.jpeg', 0),
(72, 6, '1765965255_20240725_kirFD6lp2L.jpeg', 0),
(73, 7, '1765965275_0.png', 1),
(74, 7, '1765965275_20240116_57jvPK0Fr0.jpeg', 0),
(75, 7, '1765965275_20240725_AEL14wwuzA.jpeg', 0),
(76, 7, '1765965275_20240725_ceQoz6ZtfV.jpeg', 0),
(77, 7, '1765965275_20240725_ILimDjGOw9.jpeg', 0),
(78, 7, '1765965275_20240725_IxF54EpLpq.jpeg', 0),
(79, 7, '1765965275_20240725_kirFD6lp2L.jpeg', 0),
(80, 8, '1765965362_0.png', 1),
(81, 8, '1765965362_20240116_57jvPK0Fr0.jpeg', 0),
(82, 8, '1765965362_20240725_AEL14wwuzA.jpeg', 0),
(83, 8, '1765965362_20240725_ceQoz6ZtfV.jpeg', 0),
(84, 8, '1765965362_20240725_ILimDjGOw9.jpeg', 0),
(85, 8, '1765965362_20240725_IxF54EpLpq.jpeg', 0),
(86, 8, '1765965362_20240725_kirFD6lp2L.jpeg', 0),
(87, 9, '1765965378_0.png', 0),
(88, 9, '1765965378_20240116_57jvPK0Fr0.jpeg', 0),
(89, 9, '1765965378_20240725_AEL14wwuzA.jpeg', 0),
(90, 9, '1765965378_20240725_ceQoz6ZtfV.jpeg', 0),
(91, 9, '1765965378_20240725_ILimDjGOw9.jpeg', 0),
(92, 9, '1765965378_20240725_IxF54EpLpq.jpeg', 1),
(93, 9, '1765965378_20240725_kirFD6lp2L.jpeg', 0),
(94, 10, '1765965406_0.png', 1),
(95, 10, '1765965406_20240116_57jvPK0Fr0.jpeg', 0),
(96, 10, '1765965406_20240725_AEL14wwuzA.jpeg', 0),
(97, 10, '1765965406_20240725_ceQoz6ZtfV.jpeg', 0),
(98, 10, '1765965406_20240725_ILimDjGOw9.jpeg', 0),
(99, 10, '1765965406_20240725_IxF54EpLpq.jpeg', 0),
(100, 10, '1765965406_20240725_kirFD6lp2L.jpeg', 0),
(101, 11, '1765965417_0.png', 1),
(102, 11, '1765965417_20240116_57jvPK0Fr0.jpeg', 0),
(103, 11, '1765965417_20240725_AEL14wwuzA.jpeg', 0),
(104, 11, '1765965417_20240725_ceQoz6ZtfV.jpeg', 0),
(105, 11, '1765965417_20240725_ILimDjGOw9.jpeg', 0),
(106, 11, '1765965417_20240725_IxF54EpLpq.jpeg', 0),
(107, 11, '1765965417_20240725_kirFD6lp2L.jpeg', 0),
(108, 12, '1765965429_0.png', 1),
(109, 13, '1765965441_0.png', 0),
(110, 13, '1765965441_20240116_57jvPK0Fr0.jpeg', 0),
(111, 13, '1765965441_20240725_AEL14wwuzA.jpeg', 0),
(112, 13, '1765965441_20240725_ceQoz6ZtfV.jpeg', 0),
(113, 13, '1765965441_20240725_ILimDjGOw9.jpeg', 0),
(114, 13, '1765965441_20240725_IxF54EpLpq.jpeg', 1),
(115, 13, '1765965441_20240725_kirFD6lp2L.jpeg', 0),
(116, 14, '1765965454_0.png', 0),
(117, 14, '1765965454_20240116_57jvPK0Fr0.jpeg', 0),
(118, 14, '1765965454_20240725_AEL14wwuzA.jpeg', 0),
(119, 14, '1765965454_20240725_ceQoz6ZtfV.jpeg', 0),
(120, 14, '1765965454_20240725_ILimDjGOw9.jpeg', 0),
(121, 14, '1765965454_20240725_IxF54EpLpq.jpeg', 0),
(122, 14, '1765965454_20240725_kirFD6lp2L.jpeg', 1),
(123, 15, '1765965466_0.png', 0),
(124, 15, '1765965466_20240116_57jvPK0Fr0.jpeg', 1),
(125, 15, '1765965466_20240725_AEL14wwuzA.jpeg', 0),
(126, 15, '1765965466_20240725_ceQoz6ZtfV.jpeg', 0),
(127, 15, '1765965466_20240725_ILimDjGOw9.jpeg', 0),
(128, 15, '1765965466_20240725_IxF54EpLpq.jpeg', 0),
(129, 15, '1765965466_20240725_kirFD6lp2L.jpeg', 0),
(130, 16, '1765965478_20240725_AEL14wwuzA.jpeg', 1),
(131, 16, '1765965478_20240725_ceQoz6ZtfV.jpeg', 0),
(132, 16, '1765965478_20240725_ILimDjGOw9.jpeg', 0),
(133, 17, '1765965490_0.png', 1),
(134, 17, '1765965490_20240116_57jvPK0Fr0.jpeg', 0),
(135, 17, '1765965490_20240725_AEL14wwuzA.jpeg', 0),
(136, 17, '1765965490_20240725_ceQoz6ZtfV.jpeg', 0),
(137, 17, '1765965490_20240725_ILimDjGOw9.jpeg', 0),
(138, 17, '1765965490_20240725_IxF54EpLpq.jpeg', 0),
(139, 17, '1765965490_20240725_kirFD6lp2L.jpeg', 0),
(140, 18, '1765965504_20240725_AEL14wwuzA.jpeg', 0),
(141, 18, '1765965504_20240725_ceQoz6ZtfV.jpeg', 1),
(142, 18, '1765965504_20240725_ILimDjGOw9.jpeg', 0),
(143, 18, '1765965504_20240725_IxF54EpLpq.jpeg', 0),
(144, 19, '1765965510_0.png', 0),
(145, 19, '1765965510_20240116_57jvPK0Fr0.jpeg', 0),
(146, 19, '1765965510_20240725_AEL14wwuzA.jpeg', 0),
(147, 19, '1765965510_20240725_ceQoz6ZtfV.jpeg', 0),
(148, 19, '1765965510_20240725_ILimDjGOw9.jpeg', 0),
(149, 19, '1765965510_20240725_IxF54EpLpq.jpeg', 1),
(150, 19, '1765965510_20240725_kirFD6lp2L.jpeg', 0),
(151, 20, '1765965516_0.png', 0),
(152, 20, '1765965516_20240116_57jvPK0Fr0.jpeg', 1),
(153, 20, '1765965516_20240725_AEL14wwuzA.jpeg', 0),
(154, 20, '1765965516_20240725_ceQoz6ZtfV.jpeg', 0),
(155, 20, '1765965516_20240725_ILimDjGOw9.jpeg', 0),
(156, 20, '1765965516_20240725_IxF54EpLpq.jpeg', 0),
(157, 20, '1765965516_20240725_kirFD6lp2L.jpeg', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

DROP TABLE IF EXISTS `khachhang`;
CREATE TABLE `khachhang` (
  `MaKH` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `SDT` varchar(15) DEFAULT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `NgayDangKy` datetime DEFAULT current_timestamp(),
  `MaTK` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`MaKH`, `HoTen`, `Email`, `SDT`, `DiaChi`, `NgayDangKy`, `MaTK`) VALUES
(1, 'Nguyễn Văn An', 'an@gmail.com', '0901234567', 'TP.HCM', '2024-01-10 00:00:00', NULL),
(2, 'Trần Thị Bé', 'be@yahoo.com', '0912345678', 'Hà Nội', '2024-01-15 00:00:00', NULL),
(3, 'Lê Văn Cường', 'cuong@gmail.com', '0923456789', 'Đà Nẵng', '2024-02-01 00:00:00', NULL),
(4, 'Phạm Minh Đức', 'duc@gmail.com', '0934567890', 'Hải Phòng', '2024-02-10 00:00:00', NULL),
(5, 'Hoàng Thị Em', 'em@gmail.com', '0945678901', 'Cần Thơ', '2024-03-05 00:00:00', NULL),
(6, 'Vũ Văn Nam', 'nam@gmail.com', '0956789012', 'Huế', '2024-03-20 00:00:00', NULL),
(7, 'Đỗ Thị Lan', 'lan@gmail.com', '0967890123', 'Nha Trang', '2024-04-08 00:00:00', NULL),
(8, 'Bùi Văn Hùng', 'hung@gmail.com', '0978901234', 'Vũng Tàu', '2024-04-25 00:00:00', NULL),
(9, 'Ngô Thị Mai', 'mai@gmail.com', '0989012345', 'Biên Hòa', '2024-05-12 00:00:00', NULL),
(10, 'Đinh Văn Tuấn', 'tuan@gmail.com', '0990123456', 'Thanh Hóa', '2024-05-28 00:00:00', NULL),
(11, 'Nguyễn Thị Hồng', 'hong@gmail.com', '0909876543', 'Hà Nội', '2024-06-05 00:00:00', NULL),
(12, 'Trần Văn Khánh', 'khanh@gmail.com', '0918765432', 'TP.HCM', '2024-06-18 00:00:00', NULL),
(13, 'Lê Thị Ngọc', 'ngoc@gmail.com', '0927654321', 'Đà Nẵng', '2024-07-03 00:00:00', NULL),
(14, 'Phạm Văn Long', 'long@gmail.com', '0936543210', 'Cần Thơ', '2024-07-22 00:00:00', NULL),
(15, 'Hoàng Văn Minh', 'minh@gmail.com', '0945432109', 'Hải Phòng', '2024-08-10 00:00:00', NULL),
(16, 'Vũ Thị Thu', 'thu@gmail.com', '0954321098', 'Huế', '2024-08-25 00:00:00', NULL),
(17, 'Đỗ Văn Quang', 'quang@gmail.com', '0963210987', 'Nha Trang', '2024-09-05 00:00:00', NULL),
(18, 'Bùi Thị Hương', 'huong@gmail.com', '0972109876', 'Vũng Tàu', '2024-09-18 00:00:00', NULL),
(19, 'Ngô Văn Tuấn', 'tuan.ngo@gmail.com', '0981098765', 'Bình Dương', '2024-10-02 00:00:00', NULL),
(20, 'Đinh Thị Lan Anh', 'lananh@gmail.com', '0990987654', 'Hà Nội', '2024-10-20 00:00:00', NULL),
(21, 'Hoàng Duy An', 'hoangduyankt@gmail.com', '0392419113', 'Xã Kim Trung, huyện Hưng Hà, tỉnh Thái Bình', '2025-12-18 09:38:11', 6);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyenmai`
--

DROP TABLE IF EXISTS `khuyenmai`;
CREATE TABLE `khuyenmai` (
  `MaKM` int(11) NOT NULL,
  `TenKM` varchar(100) DEFAULT NULL,
  `PhanTramGiam` decimal(5,2) DEFAULT NULL,
  `NgayBatDau` date DEFAULT NULL,
  `NgayKetThuc` date DEFAULT NULL,
  `DieuKien` varchar(255) DEFAULT NULL,
  `LoaiKM` enum('ToanDon','DanhMuc') DEFAULT 'ToanDon',
  `MaDM` int(11) DEFAULT NULL,
  `DieuKienToiThieu` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khuyenmai`
--

INSERT INTO `khuyenmai` (`MaKM`, `TenKM`, `PhanTramGiam`, `NgayBatDau`, `NgayKetThuc`, `DieuKien`, `LoaiKM`, `MaDM`, `DieuKienToiThieu`) VALUES
(1, 'Giảm 10%', 10.00, '2025-01-01', '2025-12-31', 'Đơn hàng > 200k', 'ToanDon', NULL, 0.00),
(2, 'Giảm 20%', 20.00, '2025-06-01', '2025-06-30', 'Khách VIP', 'ToanDon', NULL, 0.00),
(3, 'Truyện Kiều', 20.00, '2004-12-01', '2004-12-01', '', 'DanhMuc', NULL, 300000.00),
(4, 'anque', 20.00, '2025-12-15', '2025-12-25', '', 'ToanDon', NULL, 0.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lichsunhaphang`
--

DROP TABLE IF EXISTS `lichsunhaphang`;
CREATE TABLE `lichsunhaphang` (
  `MaNhap` int(11) NOT NULL,
  `NgayNhap` datetime DEFAULT current_timestamp(),
  `MaNCC` int(11) DEFAULT NULL,
  `TongTienNhap` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `lichsunhaphang`
--

INSERT INTO `lichsunhaphang` (`MaNhap`, `NgayNhap`, `MaNCC`, `TongTienNhap`) VALUES
(1, '2025-12-04 13:11:58', 1, 5000000.00),
(2, '2025-12-04 13:11:58', 2, 3000000.00),
(3, '2025-12-04 23:44:27', 2, 320000.00),
(4, '2025-12-05 16:53:40', 2, 500000.00),
(5, '2025-12-05 16:54:21', 2, 2500000.00),
(6, '2025-12-05 16:55:16', 2, 500000.00),
(7, '2025-12-17 18:28:51', 2, 5000000.00),
(8, '2025-12-17 18:29:17', 2, 1000000.00),
(9, '2025-12-17 18:29:42', 2, 1000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacungcap`
--

DROP TABLE IF EXISTS `nhacungcap`;
CREATE TABLE `nhacungcap` (
  `MaNCC` int(11) NOT NULL,
  `TenNCC` varchar(100) DEFAULT NULL,
  `SDT` varchar(15) DEFAULT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhacungcap`
--

INSERT INTO `nhacungcap` (`MaNCC`, `TenNCC`, `SDT`, `DiaChi`, `Email`) VALUES
(1, 'Công ty phát hành A', '0901111111', 'Hà Nội', 'ctyA@gmail.com'),
(2, 'Công ty phát hành B', '0902222222', 'TP HCM', 'ctyB@gmail.com');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhaxuatban`
--

DROP TABLE IF EXISTS `nhaxuatban`;
CREATE TABLE `nhaxuatban` (
  `MaNXB` int(11) NOT NULL,
  `TenNXB` varchar(100) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `SDT` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhaxuatban`
--

INSERT INTO `nhaxuatban` (`MaNXB`, `TenNXB`, `DiaChi`, `SDT`, `Email`) VALUES
(1, 'NXB Kim Đồng', 'Hà Nội', '0243823456', 'kimdong@nxb.vn'),
(2, 'NXB Giáo Dục', 'Hà Nội', '0243834567', 'giaoduc@nxb.vn'),
(3, 'NXB Trẻ', 'TP.HCM', '02838345678', 'tre@nxb.vn'),
(4, 'NXB Văn Học', 'Hà Nội', '02438456789', 'vanhoc@nxb.vn'),
(5, 'NXB Tổng Hợp', 'TP.HCM', '0283845678', 'tonghop@nxb.vn'),
(6, 'NXB Hội Nhà Văn', 'Hà Nội', '0243856789', 'hoinhavan@nxb.vn'),
(7, 'Alpha Books', 'Hà Nội', NULL, 'contact@alphabooks.vn'),
(8, 'NXB Thế Giới', 'Hà Nội', NULL, 'thegioi@nxb.vn'),
(9, 'First News', 'TP.HCM', '0283915678', 'info@firstnews.com.vn'),
(10, 'NXB Phụ Nữ', 'TP.HCM', '02839105678', 'phunu@nxb.vn');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

DROP TABLE IF EXISTS `sanpham`;
CREATE TABLE `sanpham` (
  `MaSP` int(11) NOT NULL,
  `TenSP` varchar(200) NOT NULL,
  `DonGia` decimal(10,2) NOT NULL,
  `SoLuong` int(11) DEFAULT 0,
  `MoTa` text DEFAULT NULL,
  `HinhAnh` varchar(255) DEFAULT NULL,
  `MaDM` int(11) DEFAULT NULL,
  `MaNXB` int(11) DEFAULT NULL,
  `NgayCapNhat` datetime DEFAULT current_timestamp(),
  `SoLuongDaBan` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`MaSP`, `TenSP`, `DonGia`, `SoLuong`, `MoTa`, `HinhAnh`, `MaDM`, `MaNXB`, `NgayCapNhat`, `SoLuongDaBan`) VALUES
(1, 'Lập trình Java cơ bản', 250000.00, 70, 'Sách học Java từ cơ bản đến nâng cao', '1765965143_0.png', 1, 1, '2025-12-05 11:56:58', 0),
(2, 'Thiết kế web với HTML/CSS', 180000.00, 115, 'Hướng dẫn thiết kế web hiện đại', '1765965188_20240116_57jvPK0Fr0.jpeg', 1, 2, '2025-12-05 11:56:58', 0),
(3, 'PHP & MySQL từ zero', 320000.00, 0, 'Sách PHP thực chiến', '1765965203_20240725_ceQoz6ZtfV.jpeg', 1, 1, '2025-12-05 11:56:58', 0),
(4, 'Sách toán lớp 10', 85000.00, 89, 'Sách giáo khoa lớp 10', '1765965216_0.png', 2, 2, '2025-12-05 11:56:58', 1),
(5, 'Văn học Việt Nam', 150000.00, 15, 'Tuyển tập văn học Việt Nam', '1765965242_0.png', 3, 4, '2025-12-05 11:56:58', 0),
(6, 'Truyện tranh Doraemon', 25000.00, 170, 'Bộ truyện tranh nổi tiếng', '1765965255_0.png', 4, 1, '2025-12-05 11:56:58', 0),
(7, 'Sách tiếng Anh giao tiếp', 195000.00, 30, 'Học tiếng Anh thực tế', '1765965275_0.png', 5, 7, '2025-12-05 11:56:58', 0),
(8, 'Sách nấu ăn Việt Nam', 135000.00, 50, '100 món ăn Việt Nam', '1765965362_0.png', 6, 10, '2025-12-05 11:56:58', 0),
(9, 'Sách lịch sử Việt Nam', 280000.00, 0, 'Lịch sử từ thời Hùng Vương', '1765965378_20240725_IxF54EpLpq.jpeg', 7, 5, '2025-12-05 11:56:58', 0),
(10, 'Harry Potter tập 1', 185000.00, 75, 'Bộ sách nổi tiếng thế giới', '1765965406_0.png', 3, 3, '2025-12-05 11:56:58', 0),
(11, 'Harry Potter tập 2', 195000.00, 70, 'Phần tiếp theo', '1765965417_0.png', 3, 3, '2025-12-05 11:56:58', 0),
(12, '7 thói quen hiệu quả', 125000.00, 55, 'Sách kỹ năng sống kinh điển', '1765965429_0.png', 9, 7, '2025-12-05 11:56:58', 0),
(13, 'Người giàu nhất thành Babylon', 350000.00, 10, 'Sách kinh doanh kinh điển', '1765965441_20240725_IxF54EpLpq.jpeg', 8, 8, '2025-12-05 11:56:58', 0),
(14, 'Truyện cổ tích Andersen', 45000.00, 135, 'Dành cho trẻ em', '1765965454_20240725_kirFD6lp2L.jpeg', 11, 1, '2025-12-05 11:56:58', 0),
(15, 'Vũ trụ trong lòng bàn tay', 220000.00, 24, 'Khoa học phổ thông', '1765965466_20240116_57jvPK0Fr0.jpeg', 10, 6, '2025-12-05 11:56:58', 1),
(16, 'One Piece tập 100', 30000.00, 165, 'Truyện tranh Nhật Bản', '1765965478_20240725_AEL14wwuzA.jpeg', 4, 1, '2025-12-05 11:56:58', 0),
(17, 'Học làm bánh', 160000.00, 40, 'Công thức làm bánh ngọt', '1765965490_0.png', 6, 10, '2025-12-05 11:56:58', 0),
(18, 'Rich Dad Poor Dad', 198000.00, 50, 'Tư duy tài chính', '1765965504_20240725_ceQoz6ZtfV.jpeg', 8, 7, '2025-12-05 11:56:58', 0),
(19, 'Đắc nhân tâm', 118000.00, 80, 'Kỹ năng giao tiếp', '1765965510_20240725_IxF54EpLpq.jpeg', 9, 4, '2025-12-05 11:56:58', 0),
(20, 'Nhà giả kim', 98000.00, 95, 'Tiểu thuyết nổi tiếng', '1765965516_20240116_57jvPK0Fr0.jpeg', 3, 4, '2025-12-05 11:56:58', 0),
(22, 'boy dep zai', 99999999.99, 11, 'dep zai', '22_1765964718_0.JPG', 11, 1, '2025-12-17 16:45:18', 0),
(23, 'boy dep zai', 99999999.99, 0, 'dep zai', '23_1765964737_0.JPG', 11, 1, '2025-12-17 16:45:37', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham_hinhanh`
--

DROP TABLE IF EXISTS `sanpham_hinhanh`;
CREATE TABLE `sanpham_hinhanh` (
  `MaHA` int(11) NOT NULL,
  `MaSP` int(11) NOT NULL,
  `DuongDan` varchar(255) NOT NULL,
  `LaAnhChinh` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham_tacgia`
--

DROP TABLE IF EXISTS `sanpham_tacgia`;
CREATE TABLE `sanpham_tacgia` (
  `MaSP` int(11) NOT NULL,
  `MaTacGia` int(11) NOT NULL,
  `VaiTro` varchar(100) DEFAULT 'TacGia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham_tacgia`
--

INSERT INTO `sanpham_tacgia` (`MaSP`, `MaTacGia`, `VaiTro`) VALUES
(19, 1, 'Tác giả');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tacgia`
--

DROP TABLE IF EXISTS `tacgia`;
CREATE TABLE `tacgia` (
  `MaTacGia` int(11) NOT NULL,
  `TenTacGia` varchar(255) NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `QuocTich` varchar(100) DEFAULT NULL,
  `MoTa` text DEFAULT NULL,
  `AnhDaiDien` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tacgia`
--

INSERT INTO `tacgia` (`MaTacGia`, `TenTacGia`, `NgaySinh`, `QuocTich`, `MoTa`, `AnhDaiDien`) VALUES
(1, 'Nguyễn Nhật Ánh', '1955-05-07', 'Việt Nam', 'Nhà văn thiếu nhi', NULL),
(2, 'Robin Sharma', '1965-06-16', 'Canada', 'Tác giả phát triển bản thân', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

DROP TABLE IF EXISTS `taikhoan`;
CREATE TABLE `taikhoan` (
  `MaTK` int(11) NOT NULL,
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `VaiTro` enum('QuanLy','NhanVien','KhachHang') DEFAULT 'KhachHang',
  `TrangThai` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`MaTK`, `TenDangNhap`, `MatKhau`, `VaiTro`, `TrangThai`) VALUES
(1, 'admin', '123456', 'QuanLy', 1),
(2, 'nhanvien1', '123456', 'NhanVien', 1),
(3, 'khach1', '123456', 'KhachHang', 1),
(5, 'anquee', '20223457', 'QuanLy', 1),
(6, 'hoangduyan', '$2y$10$0ekNq8naRUa3ooLpMzRFmu2UYHeKGjDcUo3f9V2RdUktmOEd6Wxti', 'KhachHang', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thongbao`
--

DROP TABLE IF EXISTS `thongbao`;
CREATE TABLE `thongbao` (
  `MaTB` int(11) NOT NULL,
  `MaKH` int(11) DEFAULT NULL,
  `TieuDe` varchar(200) DEFAULT NULL,
  `NoiDung` text DEFAULT NULL,
  `NgayGui` datetime DEFAULT current_timestamp(),
  `TrangThaiDoc` tinyint(4) DEFAULT 0,
  `LoaiTB` enum('KhuyenMai','DonHang','SanPhamMoi','HeThong') DEFAULT 'HeThong',
  `LienKet` varchar(255) DEFAULT NULL,
  `DaDoc` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`MaDH`,`MaSP`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `chitietgiohang`
--
ALTER TABLE `chitietgiohang`
  ADD PRIMARY KEY (`MaGH`,`MaSP`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `chitietnhaphang`
--
ALTER TABLE `chitietnhaphang`
  ADD PRIMARY KEY (`MaNhap`,`MaSP`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`MaDM`);

--
-- Chỉ mục cho bảng `doanhthu`
--
ALTER TABLE `doanhthu`
  ADD PRIMARY KEY (`MaBC`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`MaDH`),
  ADD KEY `MaKH` (`MaKH`),
  ADD KEY `MaKM` (`MaKM`);

--
-- Chỉ mục cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`MaGH`),
  ADD KEY `MaKH` (`MaKH`);

--
-- Chỉ mục cho bảng `hinhanhsanpham`
--
ALTER TABLE `hinhanhsanpham`
  ADD PRIMARY KEY (`MaHinh`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKH`),
  ADD KEY `MaTK` (`MaTK`);

--
-- Chỉ mục cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  ADD PRIMARY KEY (`MaKM`),
  ADD KEY `MaDM` (`MaDM`);

--
-- Chỉ mục cho bảng `lichsunhaphang`
--
ALTER TABLE `lichsunhaphang`
  ADD PRIMARY KEY (`MaNhap`),
  ADD KEY `MaNCC` (`MaNCC`);

--
-- Chỉ mục cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD PRIMARY KEY (`MaNCC`);

--
-- Chỉ mục cho bảng `nhaxuatban`
--
ALTER TABLE `nhaxuatban`
  ADD PRIMARY KEY (`MaNXB`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`MaSP`),
  ADD KEY `MaDM` (`MaDM`),
  ADD KEY `MaNXB` (`MaNXB`);

--
-- Chỉ mục cho bảng `sanpham_hinhanh`
--
ALTER TABLE `sanpham_hinhanh`
  ADD PRIMARY KEY (`MaHA`),
  ADD KEY `MaSP` (`MaSP`);

--
-- Chỉ mục cho bảng `sanpham_tacgia`
--
ALTER TABLE `sanpham_tacgia`
  ADD PRIMARY KEY (`MaSP`,`MaTacGia`),
  ADD KEY `MaTacGia` (`MaTacGia`);

--
-- Chỉ mục cho bảng `tacgia`
--
ALTER TABLE `tacgia`
  ADD PRIMARY KEY (`MaTacGia`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`MaTK`),
  ADD UNIQUE KEY `TenDangNhap` (`TenDangNhap`);

--
-- Chỉ mục cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  ADD PRIMARY KEY (`MaTB`),
  ADD KEY `MaKH` (`MaKH`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `MaDM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `doanhthu`
--
ALTER TABLE `doanhthu`
  MODIFY `MaBC` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `donhang`
--
ALTER TABLE `donhang`
  MODIFY `MaDH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `giohang`
--
ALTER TABLE `giohang`
  MODIFY `MaGH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `hinhanhsanpham`
--
ALTER TABLE `hinhanhsanpham`
  MODIFY `MaHinh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `MaKH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  MODIFY `MaKM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `lichsunhaphang`
--
ALTER TABLE `lichsunhaphang`
  MODIFY `MaNhap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  MODIFY `MaNCC` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `nhaxuatban`
--
ALTER TABLE `nhaxuatban`
  MODIFY `MaNXB` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `MaSP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `sanpham_hinhanh`
--
ALTER TABLE `sanpham_hinhanh`
  MODIFY `MaHA` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tacgia`
--
ALTER TABLE `tacgia`
  MODIFY `MaTacGia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  MODIFY `MaTK` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  MODIFY `MaTB` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`MaDH`) REFERENCES `donhang` (`MaDH`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `chitietgiohang`
--
ALTER TABLE `chitietgiohang`
  ADD CONSTRAINT `chitietgiohang_ibfk_1` FOREIGN KEY (`MaGH`) REFERENCES `giohang` (`MaGH`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietgiohang_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `chitietnhaphang`
--
ALTER TABLE `chitietnhaphang`
  ADD CONSTRAINT `chitietnhaphang_ibfk_1` FOREIGN KEY (`MaNhap`) REFERENCES `lichsunhaphang` (`MaNhap`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietnhaphang_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE SET NULL,
  ADD CONSTRAINT `donhang_ibfk_2` FOREIGN KEY (`MaKM`) REFERENCES `khuyenmai` (`MaKM`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hinhanhsanpham`
--
ALTER TABLE `hinhanhsanpham`
  ADD CONSTRAINT `hinhanhsanpham_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD CONSTRAINT `khachhang_ibfk_1` FOREIGN KEY (`MaTK`) REFERENCES `taikhoan` (`MaTK`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  ADD CONSTRAINT `khuyenmai_ibfk_1` FOREIGN KEY (`MaDM`) REFERENCES `danhmuc` (`MaDM`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `lichsunhaphang`
--
ALTER TABLE `lichsunhaphang`
  ADD CONSTRAINT `lichsunhaphang_ibfk_1` FOREIGN KEY (`MaNCC`) REFERENCES `nhacungcap` (`MaNCC`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`MaDM`) REFERENCES `danhmuc` (`MaDM`) ON DELETE SET NULL,
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`MaNXB`) REFERENCES `nhaxuatban` (`MaNXB`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `sanpham_hinhanh`
--
ALTER TABLE `sanpham_hinhanh`
  ADD CONSTRAINT `sanpham_hinhanh_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `sanpham_tacgia`
--
ALTER TABLE `sanpham_tacgia`
  ADD CONSTRAINT `sanpham_tacgia_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE,
  ADD CONSTRAINT `sanpham_tacgia_ibfk_2` FOREIGN KEY (`MaTacGia`) REFERENCES `tacgia` (`MaTacGia`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  ADD CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
