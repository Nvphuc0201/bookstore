<?php
require_once "../app/middleware/admin.php";
require_once "../app/models/NhapHang.php";
require_once "../app/models/NhaCungCap.php";
require_once "../app/models/SanPham.php";

$nh = new NhapHang();
$nccModel = new NhaCungCap();
$spModel = new SanPham();

$nccList = $nccModel->getAll();
$spList = $spModel->getAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maNCC = (int)($_POST['maNCC'] ?? 0);
    $ngay = trim($_POST['ngayNhap'] ?? '');
    // arrays
    $maSPs = $_POST['maSP'] ?? [];
    $soluongs = $_POST['soLuong'] ?? [];
    $dongias = $_POST['donGia'] ?? [];

    $items = [];
    for ($i = 0; $i < count($maSPs); $i++) {
        $m = (int)$maSPs[$i];
        $q = (int)$soluongs[$i];
        $p = (float)$dongias[$i];
        if ($m > 0 && $q > 0) {
            $items[] = ['maSP'=>$m, 'soLuong'=>$q, 'donGia'=>$p];
        }
    }

    if ($maNCC <= 0) $errors[] = "Chọn nhà cung cấp.";
    if (empty($items)) $errors[] = "Bạn phải thêm ít nhất 1 sản phẩm có số lượng > 0.";

    if (empty($errors)) {
        $maNhap = $nh->create($maNCC, $ngay, $items);
        if ($maNhap) {
            header("Location: nhaphang_chitiet.php?id=" . $maNhap);
            exit;
        } else {
            $errors[] = "Tạo phiếu nhập thất bại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo phiếu nhập</title>
    <style>
        table td { vertical-align: top; }
        .small { width: 100px; }
    </style>
</head>
<body>
<h2>TẠO PHIẾU NHẬP</h2>

<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
    </div>
<?php endif; ?>

<form method="post" id="frm">
    Nhà cung cấp:<br>
    <select name="maNCC" required>
        <option value="0">-- Chọn NCC --</option>
        <?php while ($r = $nccList->fetch_assoc()): ?>
            <option value="<?= $r['MaNCC'] ?>" <?= (isset($_POST['maNCC']) && $_POST['maNCC']==$r['MaNCC'])?'selected':'' ?>>
                <?= htmlspecialchars($r['TenNCC']) ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    Ngày nhập (để trống = giờ hiện tại):<br>
    <input type="datetime-local" name="ngayNhap" value="<?= htmlspecialchars($_POST['ngayNhap'] ?? '') ?>"><br><br>

    <table id="items" border="1" cellpadding="6">
        <tr>
            <th>Sản phẩm</th>
            <th>Số lượng</th>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
            <th>Hành động</th>
        </tr>

        <!-- template row -->
        <tr class="item-row">
            <td>
                <select name="maSP[]">
                    <option value="0">-- Chọn SP --</option>
                    <?php foreach ($spList as $sp): ?>
                        <option value="<?= $sp['MaSP'] ?>"><?= htmlspecialchars($sp['TenSP']) ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="number" name="soLuong[]" value="1" min="1" class="small qty"></td>
            <td><input type="number" name="donGia[]" value="0" min="0" step="0.01" class="small price"></td>
            <td class="thanhTien">0</td>
            <td><button type="button" class="remove">Xóa</button></td>
        </tr>
    </table>

    <button type="button" id="add">➕ Thêm sản phẩm</button>
    <br><br>

    <div>
        Tổng tiền: <span id="total">0</span> ₫
    </div>

    <br>
    <button type="submit">Lưu phiếu nhập</button>
    <a href="nhaphang.php">Hủy</a>
</form>

<script>
    // copy initial row as template
    const template = document.querySelector('#items .item-row').cloneNode(true);

    function formatNumber(n) {
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function recalcRow(row) {
        const qty = parseInt(row.querySelector('.qty').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const tt = qty * price;
        row.querySelector('.thanhTien').textContent = formatNumber(tt.toFixed(0));
        recalcTotal();
    }

    function recalcTotal() {
        let total = 0;
        document.querySelectorAll('#items .item-row').forEach(r => {
            const qty = parseInt(r.querySelector('.qty').value) || 0;
            const price = parseFloat(r.querySelector('.price').value) || 0;
            total += qty * price;
        });
        document.getElementById('total').textContent = formatNumber(total.toFixed(0));
    }

    document.getElementById('add').addEventListener('click', () => {
        const newRow = template.cloneNode(true);
        // reset selects/inputs
        newRow.querySelector('select').value = "0";
        newRow.querySelector('.qty').value = 1;
        newRow.querySelector('.price').value = 0;
        newRow.querySelector('.thanhTien').textContent = "0";
        document.getElementById('items').appendChild(newRow);
    });

    document.getElementById('items').addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove')) {
            const row = e.target.closest('.item-row');
            row.parentNode.removeChild(row);
            recalcTotal();
        }
    });

    document.getElementById('items').addEventListener('input', function(e) {
        if (e.target && (e.target.classList.contains('qty') || e.target.classList.contains('price'))) {
            const row = e.target.closest('.item-row');
            recalcRow(row);
        }
    });

    // init calc for initial row (in case user changes)
    recalcRow(document.querySelector('#items .item-row'));
</script>
</body>
</html>
