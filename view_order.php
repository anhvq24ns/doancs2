<?php
include 'components/connection.php';
session_start();

// 🔹 Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// 🔹 Đăng xuất
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// 🔹 Lấy ID đơn hàng
if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    header("Location: orders.php");
    exit;
}

// 🔹 Hủy đơn hàng (chỉ cho phép user hiện tại hủy đơn của chính họ, và chỉ nếu chưa hủy)
if (isset($_POST['cancel'])) {
    $update_order = $conn->prepare("UPDATE orders SET status = ? WHERE id = ? AND user_id = ? AND status != 'canceled'");
    $update_order->execute(['canceled', $get_id, $user_id]);
    header("Location: view_order.php?get_id=" . $get_id);
    exit;
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
  <title>DecorMimic - Chi tiết đơn hàng</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
  <?php include 'components/header.php'; ?>

  <div class="main">
    <div class="banner">
      <h1>Chi tiết đơn hàng</h1>
    </div>

    <div class="title2">
      <a href="home.php">Trang chủ</a><span> / Đơn hàng của tôi / Chi tiết</span> <!-- 🔹 Sửa: Breadcrumb chính xác hơn -->
    </div>

    <section class="order-detail">
      <div class="title">
        <img src="img/download.png" alt="" class="logo">
        <h1>Đơn hàng của tôi</h1>
        <p>Dưới đây là chi tiết đơn hàng bạn đã chọn.</p>
      </div>

      <div class="box-container">
        <?php 
        $grand_total = 0;

        // 🔹 Lấy thông tin đơn hàng theo ID + user_id
        $select_orders = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
        $select_orders->execute([$get_id, $user_id]);

        if ($select_orders->rowCount() > 0) {
            $fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC);

            // 🔹 Lấy thông tin sản phẩm tương ứng - Sửa logic: Fallback nếu sản phẩm không tồn tại
            $select_product = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
            $select_product->execute([$fetch_order['product_id']]);
            
            if ($select_product->rowCount() > 0) {
                $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
            } else {
                $fetch_product = ['name' => 'Sản phẩm không xác định', 'image' => 'default.jpg']; // Fallback để tránh lỗi
            }

            // 🔹 Tính tổng tiền
            $qty = $fetch_order['qty'];
            $price = $fetch_order['price'];
            $sub_total = $price * $qty;
            $grand_total += $sub_total;
        ?>
        <div class="box">
          <div class="col">
            <p class="title"><i class="bi bi-calendar-fill"></i> <?= date('d/m/Y H:i', strtotime($fetch_order['date'])); ?></p> <!-- 🔹 Sửa: Format ngày tháng dễ đọc -->   
            <img src="img/<?= $fetch_product['image']; ?>" class="img" alt="">
            <h3 class="name"><?= $fetch_product['name']; ?></h3> 
            <p class="price">Giá: $<?= number_format($price); ?> × <?= $qty; ?></p> <!-- 🔹 Sửa: Format giá dễ đọc -->  
            <p class="grand-total">Tổng thanh toán: <span>$<?= number_format($grand_total); ?></span></p> <!-- 🔹 Sửa: Format tổng tiền -->
          </div>

          <div class="col">
            <p class="title">Địa chỉ thanh toán</p>
            <p class="user"><i class="bi bi-person-bounding-box"></i> <?= $fetch_order['name']; ?></p>
            <p class="user"><i class="bi bi-phone"></i> <?= $fetch_order['number']; ?></p>
            <p class="user"><i class="bi bi-envelope"></i> <?= $fetch_order['email']; ?></p>
            <p class="user"><i class="bi bi-pin-map-fill"></i> <?= $fetch_order['address']; ?></p>

            <p class="title">Trạng thái đơn hàng</p>
            <p class="status" 
               style="color:<?php 
                    if ($fetch_order['status'] == 'delivered') echo 'green';
                    elseif ($fetch_order['status'] == 'canceled') echo 'red';
                    else echo 'orange';
                ?>">
              <?php 
                if ($fetch_order['status'] == 'delivered') echo 'Đã giao hàng';
                elseif ($fetch_order['status'] == 'canceled') echo 'Đã hủy';
                else echo 'Đang xử lý';
              ?>
            </p>

            <p class="title">Trạng thái thanh toán</p>
            <p class="status" style="color:<?php 
                echo ($fetch_order['payment_status'] == 'paid') ? 'green' : 'red';
            ?>">
              <?php 
                if ($fetch_order['payment_status'] == 'paid') echo 'Đã thanh toán';
                else echo 'Chưa thanh toán';
              ?>
            </p>

            <?php if ($fetch_order['status'] == 'canceled') { ?>
              <a href="checkout.php?get_id=<?= $fetch_order['product_id']; ?>" class="btn">Đặt lại</a> <!-- 🔹 Sửa: Dùng product_id từ order để reorder -->
            <?php } else { ?> 
              <form method="post">
                <button type="submit" name="cancel" class="btn" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này không?')">Hủy đơn hàng</button>
              </form>
            <?php } ?>        
          </div>
        </div>
        <?php
        } else {
          echo '<p class="empty">Không tìm thấy đơn hàng hoặc bạn không có quyền xem đơn này.</p>';
        }
        ?>
      </div>
    </section>

    <?php include 'components/footer.php'; ?>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  <script src="script.js"></script>
  <?php include 'components/alert.php'; ?>
</body>
</html>