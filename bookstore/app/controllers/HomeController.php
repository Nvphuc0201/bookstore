<?php
require_once __DIR__ . '/../models/SanPham.php';
require_once __DIR__ . '/../models/DanhMuc.php';

class HomeController {
    private $spModel;
    private $dmModel;

    public function __construct() {
        $this->spModel = new SanPham();
        $this->dmModel = new DanhMuc();
    }

    // Trang chủ: nhận search (GET 'search') và page (GET 'p')
    public function index() {
        // toàn bộ sản phẩm (mảng)
        $products = $this->spModel->getAll();
        $categories = $this->dmModel->getAll();
        $bestSellers = $this->spModel->getBestSeller(8); // top 8

        // truyền vào view
        require_once __DIR__ . '/../views/home/index.php';
    }

    // Tùy chọn: danh mục riêng (nếu cần)
    public function category() {
        $madm = intval($_GET['id'] ?? 0);
        if ($madm <= 0) {
            header("Location: index.php");
            exit;
        }
        $products = $this->spModel->getByCategory($madm);
        $categories = $this->dmModel->getAll();
        $bestSellers = $this->spModel->getBestSeller(8);
        require_once __DIR__ . '/../views/home/index.php';
    }
}
