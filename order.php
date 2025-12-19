<?php
include 'components/connection.php';
session_start();

// 🔹 Kiểm tra đăng nhập
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header("Location: login.php");
    exit;
}

// 🔹 Đăng xuất
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
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
  <title>DecorMimic - Đơn hàng của tôi</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
  <?php include 'components/header.php'; ?>

  <div class="main">
    <div class="banner">
      <h1>Đơn hàng của tôi</h1>
    </div>

    <div class="title2">
      <a href="home.php">Trang chủ</a><span> / Đơn hàng của tôi</span>
    </div>

    <section class="orders">
      <div class="box-container">
        <?php
        // 🔹 Lấy tất cả đơn hàng của người dùng hiện tại
        $select_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
        $select_orders->execute([$user_id]);

        if ($select_orders->rowCount() > 0) {
            while ($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                // 🔹 Lấy thông tin sản phẩm tương ứng
                $select_product = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
                $select_product->execute([$fetch_order['product_id']]);
                $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
                
                if (!$fetch_product) {
                    $fetch_product = ['name' => 'Sản phẩm không xác định', 'image' => 'default.jpg'];
                }

                // 🔹 Xác định trạng thái hiển thị dựa trên cả status và payment_status
                $status_display = '';
                $status_color = 'orange'; // Mặc định là màu cam (đang xử lý)
                
                if ($fetch_order['status'] == 'delivered' && $fetch_order['payment_status'] == 'complete') {
                    $status_display = 'Đã giao hàng';
                    $status_color = 'green';
                } elseif ($fetch_order['status'] == 'canceled') {
                    $status_display = 'Đã hủy';
                    $status_color = 'red';
                } elseif ($fetch_order['payment_status'] == 'complete' && $fetch_order['status'] == 'pending') {
                    $status_display = 'Đã thanh toán - Đang giao hàng';
                    $status_color = 'blue';
                } elseif ($fetch_order['payment_status'] == 'pending' || $fetch_order['payment_status'] == 'unpaid') {
                    $status_display = 'Đang xử lý';
                    $status_color = 'orange';
                } else {
                    $status_display = 'Đang xử lý';
                    $status_color = 'orange';
                }
        ?>
        <div class="box">
          <img src="img/<?= $fetch_product['image']; ?>" alt="">
          <h3><?= $fetch_product['name']; ?></h3>
          <p>Số lượng: <span><?= $fetch_order['qty']; ?></span></p>
          <p>Giá: <span>$<?= number_format($fetch_order['price']); ?></span></p>
          <p>Ngày đặt: <span><?= date('d/m/Y H:i', strtotime($fetch_order['date'])); ?></span></p>
          <p>Trạng thái: 
            <span style="color: <?= $status_color; ?>; font-weight: bold;">
              <?= $status_display; ?>
            </span>
          </p>
          <p>Thanh toán: 
            <span style="color: <?= ($fetch_order['payment_status'] == 'complete') ? 'green' : 'red'; ?>;">
              <?= ($fetch_order['payment_status'] == 'complete') ? 'Đã thanh toán' : 'Chưa thanh toán'; ?>
            </span>
          </p>

          <a href="order.php?get_id=<?= $fetch_order['id']; ?>" class="btn">Xem chi tiết</a>
        </div>
        <?php
            }
        } else {
            echo '<p class="empty">Bạn chưa có đơn hàng nào.</p>';
        }
        ?>
      </div>
    </section>

    <?php include 'components/footer.php'; ?>
  </div>

  <script src="script.js"></script>
</body>
</html>