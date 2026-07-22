</main> <footer class="bg-dark text-light pt-5 pb-3" style="background-color: #2c3e50 !important;">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="text-uppercase fw-bold mb-3" style="color: #e67e22;">BookStore Premium</h5>
                <p class="small text-white-50">
                    Nền tảng thương mại điện tử chuyên cung cấp sách chính hãng, uy tín hàng đầu. 
                    Chúng tôi cam kết mang lại tri thức và trải nghiệm mua sắm tuyệt vời nhất.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-white fs-5 hover-orange"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="text-white fs-5 hover-orange"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-white fs-5 hover-orange"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="text-white fs-5 hover-orange"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <h6 class="text-uppercase fw-bold mb-3">Hỗ trợ khách hàng</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-orange">Hướng dẫn mua hàng</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-orange">Chính sách đổi trả</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-orange">Phương thức vận chuyển</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-orange">Câu hỏi thường gặp (FAQ)</a></li>
                </ul>
            </div>

            <div class="col-md-2 mb-4">
                <h6 class="text-uppercase fw-bold mb-3">Tài khoản</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="index.php?controller=auth&action=login" class="text-white-50 text-decoration-none hover-orange">Đăng nhập</a></li>
                    <li class="mb-2"><a href="index.php?controller=auth&action=register" class="text-white-50 text-decoration-none hover-orange">Đăng ký</a></li>
                    <li class="mb-2"><a href="index.php?controller=cart" class="text-white-50 text-decoration-none hover-orange">Giỏ hàng</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-orange">Lịch sử đơn hàng</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h6 class="text-uppercase fw-bold mb-3">Liên hệ</h6>
                <ul class="list-unstyled small text-white-50">
                    <li class="mb-2"><i class="fa-solid fa-location-dot me-2 text-warning"></i> 123 Đường Sách, Q.1, TP.HCM</li>
                    <li class="mb-2"><i class="fa-solid fa-phone me-2 text-warning"></i> 1900 123 456</li>
                    <li class="mb-2"><i class="fa-solid fa-envelope me-2 text-warning"></i> hotro@bookstore.vn</li>
                    <li class="mb-2"><i class="fa-solid fa-clock me-2 text-warning"></i> 8:00 - 22:00 (Hàng ngày)</li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="small text-white-50 mb-0">&copy; <?= date('Y') ?> <strong>BookStore Premium</strong>. All Rights Reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <i class="fa-brands fa-cc-visa text-white-50 fs-4 me-2"></i>
                <i class="fa-brands fa-cc-mastercard text-white-50 fs-4 me-2"></i>
                <i class="fa-solid fa-money-bill-wave text-white-50 fs-4"></i>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-orange:hover {
        color: #e67e22 !important;
        padding-left: 5px; /* Hiệu ứng đẩy nhẹ sang phải */
        transition: all 0.3s ease;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>