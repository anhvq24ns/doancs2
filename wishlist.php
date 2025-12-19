<?php
include 'components/connection.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// 🔹 Kiểm tra đăng nhập: Nếu chưa đăng nhập, chuyển hướng đến login
if ($user_id == '') {
    header("location: login.php");
    exit;
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
    exit;
}

// 🧩 Thêm sản phẩm từ wishlist vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    $qty = 1; // Mặc định qty=1 khi chuyển từ wishlist

    $verify_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $verify_cart->execute([$user_id, $product_id]);

    if ($verify_cart->rowCount() > 0) {
        $warning_msg[] = 'Sản phẩm đã có trong giỏ hàng';
    } else {
        // 🔹 Sửa: Kiểm tra sản phẩm tồn tại trước khi lấy giá, và không insert manual id (AUTO_INCREMENT)
        $select_price = $conn->prepare("SELECT price FROM products WHERE id = ? AND status = 'active' LIMIT 1");
        $select_price->execute([$product_id]);
        if ($select_price->rowCount() > 0) {
            $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

            $insert_cart = $conn->prepare("INSERT INTO cart(user_id, product_id, price, qty) VALUES (?, ?, ?, ?)");
            $insert_cart->execute([$user_id, $product_id, $fetch_price['price'], $qty]);
            $success_msg[] = 'Đã thêm sản phẩm vào giỏ hàng thành công';
        } else {
            $warning_msg[] = 'Sản phẩm không tồn tại!';
        }
    }
}

// 🧩 Xóa item khỏi wishlist
if (isset($_POST['delete_item'])) {
    $wishlist_id = $_POST['wishlist_id'];
    $wishlist_id = filter_var($wishlist_id, FILTER_SANITIZE_STRING);

    $verify_delete_items = $conn->prepare("SELECT * FROM wishlist WHERE id = ?");
    $verify_delete_items->execute([$wishlist_id]);

    if ($verify_delete_items->rowCount() > 0) {
        $delete_wishlist_id = $conn->prepare("DELETE FROM wishlist WHERE id = ?");
        $delete_wishlist_id->execute([$wishlist_id]);
        $success_msg[] = 'Đã xóa sản phẩm khỏi danh sách yêu thích thành công';
    } else {
        $warning_msg[] = 'Sản phẩm đã được xóa khỏi danh sách yêu thích';
    }
}
?>
<style type="text/css">
    <?php include 'style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecorMimic - Danh sách yêu thích</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'components/header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>Danh sách yêu thích của tôi</h1>
        </div>
        <div class="title2">
            <a href="home.php">Trang chủ</a><span> / Danh sách yêu thích</span>
        </div>
        <section class="products">
            <h1 class="title">Sản phẩm đã thêm vào danh sách yêu thích</h1>
            <div class="box-container">
                <?php
                $grand_total = 0;
                $select_wishlist = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ?");
                $select_wishlist->execute([$user_id]);

                if ($select_wishlist->rowCount() > 0) {
                    while ($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)) {
                        $select_products = $conn->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
                        $select_products->execute([$fetch_wishlist['product_id']]);
                        if ($select_products->rowCount() > 0) {
                            $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                ?>
                            <form method="post" action="" class="box">
                                <input type="hidden" name="wishlist_id" value="<?= htmlspecialchars($fetch_wishlist['id']); ?>">
                                <img src="img/<?= htmlspecialchars($fetch_products['image']); ?>" alt="<?= htmlspecialchars($fetch_products['name']); ?>">
                                <div class="button">
                                    <button type="submit" name="add_to_cart"><i class="bx bx-cart"></i></button>
                                    <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="bx bxs-show"></a>
                                    <button type="submit" name="delete_item" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')"><i class="bx bx-x"></i></button>
                                </div>
                                <h3 class="name"><?= htmlspecialchars($fetch_products['name']); ?></h3>
                                <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
                                <div class="flex">
                                    <p class="price">Giá $<?= number_format($fetch_products['price']); ?>/-</p>
                                </div>
                                <a href="checkout.php?get_id=<?= $fetch_products['id']; ?>" class="btn">Mua ngay</a>
                            </form>
                <?php
                            $grand_total += $fetch_wishlist['price'];
                        } // 🔹 Không fallback vì chỉ hiển thị nếu product tồn tại
                    }
                    // 🔹 Hiển thị tổng tiền (đã tính nhưng chưa dùng)
                    if ($grand_total > 0) {
                        echo '<div class="grand-total">Tổng giá trị danh sách: <span>$' . number_format($grand_total) . '</span></div>';
                    }
                } else {
                    echo '<p class="empty">Chưa có sản phẩm nào trong danh sách yêu thích!</p>';
                }
                ?>
            </div>
    </div>

    </section>

    <?php include 'components/footer.php'; ?>
    </div>
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="script.js"></script>
    <?php include 'components/alert.php'; ?>
</body>

</html>