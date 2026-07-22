<?php
require_once __DIR__ . '/../models/SanPham.php';
require_once __DIR__ . '/../models/DanhMuc.php'; // Cần thêm cái này để hiện Sidebar
require_once __DIR__ . '/../config/db.php';

class SanPhamController {
    private $spModel;
    private $dmModel;

    public function __construct() {
        $this->spModel = new SanPham();
        $this->dmModel = new DanhMuc();
    }

    // ====================================================
    // 1. ACTION: TRANG CỬA HÀNG (HIỂN THỊ TẤT CẢ HOẶC LỌC THEO DANH MỤC)
    // URL: index.php?controller=sanpham&action=list
    // URL: index.php?controller=sanpham&action=list&id=1
    // ====================================================
    public function list() {
        // Kiểm tra xem có tham số 'id' (danh mục) không
        $categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($categoryId > 0) {
            // Lọc theo danh mục
            $products = $this->spModel->getByCategory($categoryId);
            $categoryData = $this->dmModel->getById($categoryId);
            $pageTitle = $categoryData ? "Danh mục: " . $categoryData['TenDM'] : "Sách theo danh mục";
        } else {
            // Lấy tất cả sản phẩm
            $products = $this->spModel->getAll();
            $pageTitle = "Tất cả sách"; // Tiêu đề trang
        }

        $categories = $this->dmModel->getAll(); // Để hiển thị sidebar

        // Gọi View
        require_once __DIR__ . '/../views/sanpham/list.php';
    }

    // ====================================================
    // 2. ACTION: TÌM KIẾM SẢN PHẨM
    // URL: index.php?controller=sanpham&action=search&keyword=abc
    // ====================================================
    public function search() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        
        if (!empty($keyword)) {
            // Gọi hàm search trong Model (đã viết ở bước trước)
            $products = $this->spModel->search($keyword);
            $pageTitle = "Kết quả tìm kiếm: '" . htmlspecialchars($keyword) . "'";
        } else {
            $products = [];
            $pageTitle = "Vui lòng nhập từ khóa";
        }

        // Vẫn cần danh mục cho sidebar
        $categories = $this->dmModel->getAll();

        // Dùng chung view với trang list
        require_once __DIR__ . '/../views/sanpham/list.php';
    }

    // ====================================================
    // 3. ACTION: CHI TIẾT SẢN PHẨM
    // URL: index.php?controller=sanpham&action=detail&id=1
    // ====================================================
    public function detail() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id > 0) {
            // 1. Lấy thông tin sản phẩm chính
            $product = $this->spModel->getById($id);
            
            // Nếu không tìm thấy sản phẩm (ID sai), quay về trang chủ
            if (!$product) {
                header("Location: index.php");
                exit;
            }

            // 2. Lấy danh sách ảnh phụ (nếu có)
            $imagesResult = $this->spModel->getImages($id);
            $images = [];
            if ($imagesResult) {
                while ($r = $imagesResult->fetch_assoc()) $images[] = $r;
            }

            // 3. Lấy sách liên quan (Cùng danh mục, trừ cuốn hiện tại)
            $relatedProducts = $this->spModel->getByCategory($product['MaDM']);
            // Lọc bỏ cuốn hiện tại ra khỏi danh sách liên quan
            $relatedProducts = array_filter($relatedProducts, function($p) use ($id) {
                return $p['MaSP'] != $id;
            });
            // Lấy 4 cuốn thôi
            $relatedProducts = array_slice($relatedProducts, 0, 4);

            // 4. Lấy danh mục để hiện Sidebar (nếu cần)
            $categories = $this->dmModel->getAll();

            require_once __DIR__ . '/../views/sanpham/detail.php';
        } else {
            header("Location: index.php");
            exit;
        }
    }

    // ====================================================
    // CÁC HÀM CŨ CỦA BẠN (DÀNH CHO ADMIN HOẶC XỬ LÝ DỮ LIỆU)
    // ====================================================
    
    // Mặc định nếu không gọi action gì thì hiện danh sách
    public function index() {
        $this->list(); 
    }

    public function store($data) {
        $ten   = $data['ten'];
        $gia   = $data['gia'];
        $mota  = $data['mota'];
        $madm  = $data['madm'];
        $manxb = $data['manxb'];
        $soluong = 0;
        $anh = ""; // Nên xử lý upload file ở đây nếu cần

        return $this->spModel->insert(
            $ten, $gia, $soluong, $mota, $anh, $madm, $manxb
        );
    }
}
?>