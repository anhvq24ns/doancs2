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
?>
<style type="text/css">
  <?php include 'style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DecorMimic - Về Chúng Tôi</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
  <?php include 'components/header.php'; ?>
  <div class="main">
    <div class="banner">
        <h1>Về Chúng Tôi</h1>
    </div>
    <div class="title2">
    <a href="home.php">Trang Chủ</a><span>/ Về Chúng Tôi</span>
    </div>
    <div class="about-category">
        <!-- Tượng trang trí -->
        <div class="box">
            <img src="img/tuong.webp" alt="Tượng trang trí">
            <div class="detail">
                <span class="tag">Tượng</span>
                <h1>Tượng trang trí</h1>
                <p>Tượng decor mang vẻ đẹp nghệ thuật, phù hợp mọi phong cách từ hiện đại đến cổ điển.</p>
                <a href="view_products.php" class="btn">Mua ngay</a>
            </div>
        </div>

        <!-- Đồng hồ treo tường -->
        <div class="box">
            <img src="img/dongho.webp" alt="Đồng hồ treo tường">
            <div class="detail">
                <span class="tag">Đồng hồ</span>
                <h1>Đồng hồ treo tường</h1>
                <p>Không chỉ xem giờ, đồng hồ còn là điểm nhấn sang trọng, tạo sự lấp lánh cho không gian sống.</p>
                <a href="view_products.php" class="btn">Mua ngay</a>
            </div>
        </div>

        <!-- Đồ decor để bàn -->
        <div class="box">
            <img src="img/diacau.webp" alt="Đồ decor để bàn">
            <div class="detail">
                <span class="tag">Để bàn</span>
                <h1>Đồ decor để bàn</h1>
                <p>Những phụ kiện nhỏ xinh như quả địa cầu, mô hình, hộp trang trí – vừa đẹp vừa tiện dụng.</p>
                <a href="view_products.php" class="btn">Mua ngay</a>
            </div>
        </div>

        <!-- Phù điêu -->
        <div class="box">
            <img src="img/phudieu.webp" alt="Phù điêu">
            <div class="detail">
                <span class="tag">Phù điêu</span>
                <h1>Phù điêu trang trí</h1>
                <p>Tác phẩm nghệ thuật nổi 3D trên tường – mang đến vẻ đẹp sang trọng và đẳng cấp cho ngôi nhà.</p>
                <a href="view_products.php" class="btn">Mua ngay</a>
            </div>
        </div>
    </div>
     <section class="services">
        <div class="title">
        <img src="img/logo5.png" class="logo" alt="Logo DecorMimic">
       <h1>Tại Sao Chọn Chúng Tôi</h1>
  <p>Trải nghiệm nhanh – dịch vụ tận tâm – chất lượng vượt trội.</p>

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
        <h3>Giao hàng toàn cầu</h3>
        <p>Giao hàng toàn thế giới</p>
        </div>
        </div>
        </div>
      </section>
      <div class="about">
        <div class="row">
        <div class="img-box">
          <img src="img/noithat.jpg" alt="Showroom Của Chúng Tôi">
        </div>
        <div class="detail">
          <h1>Thăm showroom đẹp của chúng tôi!</h1>
          <p>Chúng tôi không chỉ bán đồ decor – chúng tôi bán cảm hứng sống đẹp mỗi ngày.</p>
                <p>Từ những chiếc đèn ngủ ấm áp, bình hoa thủ công, tranh treo tường nghệ thuật đến cây giả xanh mướt... tất cả đều được chọn lọc kỹ càng để mang đến không gian sống hoàn hảo nhất cho bạn.</p>
                <p>Hơn 50.000+ khách hàng đã tin tưởng và biến nhà thành tổ ấm mơ ước nhờ DecorMimic.</p>
                <p><strong>Bạn đã sẵn sàng chưa?</strong>
              </p>
          <a href="view_products.php" class="btn">Mua Ngay</a>
        </div>
        </div>
      </div>
      <div class="testimonial-container">
    <div class="title">
        <img src="img/logo1.jpg" class="logo" alt="Logo DecorMimic">
        <h1>Mọi người nói gì về chúng tôi</h1>
        <p>“Đơn giản là tuyệt vời! Không thể tìm thấy dịch vụ nào tốt hơn đâu!”</p>
    </div>

    <div class="container">
        <!-- Testimonial 1 -->
        <div class="testimonial-item active">
            <img src="img/03.jpg" alt="Khách hàng">
            <h1>Nguyễn Lan Anh</h1>
            <p>Đồ decor đẹp, giá hợp lý, ship nhanh. Mình đã mua 3 lần rồi và lần nào cũng ưng ý!</p>
        </div>
        <!-- Testimonial 2 -->
        <div class="testimonial-item">
            <img src="img/02.jpg" alt="Khách hàng">
            <h1>Trần Minh Quân</h1>
            <p>Tranh treo tường chất lượng cao, màu sắc y như hình. Rất hài lòng!</p>
        </div>
        <!-- Testimonial 3 -->
        <div class="testimonial-item">
            <img src="img/04.png" alt="Khách hàng">
            <h1>Phạm Thu Hương</h1>
            <p>Cây giả xanh mướt, nhìn như thật luôn! Cảm ơn DecorMimic đã giúp phòng mình đẹp hơn.</p>
        </div>

        <div class="left-arrow" onclick="prevTesti()"><i class='bx bxs-left-arrow'></i></div>
        <div class="right-arrow" onclick="nextTesti()"><i class='bx bxs-right-arrow'></i></div>
    </div>
</div>
<div class="container">
    <?php
    // 🔹 Lấy 3 testimonials từ bảng message, JOIN với users để lấy ảnh profile (order mới nhất)
    $select_testimonials = $conn->prepare("
        SELECT m.*, u.profile_image 
        FROM message m 
        LEFT JOIN users u ON m.user_id = u.id 
        ORDER BY m.id DESC LIMIT 3
    ");
    $select_testimonials->execute();
    $testimonials = $select_testimonials->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($testimonials)) {
        foreach ($testimonials as $index => $testimonial): 
            // 🔹 Lấy ảnh từ profile_image nếu có, fallback placeholder theo index
            $img_src = !empty($testimonial['profile_image']) ? $testimonial['profile_image'] : "img/0" . ($index + 1) . ".jpg";
            $active_class = ($index == 0) ? 'active' : ''; // Chỉ slide đầu active
    ?>
            <div class="testimonial-item <?= $active_class; ?>">
                <img src="<?= htmlspecialchars($img_src); ?>" alt="<?= htmlspecialchars($testimonial['name']); ?>">
                <h1><?= htmlspecialchars($testimonial['name']); ?></h1>
                <p><?= htmlspecialchars($testimonial['message']); ?></p>
            </div>
    <?php 
        endforeach;
    }
    ?>
    <div class="left-arrow" onclick="nextSlide()"><i class="bx bxs-left-arrow"></i></div>
    <div class="right-arrow" onclick="prevSlide()"><i class="bx bxs-right-arrow"></i></div>
</div>
      </div>
    <?php include 'components/footer.php'; ?>
  </div>
  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  <script src="script.js"></script>
  <?php include 'components/alert.php'; ?>
</body>

</html>