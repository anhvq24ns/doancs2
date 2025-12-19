<?php
include 'components/connection.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// 🔹 Xử lý đăng xuất
if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
    exit;
}

// 🔹 Lấy sản phẩm trending (active, order by id DESC - giả sử mới nhất là trending)
$trending_products = [];
$select_products = $conn->prepare("SELECT * FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 6");
$select_products->execute();
$trending_products = $select_products->fetchAll(PDO::FETCH_ASSOC);
?>
<style type="text/css">
  <?php include 'style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DecorMimic - Trang trí không gian sống đẹp từng chi tiết</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
  <?php include 'components/header.php'; ?>
  <div class="main">
    <section class="home-section">
      <div class="slider">
        <!-- Slide 1 -->
        <div class="slider__slider slider1">
          <div class="overlay"></div>
          <div class="slider-detail">
            <h1>Trang trí phòng ngủ phong cách Vintage</h1>
            <p>Làm đẹp không gian sống theo phong cách nhẹ nhàng & ấm áp.</p>
            <a href="view_products.php" class="btn">Mua Ngay</a>
          </div>
          <div class="hero-dec-top"></div>
          <div class="hero-dec-bottom"></div>
        </div>

        <!-- Slide 2 -->
        <div class="slider__slider slider2">
          <div class="overlay"></div>
          <div class="slider-detail">
            <h1>Đèn Ngủ</h1>
            <p>Tạo ánh sáng ấm áp cho không gian thư giãn.</p>
            <a href="view_products.php" class="btn">Mua Ngay</a>
          </div>
          <div class="hero-dec-top"></div>
          <div class="hero-dec-bottom"></div>
        </div>

        <!-- Slide 3 -->
        <div class="slider__slider slider3">
          <div class="overlay"></div>
          <div class="slider-detail">
            <h1>Cây & Hoa giả</h1>
            <p>Làm tươi mới không gian mà không cần chăm sóc.</p>
            <a href="view_products.php" class="btn">Mua Ngay</a>
          </div>
          <div class="hero-dec-top"></div>
          <div class="hero-dec-bottom"></div>
        </div>

        <!-- Slide 4 -->
        <div class="slider__slider slider4">
          <div class="overlay"></div>
          <div class="slider-detail">
            <h1>Tranh treo tường</h1>
            <p>Tăng tính thẩm mỹ và điểm nhấn cho căn phòng.</p>
            <a href="view_products.php" class="btn">Mua Ngay</a>
          </div>
          <div class="hero-dec-top"></div>
          <div class="hero-dec-bottom"></div>
        </div>

        <!-- Slide 5 -->
        <div class="slider__slider slider5">
          <div class="overlay"></div>
          <div class="slider-detail">
            <h1>Tham Gia Cộng Đồng Của Chúng Tôi</h1>
            <p>Đăng ký để nhận ưu đãi độc quyền.</p>
            <a href="view_products.php" class="btn">Mua Ngay</a>
          </div>
          <div class="hero-dec-top"></div>
          <div class="hero-dec-bottom"></div>
        </div>

        <!-- Arrows với accessibility -->
        <div class="left-arrow"><i class='bx bxs-left-arrow'></i></div>
        <div class="right-arrow"><i class='bx bxs-right-arrow'></i></div>
      </div>
    </section>
    <!-----home slider end--->
      <section class="thumb">
        <div class="box-container">
        <div class="box">
          <img src="img/binhhoa2.jpg" alt="Bình Hoa">
          <h3>Bình Hoa</h3>
           <p>Đăng ký để nhận ưu đãi độc quyền.</p>
           <i class="bx bx-chevron-right"></i>
        </div>
        <div class="box">
          <img src="img/denngu.jpg" alt="Đèn Ngủ">
          <h3>Đèn Ngủ</h3>
           <p>Đăng ký để nhận ưu đãi độc quyền.</p>
           <i class="bx bx-chevron-right"></i>
        </div>
        <div class="box">
          <img src="img/ghe.jpg" alt="Ghế Ngồi">
          <h3>Ghế Ngồi</h3>
           <p>Đăng ký để nhận ưu đãi độc quyền.</p>
           <i class="bx bx-chevron-right"></i>
        </div>
        <div class="box">
          <img src="img/kesach.jpg" alt="kệ đẻ sách">
          <h3>Kệ Để Sách</h3>
           <p>Đăng ký để nhận ưu đãi độc quyền.</p>
           <i class="bx bx-chevron-right"></i>
        </div>
        </div>
      </section>
        <!-- ==================== KHUYẾN MÃI SALE LỚN ==================== -->
        <section class="sale-promo-section">
            <div class="sale-promo-container">
                <!-- Ảnh bên trái -->
                <div class="promo-image">
                    <img src="img/2.webp" alt="Phòng decor đẹp">
                </div>

                
                <div class="promo-content">
                    <div class="logo-leaf">
                        <img src="img/download.png" alt="DecorMimic"> 
                    </div>
                    <h3 class="highlight">Mua Sắm</h3>
                    <h1 class="big-sale">Tiết kiệm đến <span>50%</span></h1>
                    <p>Hàng ngàn món decor hot nhất 2025.</p>
                    <a href="view_products.php" class="btn btn-large">Mua ngay</a>
                </div>
            </div>
        </section>
      <section class="shop">
      <div class="title">
       <img src="img/download.png" alt="Logo">
       <h1>Sản phẩm nổi bật</h1> 
      </div>
      <div class="row">
      <img src="img/11.jpg" alt="Giới thiệu">
      <div class="row-detail">
        <img src="img/10.png" alt="Basil">
        <div class="top-footer">
          <h1>Mua sắm thả ga không lo về giá cả.</h1>
        </div>
      </div>
      </div>
      <div class="box-container">
      <?php if (!empty($trending_products)): ?>
        <?php foreach ($trending_products as $product): ?>
        <div class="box">
          <img src="img/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
          <a href="view_products.php" class="btn">Mua Ngay</a>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="empty">Chưa có sản phẩm nào!</p>
      <?php endif; ?>
      </div>
      </section>
      <section class="shop-category">
      <div class="box-container">
      <div class="box">
      <img src="img/uudai.jpg" alt="Ưu Đãi Lớn">
      <div class="detail">
        <span>ƯU ĐÃI LỚN</span>
      <h1>Giảm thêm 15%</h1>
      <a href="view_products.php" class="btn">Mua Ngay</a>
      </div>
      </div>  
      <div class="box">
      <img src="img/noithat.jpg" alt="San Phẩm mới">
      <div class="detail">
        <span>Sản Phẩm mới</span>
      <h1>Thiết kế Đẹp và Tinh Xảo</h1>
      <a href="view_products.php" class="btn">Mua Ngay</a>
      </div>
      </div>  
      </div>
      </section>
      <section class="services">
        <div class="box-container">
        <div class="box">
        <img src="img/icon2.png" alt="Tiết Kiệm Lớn">
        <div class="detail">
        <h3>Tiết kiệm lớn</h3>
        <p>Tiết kiệm lớn mỗi đơn hàng</p>
        </div>
        </div>
         <div class="box">
        <img src="img/icon1.png" alt="Hỗ Trợ 24/7">
        <div class="detail">
        <h3>Hỗ trợ 24/7</h3>
        <p>Hỗ trợ một-một</p>
        </div>
        </div>
         <div class="box">
        <img src="img/icon0.png" alt="Phiếu Quà Tặng">
        <div class="detail">
        <h3>Phiếu quà tặng</h3>
        <p>Phiếu quà trên mọi lễ hội</p>
        </div>
        </div>
         <div class="box">
        <img src="img/icon.png" alt="Giao Hàng Toàn Cầu">
        <div class="detail">
        <h3>Giao hàng toàn quốc</h3>
        <p>Giao hàng mọi lúc, mọi nơi</p>
        </div>
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