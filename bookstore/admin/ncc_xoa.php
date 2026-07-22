<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/NhaCungCap.php";

$model = new NhaCungCap();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Bạn có thể thêm kiểm tra FK: nếu NhaCungCap đang được tham chiếu thì không xóa, hoặc xóa mềm
    // Ví dụ kiểm tra tồn tại LichSuNhapHang có MaNCC này:
    require_once "../app/config/db.php";
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM LichSuNhapHang WHERE MaNCC = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    if ($r['cnt'] > 0) {
        // Đang có phiếu nhập liên quan -> không xóa, chuyển hướng và báo
        header("Location: ncc.php?error=linked");
        exit;
    }

    $model->delete($id);
}

header("Location: ncc.php");
exit;
