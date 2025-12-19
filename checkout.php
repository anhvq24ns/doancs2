<?php
include 'components/connection.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// Xử lý đăng xuất
if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
    exit;
}

// ÁP DỤNG MÃ GIẢM GIÁ
if (isset($_POST['apply_coupon_checkout'])) {
    $coupon_code = trim($_POST['coupon_code'] ?? '');

    if (empty($coupon_code)) {
        $warning_msg[] = 'Vui lòng chọn mã giảm giá!';
    } else {
        $today = date('Y-m-d H:i:s');
        $check = $conn->prepare("
            SELECT * FROM coupons 
            WHERE code = ? 
              AND status = 'active' 
              AND (expiry_date IS NULL OR expiry_date >= ?)
              AND (usage_limit IS NULL OR used_count < usage_limit)
        ");
        $check->execute([$coupon_code, $today]);

        if ($check->rowCount() > 0) {
            $coupon = $check->fetch(PDO::FETCH_ASSOC);

            $_SESSION['coupon'] = [
                'id'             => $coupon['id'],
                'code'           => $coupon['code'],
                'discount_type'  => $coupon['discount_type'] == 'percentage' ? 'percent' : 'fixed',
                'discount_value' => (float)$coupon['discount_value'],
                'min_order'      => (float)$coupon['min_order'],
                'max_discount'   => $coupon['max_discount'] ? (float)$coupon['max_discount'] : null
            ];
            $success_msg[] = 'Áp dụng mã giảm giá thành công!';
        } else {
            $warning_msg[] = 'Mã giảm giá không hợp lệ, đã hết hạn hoặc hết lượt!';
        }
    }
}

// XÓA MÃ GIẢM GIÁ
if (isset($_POST['remove_coupon_checkout'])) {
    unset($_SESSION['coupon']);
    $success_msg[] = 'Đã xóa mã giảm giá!';
}

// TÍNH TỔNG TIỀN + LẤY SẢN PHẨM
$grand_total = 0;
$discount    = 0;
$final_total = 0;
$cart_items  = [];

if (isset($_GET['get_id'])) {
    // Checkout 1 sản phẩm trực tiếp
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$_GET['get_id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $grand_total = $product['price'];
        $cart_items = [['name' => $product['name'], 'image' => $product['image'], 'price' => $product['price'], 'qty' => 1]];
    }
} else {
    // Checkout từ giỏ hàng
    if ($user_id !== '') {
        $stmt = $conn->prepare("
            SELECT c.qty, p.name, p.price, p.image
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ? AND p.status = 'active'
        ");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cart_items as $item) {
            $grand_total += $item['price'] * $item['qty'];
        }
    }
}

// TÍNH GIẢM GIÁ
if (isset($_SESSION['coupon']) && $grand_total > 0) {
    $c = $_SESSION['coupon'];

    if ($grand_total < $c['min_order']) {
        $warning_msg[] = 'Đơn hàng chưa đủ $' . number_format($c['min_order']) . ' để dùng mã này!';
        unset($_SESSION['coupon']);
    } else {
        if ($c['discount_type'] == 'percent') {
            $discount = $grand_total * $c['discount_value'] / 100;
            if ($c['max_discount'] !== null && $discount > $c['max_discount']) {
                $discount = $c['max_discount'];
            }
        } else {
            $discount = $c['discount_value'];
        }
        if ($discount > $grand_total) $discount = $grand_total;
    }
}
$final_total = $grand_total - $discount;

// LẤY DANH SÁCH MÃ GIẢM GIÁ HỢP LỆ
$today = date('Y-m-d H:i:s');
$valid_coupons = [];
$list = $conn->prepare("
    SELECT * FROM coupons 
    WHERE status = 'active' 
      AND (expiry_date IS NULL OR expiry_date >= ?)
      AND (usage_limit IS NULL OR used_count < usage_limit)
    ORDER BY discount_value DESC
");
$list->execute([$today]);
$valid_coupons = $list->fetchAll(PDO::FETCH_ASSOC);

// XỬ LÝ ĐẶT HÀNG – ĐÃ SỬA SẠCH 100%
if (isset($_POST['place_order'])) {
    if ($user_id === '') {
        $warning_msg[] = 'Vui lòng đăng nhập để đặt hàng!';
    } else {
        // Validate form
        $name    = trim($_POST['name'] ?? '');
        $number  = trim($_POST['number'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $method  = trim($_POST['method'] ?? '');
        $address_type = trim($_POST['address_type'] ?? '');
        $flat    = trim($_POST['flat'] ?? '');
        $street  = trim($_POST['street'] ?? '');
        $city    = trim($_POST['city'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $pincode = trim($_POST['pincode'] ?? '');

        if (empty($name) || empty($number) || empty($email) || empty($method) || empty($address_type) ||
            empty($flat) || empty($street) || empty($city) || empty($country) || empty($pincode)) {
            $warning_msg[] = 'Vui lòng điền đầy đủ thông tin!';
        } else {
            $address     = "$flat, $street, $city, $country - $pincode";
            $coupon_code = $_SESSION['coupon']['code'] ?? null;

            if (isset($_GET['get_id'])) {
                // === MUA 1 SẢN PHẨM TRỰC TIẾP ===
                $pid = (int)$_GET['get_id'];
                $select = $conn->prepare("SELECT price FROM products WHERE id = ? AND status = 'active'");
                $select->execute([$pid]);
                $product = $select->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    $warning_msg[] = 'Sản phẩm không tồn tại hoặc đã bị xóa!';
                } else {
                    $insert = $conn->prepare("INSERT INTO orders 
                        (user_id, name, number, email, method, address_type, address, product_id, price, qty, coupon_code, discount_amount, final_price, status, payment_status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, 'pending', 'unpaid')");

                    $insert->execute([
                        $user_id, $name, $number, $email, $method, $address_type, $address,
                        $pid, $product['price'], $coupon_code, $discount, $final_total
                    ]);

                    // Tăng lượt dùng coupon nếu có
                    if ($coupon_code) {
                        $conn->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE code = ?")
                             ->execute([$coupon_code]);
                    }
                    unset($_SESSION['coupon']);
                    $success_msg[] = 'Đặt hàng thành công! Cảm ơn bạn đã mua sắm';
                }
            } else {
                // === ĐẶT HÀNG TỪ GIỎ HÀNG ===
                foreach ($cart_items as $item) {
                    $subtotal     = $item['price'] * $item['qty'];
                    $item_discount = $grand_total > 0 ? ($subtotal / $grand_total) * $discount : 0;
                    $item_final    = $subtotal - $item_discount;

                    $conn->prepare("INSERT INTO orders 
                        (user_id,name,number,email,method,address_type,address,product_id,price,qty,coupon_code,discount_amount,final_price,status,payment_status) 
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,'pending','unpaid')")
                         ->execute([
                             $user_id,$name,$number,$email,$method,$address_type,$address,
                             $item['product_id'],$item['price'],$item['qty'],
                             $coupon_code,$item_discount,$item_final
                         ]);
                }

                // Xóa giỏ hàng
                $conn->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

                // Tăng lượt dùng coupon
                if ($coupon_code) {
                    $conn->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE code = ?")
                         ->execute([$coupon_code]);
                }
                unset($_SESSION['coupon']);
                $success_msg[] = 'Đặt hàng thành công! Cảm ơn bạn đã mua sắm';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecorMimic - Thanh Toán</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        <?php include 'style.css'; ?>
        .summary, form { background:#fff; padding:2rem; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); margin-bottom:2rem; }
        .coupon-section-checkout, .total-breakdown { background:#f8f9fa; padding:1.5rem; border-radius:10px; margin:1rem 0; }
        .coupon-form-checkout { display:flex; gap:10px; flex-wrap:wrap; }
        .coupon-select-checkout { flex:1; padding:12px; border:1px solid #ddd; border-radius:8px; }
        .applied-coupon-checkout { background:#d4edda; padding:15px; border-radius:8px; display:flex; justify-content:space-between; align-items:center; }
        .final-total { font-size:1.5rem; font-weight:bold; color:#28a745; }
        .empty { text-align:center; padding:3rem; color:#888; }
        @media (max-width:768px) { .coupon-form-checkout { flex-direction:column; } .row { flex-direction:column; } }
    </style>
</head>
<body>
<?php include 'components/header.php'; ?>

<div class="main">
    <div class="banner"><h1>Thanh Toán Đơn Hàng</h1></div>
    <div class="title2"><a href="home.php">Trang chủ</a><span> / Thanh toán</span></div>

    <section class="checkout">
        <div class="row">
            <!-- TÓM TẮT ĐƠN HÀNG -->
            <div class="summary">
                <h3>Sản phẩm</h3>
                <?php if (empty($cart_items)): ?>
                    <p class="empty">Không có sản phẩm nào để thanh toán!</p>
                <?php else: foreach($cart_items as $item): ?>
                    <div style="display:flex; align-items:center; gap:15px; margin:15px 0; padding-bottom:10px; border-bottom:1px solid #eee;">
                        <img src="img/<?=htmlspecialchars($item['image'])?>" style="width:80px; height:80px; object-fit:cover; border-radius:8px;">
                        <div>
                            <h4 style="margin:0; color:#333;"><?=htmlspecialchars($item['name'])?></h4>
                            <p style="margin:5px 0; color:#666;">$<?=number_format($item['price'])?> × <?=$item['qty'] ?? 1?></p>
                        </div>
                    </div>
                <?php endforeach; endif; ?>

                <!-- MÃ GIẢM GIÁ -->
                <div class="coupon-section-checkout">
                    <?php if (!isset($_SESSION['coupon'])): ?>
                        <form method="post" class="coupon-form-checkout">
                            <select name="coupon_code" class="coupon-select-checkout" required>
                                <option value="">-- Chọn mã giảm giá --</option>
                                <?php foreach($valid_coupons as $cp):
                                    $txt = $cp['discount_type']=='percentage' ? $cp['discount_value'].'%' : '$'.number_format($cp['discount_value']);
                                    $min = $cp['min_order']>0 ? ' (Tối thiểu $'.number_format($cp['min_order']).')' : '';
                                ?>
                                    <option value="<?=htmlspecialchars($cp['code'])?>"><?=$cp['code']?> - Giảm <?=$txt.$min?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="apply_coupon_checkout" class="btn">Áp dụng</button>
                        </form>
                    <?php else: ?>
                        <div class="applied-coupon-checkout">
                            <p>Mã <strong><?=$_SESSION['coupon']['code']?></strong> đã áp dụng</p>
                            <form method="post"><button type="submit" name="remove_coupon_checkout" class="btn" style="background:#dc3545;">Xóa mã</button></form>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TỔNG KẾT -->
                <div class="total-breakdown">
                    <p>Tổng tiền: <span>$<?=number_format($grand_total,2)?></span></p>
                    <?php if($discount>0): ?>
                        <p style="color:#dc3545;">Giảm giá: <span>-$<?=number_format($discount,2)?></span></p>
                    <?php endif; ?>
                    <p class="final-total">Thành tiền: <span>$<?=number_format($final_total,2)?></span></p>
                </div>
            </div>

            <!-- FORM THANH TOÁN -->
            <form method="post">
                <h3>Thông tin giao hàng</h3>
                <div class="flex" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div class="box">
                        <input type="text" name="name" required placeholder="Họ và tên" class="input">
                        <input type="text" name="number" required placeholder="Số điện thoại" class="input">
                        <input type="email" name="email" required placeholder="Email" class="input">
                        <select name="method" required class="input">
                            <option value="">Phương thức thanh toán</option>
                            <option value="cash on delivery">Thanh toán khi nhận hàng</option>
                            <option value="credit or debit card">Thẻ tín dụng/ghi nợ</option>
                            <option value="bank transfer">Chuyển khoản</option>
                        </select>
                        <select name="address_type" required class="input">
                            <option value="">Loại địa chỉ</option>
                            <option value="home">Nhà riêng</option>
                            <option value="office">Văn phòng</option>
                        </select>
                    </div>
                    <div class="box">
                        <input type="text" name="flat" required placeholder="Số nhà, tòa nhà" class="input">
                        <input type="text" name="street" required placeholder="Tên đường" class="input">
                        <input type="text" name="city" required placeholder="Thành phố" class="input">
                        <input type="text" name="country" required placeholder="Quốc gia" class="input">
                        <input type="number" name="pincode" required placeholder="Mã bưu điện" class="input">
                    </div>
                </div>
                <button type="submit" name="place_order" class="btn" style="width:100%; padding:15px; font-size:1.2rem; margin-top:1rem;">
                    Đặt hàng ngay – $<?=number_format($final_total,2)?>
                </button>
            </form>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="script.js"></script>
<?php include 'components/alert.php'; ?>
</body>
</html>