<?php
session_start();
include('config/db.php');

$products = $conn->query("SELECT * FROM products WHERE stock_status != 'Out of Stock' ORDER BY id DESC LIMIT 6");
$testimonials = $conn->query("SELECT * FROM testimonials ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>FishNation - Premium Fingerlings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Healthy, disease-free fingerlings for farmers across Nigeria.">
  <meta name="keywords" content="fingerlings, catfish, juveniles, fish farm, aquaculture, FishNation">

  <!-- Favicons -->
  <link href="assets/img/fish.jpeg" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Raleway:wght@500;600;700;800&display=swap" rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    :root {
      --accent-color: #45A80D;
      --accent-color-dark: #45A80D;
      --accent-color-light: #45A80D;
      --bg-dark: #45A80D;
      --bg-deep: #45A80D;
      --bg-soft: #45A80D;
      --text-light: #e8f5e9;
      --text-muted: #b7d3bf;
      --card-bg: rgba(255, 255, 255, 0.05);
      --border-soft: rgba(255, 255, 255, 0.08);
    }

    body {
      background: linear-gradient(180deg, #07150f 0%, #0b2418 50%, #0f2d1f 100%);
      color: var(--text-light);
      font-family: 'Inter', sans-serif;
    }

    h1, h2, h3, h4, h5, h6,
    .sitename,
    .section-title h2,
    .hero-headline {
      font-family: 'Raleway', sans-serif;
      color: #f4fff6 !important;
    }

    p, span, li, a, label, input, textarea, select {
      color: var(--text-muted);
    }

    .header {
      background: rgba(7, 21, 15, 0.85) !important;
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .navmenu a,
    .navmenu a:focus {
      color: #d8f3dc !important;
      font-weight: 500;
    }

    .navmenu a:hover,
    .navmenu .active {
      color: #7ef29d !important;
    }

    .btn-getstarted,
    .btn-primary,
    .btn-primary-custom,
    .btn-main,
    .btn-submit,
    .hero-actions .btn-primary {
      background: linear-gradient(135deg, #45A80D, #45A80D) !important;
      border: none !important;
      color: white !important;
      border-radius: 14px !important;
      font-weight: 600;
      box-shadow: 0 10px 25px rgba(31, 122, 61, 0.25);
    }

    .btn-outline,
    .hero-actions .btn-outline {
      border: 1px solid #45A80D !important;
      color: #d8f3dc !important;
      background: transparent !important;
      border-radius: 14px !important;
    }

    .btn-outline:hover {
      background: rgba(126, 242, 157, 0.08) !important;
    }

    .hero {
      background:
        radial-gradient(circle at top right, rgba(47, 158, 87, 0.18), transparent 35%),
        radial-gradient(circle at top left, rgba(31, 122, 61, 0.2), transparent 40%),
        linear-gradient(180deg, #081c15 0%, #0b2418 100%);
    }

    .hero-tag,
    .section-badge,
    .member-badge {
      background: rgba(126, 242, 157, 0.1) !important;
      color: #98f5b3 !important;
      border: 1px solid rgba(126, 242, 157, 0.18);
    }

    .hero-text,
    .lead-text,
    .description,
    .section-title p {
      color: #cfe8d5 !important;
    }

    .stat-card,
    .feature-box,
    .service-card,
    .team-card,
    .testimonial-item,
    .form-card,
    .info-panel,
    .capability-card,
    .feature-card,
    .stat-box,
    .box,
    .footer-newsletter,
    .about-images-wrapper,
    .sidebar-content,
    .gallery-card {
      background: rgba(255, 255, 255, 0.045) !important;
      border: 1px solid var(--border-soft);
      border-radius: 24px !important;
      backdrop-filter: blur(10px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .service-card {
      padding: 30px 25px;
      margin-bottom: 25px;
      height: 100%;
      transition: 0.3s ease;
    }

    .service-card:hover,
    .gallery-card:hover {
      transform: translateY(-8px);
      border-color: rgba(126, 242, 157, 0.18);
    }

    .service-card h3,
    .feature-content h4,
    .member-info h4,
    .form-card-header h4,
    .panel-header h3,
    .footer h4 {
      color: #f1fff3 !important;
    }

    .icon-wrapper,
    .feature-icon,
    .method-icon,
    .header-icon,
    .capability-icon,
    .icon-box,
    .stat-icon-wrap {
      background: linear-gradient(135deg, #3a6b1d, #335b1c) !important;
      color: #f1fff3 !important;
      border-radius: 16px;
      width: 58px;
      height: 58px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      box-shadow: 0 10px 20px rgba(31,122,61,0.2);
    }

    .service-link,
    .feature-link,
    .legal-links a,
    .footer-links a,
    .social-links a,
    .social-icons a,
    .contact-info a {
      color: #9cf0b3 !important;
    }

    .service-link:hover,
    .feature-link:hover,
    .footer-links a:hover,
    .legal-links a:hover {
      color: #ffffff !important;
    }

    .badge.bg-success {
      background: #1f7a3d !important;
      color: white !important;
      padding: 8px 12px;
      border-radius: 10px;
      font-size: 12px;
    }

    .input-wrapper,
    input,
    textarea,
    select,
    .form-control {
      background: rgba(255,255,255,0.05) !important;
      border: 1px solid rgba(255,255,255,0.08) !important;
      color: #f4fff6 !important;
      border-radius: 14px !important;
    }

    input::placeholder,
    textarea::placeholder {
      color: #b7d3bf !important;
    }

    .contact,
    .why-us,
    .about,
    .services,
    .team,
    .testimonials,
    .section,
    .light-background,
    footer {
      background: transparent !important;
    }

    .section-title h2::after {
      background: linear-gradient(90deg, #1f7a3d, #7ef29d) !important;
    }

    .testimonial-item {
      padding: 30px;
      margin-bottom: 25px;
      position: relative;
      min-height: 220px;
      display: flex;
      align-items: center;
    }

    .testimonial-item p {
      font-size: 17px;
      line-height: 1.8;
      color: #d8f3dc !important;
      margin-bottom: 0;
    }

    .quote-icon-left,
    .quote-icon-right {
      color: #7ef29d !important;
    }

    .stats-strip,
    .stats-wrapper,
    .team-stats,
    .stat-cards {
      background: rgba(255,255,255,0.04);
      border-radius: 22px;
      padding: 20px;
      border: 1px solid rgba(255,255,255,0.06);
    }

    .stat-number,
    .stat-value,
    .number,
    .years {
      color: #7ef29d !important;
      font-weight: 800;
    }

    .footer {
      background: #06120d !important;
      color: #d8f3dc !important;
      border-top: 1px solid rgba(255,255,255,0.06);
    }

    .footer .sitename,
    .footer strong,
    .footer h4 {
      color: white !important;
    }

    .footer input {
      background: rgba(255,255,255,0.06) !important;
      border: 1px solid rgba(255,255,255,0.08) !important;
      color: white !important;
    }

    .btn-subscribe {
      background: linear-gradient(135deg, #1f7a3d, #2f9e57) !important;
      border: none !important;
      color: white !important;
    }

    .scroll-top {
      background: #1f7a3d !important;
    }

    .mobile-nav-toggle {
      color: white !important;
    }

    .section-title p,
    .feature-card span,
    .method-details span,
    .member-info p,
    .stat-label,
    .stat-text,
    .capability-card p {
      color: #cfe8d5 !important;
    }

    img {
      border-radius: 20px;
    }

    .gallery-card {
      overflow: hidden;
      transition: 0.3s ease;
      height: 100%;
    }

    .gallery-card img,
    .gallery-card video {
      width: 100%;
      height: 260px;
      object-fit: cover;
      border-radius: 18px;
    }

    .gallery-card .gallery-info {
      padding: 18px 10px 5px;
    }

    .gallery-card h5 {
      color: #f4fff6;
      margin-bottom: 8px;
      font-weight: 700;
    }

    .gallery-card p {
      font-size: 14px;
      color: #cfe8d5;
      margin-bottom: 0;
    }

    .swiper-button-next,
    .swiper-button-prev {
      color: #7ef29d !important;
    }

    .swiper-pagination-bullet {
      background: #7ef29d !important;
      opacity: 0.5;
    }

    .swiper-pagination-bullet-active {
      opacity: 1;
    }

    .testimonial-slider .swiper-slide {
      height: auto;
    }
  </style>
</head>

<body class="index-page">

  <!-- Header -->
  <?php
  $current_page = 'home';
  include 'navbar.php';
  ?>

  <main class="main">

    <!-- Hero -->
    <section id="hero" class="hero section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row align-items-center gy-5 ">

          <div class="col-lg-6" data-aos="fade-right" data-aos-delay="200">
            <div class="hero-content">
              <div class="hero-tag mb-3 px-3 py-2 d-inline-flex align-items-center rounded-pill">
                <span class="tag-dot me-2"></span>
                <span class="tag-text">Premium Fish Supply in Nigeria</span>
              </div>

              <h1 class="hero-headline">
                Healthy Fingerlings for<br>Smarter Fish Farming
              </h1>

              <p class="hero-text">
                Get active, disease-free fingerlings sourced for better survival, stronger growth,
                and more confident fish production.
              </p>

              <div class="hero-actions d-flex gap-3 mt-4">
                <a href="#services" class="btn btn-primary px-4 py-3">Browse Fingerlings</a>
                <a href="shop.php"  class="btn btn-outline px-4 py-3">Shop All Products</a>
              </div>

              <div class="mt-4 d-flex flex-wrap gap-3" >
                <div class="row">
                  <div class="col-3">
                    <div>
                  <strong style="color:#7ef29d; font-size:1.2rem;">Healthy</strong><br>
                  <small>Quality-assured stock</small>
                </div>
                  </div>
                  <div class="col-3">
                    <div>
                  <strong style="color:#7ef29d; font-size:1.2rem;">Fast</strong><br>
                  <small>Delivery support available</small>
                </div>
                  </div>
                  <div class="col-3">
                    <div>
                  <strong style="color:#7ef29d; font-size:1.2rem;">Trusted</strong><br>
                  <small>Built for serious farmers</small>
                </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          
                
                
          <div class="col-lg-6" data-aos="fade-left" data-aos-delay="300">
            <div class="stats-grid">
              <div class="stat-card stat-card-primary mb-3 p-4">
                <div class="d-flex align-items-center gap-3">
                  <div class="stat-icon-wrap"><i class="bi bi-droplet-half"></i></div>
                  <div class="stat-info">
                    <span class="stat-value d-block">92%</span>
                    <span class="stat-title">Survival Rate</span>
                  </div>
                </div>
              </div>

              <div class="stat-card mb-3 p-4">
                <div class="d-flex align-items-center gap-3">
                  <div class="stat-icon-wrap"><i class="bi bi-truck"></i></div>
                  <div class="stat-info">
                    <span class="stat-value d-block">Fast</span>
                    <span class="stat-title">Live Delivery</span>
                  </div>
                </div>
              </div>

              <div class="stat-card mb-3 p-4">
                <div class="d-flex align-items-center gap-3">
                  <div class="stat-icon-wrap"><i class="bi bi-shield-check"></i></div>
                  <div class="stat-info">
                    <span class="stat-value d-block">Healthy</span>
                    <span class="stat-title">Quality Assured</span>
                  </div>
                </div>
              </div>

              <div class="stat-card stat-card-accent p-4">
                <div class="d-flex align-items-center gap-3">
                  <div class="stat-icon-wrap"><i class="bi bi-people"></i></div>
                  <div class="stat-info">
                    <span class="stat-value d-block">Trusted</span>
                    <span class="stat-title">By Farmers</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Why Buy From Us -->
    <section class="section py-4">
      <div class="container">
        <div class="stats-wrapper">
          <div class="row text-center g-4">
            <div class="col-md-4">
              <div class="p-3">
                <div class="icon-wrapper mx-auto mb-3"><i class="bi bi-droplet-half"></i></div>
                <h4>Healthy Fingerlings</h4>
                <p>Carefully selected stock with better activity and stronger survival potential.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3">
                <div class="icon-wrapper mx-auto mb-3"><i class="bi bi-truck"></i></div>
                <h4>Reliable Delivery</h4>
                <p>Handled with care and prepared for smoother movement to your farm.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3">
                <div class="icon-wrapper mx-auto mb-3"><i class="bi bi-chat-dots"></i></div>
                <h4>Farmer Support</h4>
                <p>Guidance to help you choose the right fish size and quantity for your goals.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- About -->
    <section id="about" class="about section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-5 align-items-center">

          <div class="col-xl-6" data-aos="fade-right" data-aos-delay="200">
            <div class="about-images-wrapper p-3">
              <div class="image-main">
                <img src="assets/img/fingerlings2.png" alt="FishNation Fish Farm" class="img-fluid">
              </div>
            </div>
          </div>

          <div class="col-xl-6" data-aos="fade-left" data-aos-delay="300">
            <div class="about-content">
              <div class="section-subtitle" style="color:#7ef29d;">Who We Are</div>
              <h2>Reliable Fish Supply for Serious Farmers</h2>
              <p class="lead-text">
                FishNation exists to help fish farmers grow with confidence through premium fingerlings,
                expert guidance, and dependable delivery.
              </p>
              <p class="mb-4 description">
                Whether you are stocking a new pond or scaling production, we provide strong and healthy fingerlings
                that support better growth performance and reduced losses.
              </p>

              <div class="features-grid">
                <div class="feature-card"><i class="bi bi-check-circle-fill"></i> <span>Healthy Stock</span></div>
                <div class="feature-card"><i class="bi bi-check-circle-fill"></i> <span>Fast Delivery</span></div>
                <div class="feature-card"><i class="bi bi-check-circle-fill"></i> <span>Farmer Support</span></div>
                <div class="feature-card"><i class="bi bi-check-circle-fill"></i> <span>Trusted Quality</span></div>
              </div>

              <div class="stats-row mt-4 d-flex gap-3 flex-wrap">
                <div class="stat-box p-3 text-center">
                  <span class="number d-block">1000+</span>
                  <span class="label">Fingerlings Supplied</span>
                </div>
                <div class="stat-box p-3 text-center">
                  <span class="number d-block">Growing</span>
                  <span class="label">Farmer Network</span>
                </div>
                <div class="stat-box p-3 text-center">
                  <span class="number d-block">Reliable</span>
                  <span class="label">Support System</span>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Products -->
    <section id="services" class="services section">
      <div class="container section-title">
        <h2>Available Fingerlings</h2>
        <p>Healthy, disease-free stock ready for delivery</p>
      </div>

      <div class="container">
        <div class="row">
          <?php while($row = $products->fetch_assoc()): ?>
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="service-card h-100 shadow-sm p-3 rounded d-flex flex-column">

                <div class="shop-image-wrap text-center mb-3 position-relative">
                  <img src="uploads/<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'default.jpg'; ?>" 
                       alt="<?php echo htmlspecialchars($row['fish_name']); ?>" 
                       class="img-fluid rounded"
                       style="height: 240px; object-fit: cover; width: 100%;">

                  <span class="badge bg-success position-absolute top-0 end-0 m-3">
                    <?php echo htmlspecialchars($row['stock_status']); ?>
                  </span>
                </div>

                <div class="text-center flex-grow-1 d-flex flex-column justify-content-between">
                  <div>
                    <h3 class="mb-2"><?php echo htmlspecialchars($row['fish_name']); ?></h3>

                    <p class="mb-2">
                      <strong>Origin:</strong>
                      <?php echo !empty($row['origin']) ? htmlspecialchars($row['origin']) : 'Farm Raised'; ?>
                    </p>

                    <p class="mb-3" style="font-size: 1.1rem; color:#7ef29d; font-weight:700;">
                      ₦<?php echo number_format($row['price_min']); ?> - ₦<?php echo number_format($row['price_max']); ?>
                    </p>
                  </div>

                  <a href="view-more.php?id=<?php echo $row['id']; ?>" class="btn btn-success w-100 mt-2">
                    Buy Now <i class="bi bi-arrow-right"></i>
                  </a>
                </div>

              </div>
            </div>
          <?php endwhile; ?>
        </div>

        <div class="text-center mt-4">
          <a href="shop.php" class="btn btn-primary px-4 py-3">View All Products</a>
        </div>
      </div>
    </section>

    
    <section id="gallery" class="section py-5">
      <div class="container section-title" data-aos="fade-up">
        <h2>Inside FishNation</h2>
        <p>Take a closer look at our stock quality, farm handling, and real delivery-ready fingerlings.</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="swiper gallery-slider" style="height:70vh; width:100%">
          <div class="swiper-wrapper">

            
            <div class="swiper-slide">
              <div class="gallery-card p-3">
                <a href="assets/img/gallery1.jpg" class="glightbox" data-gallery="fishnation-gallery">
                  <img src="assets/img/gallery1.jpg" alt="Healthy Fingerlings Batch">
                </a>
                <div class="gallery-info">
                  <h5>Healthy Fingerlings Batch</h5>
                  <p>Fresh, active stock prepared for fish farmers.</p>
                </div>
              </div>
            </div>

            
            <div class="swiper-slide" >
              <div class="gallery-card p-3">
                <a href="assets/img/fingerlings2.png" class="glightbox" data-gallery="fishnation-gallery">
                  <img src="assets/img/fingerlings2.png" alt="Farm Sorting Process">
                </a>
                <div class="gallery-info">
                  <h5>Live Pond Activity</h5>
                  <p>See our fingerlings in motion and observe stock vitality.</p>
                </div>
              </div>
            </div>

           
            <div class="swiper-slide">
              <div class="gallery-card p-3">
                <a href="assets/img/gallery2.jpg" class="glightbox" data-gallery="fishnation-gallery">
                  <img src="assets/img/gallery2.jpg" alt="Farm Sorting Process">
                </a>
                <div class="gallery-info">
                  <h5>Farm Sorting Process</h5>
                  <p>Careful handling and grading before delivery.</p>
                </div>
              </div>
            </div>

            
            <div class="swiper-slide">
              <div class="gallery-card p-3">
                <a href="assets/img/fingerlings1.png" class="glightbox" data-gallery="fishnation-gallery">
                  <img src="assets/img/fingerlings1.png" alt="Farm Sorting Process">
                </a>
                <div class="gallery-info">
                  <h5>Delivery Preparation</h5>
                  <p>Packaging and preparation for customer orders.</p>
                </div>
              </div>
            </div>

            
            <div class="swiper-slide">
              <div class="gallery-card p-3">
                <a href="assets/img/gallery3.jpg" class="glightbox" data-gallery="fishnation-gallery">
                  <img src="assets/img/gallery3.jpg" alt="Quality Fish Stock">
                </a>
                <div class="gallery-info">
                  <h5>Quality Fish Stock</h5>
                  <p>Premium fingerlings selected for better survival and growth.</p>
                </div>
              </div>
            </div>

          </div>

          <!-- Swiper Controls -->
          <div class="swiper-pagination mt-4"></div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
        </div>
      </div>
    </section>

    <!-- Why Us -->
    <section id="why-us" class="why-us section light-background">
      <div class="container section-title" data-aos="fade-up">
        <h2>Why Farmers Choose FishNation</h2>
        <p>Because survival rate, stock quality, and trust matter.</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-5">
          <div class="col-lg-5" data-aos="fade-right" data-aos-delay="200">
            <div class="sidebar-content p-4">
              <div class="badge-wrapper mb-3">
                <span class="section-badge"><i class="bi bi-stars"></i> Our Difference</span>
              </div>
              <h2>Built Around Fish Farming Success</h2>
              <p class="description">
                We focus on practical value — healthier stock, smoother ordering, and support that helps farmers make better production decisions.
              </p>

              <div class="stat-cards mt-4">
                <div class="stat-card mb-3 p-3">
                  <div class="stat-value">Live</div>
                  <div class="stat-text">Delivery Support</div>
                </div>
                <div class="stat-card mb-3 p-3">
                  <div class="stat-value">Healthy</div>
                  <div class="stat-text">Fingerlings Guaranteed</div>
                </div>
                <div class="stat-card p-3">
                  <div class="stat-value">Trusted</div>
                  <div class="stat-text">By Returning Buyers</div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-7" data-aos="fade-left" data-aos-delay="200">
            <div class="features-grid">
              <div class="feature-box highlight p-4 mb-4">
                <div class="feature-icon mb-3"><i class="bi bi-shield-check"></i></div>
                <div class="feature-content">
                  <h4>Healthy Stock Selection</h4>
                  <p>Every fingerling category is selected to help farmers reduce losses and improve growth performance.</p>
                </div>
              </div>

              <div class="feature-box p-4 mb-4">
                <div class="feature-icon mb-3"><i class="bi bi-truck"></i></div>
                <div class="feature-content">
                  <h4>Reliable Delivery</h4>
                  <p>We make sure your fingerlings reach you quickly and in the best possible condition.</p>
                </div>
              </div>

              <div class="feature-box p-4">
                <div class="feature-icon mb-3"><i class="bi bi-chat-dots"></i></div>
                <div class="feature-content">
                  <h4>Farmer Guidance</h4>
                  <p>Need help choosing fish size or quantity? FishNation helps you make informed buying decisions.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="testimonials section">
      <div class="container section-title" data-aos="fade-up">
        <h2>What Farmers Say</h2>
        <p>Real feedback from real buyers</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="swiper testimonial-slider">
          <div class="swiper-wrapper">
            <?php while($row = $testimonials->fetch_assoc()): ?>
              <div class="swiper-slide">
                <div class="testimonial-item h-100">
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                    <?php echo htmlspecialchars($row['content']); ?>
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
          <div class="swiper-pagination mt-4"></div>
        </div>
      </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="contact section">
      <div class="container section-title">
        <h2>Contact FishNation</h2>
        <p>We’re here to help you grow your fish farming business with confidence.</p>
      </div>

      <div class="container">
        <div class="row gy-5 align-items-stretch">

          <div class="col-lg-5">
            <div class="info-panel p-4 h-100">
              <div class="panel-header mb-4">
                <span class="section-badge">
                  <i class="bi bi-chat-dots-fill"></i> Get In Touch
                </span>

                <h3 class="mt-3">Let’s Grow Your Fish Business</h3>

                <p>
                  Need healthy fingerlings or advice? Reach out to FishNation and let’s help you reduce mortality and increase profits.
                </p>
              </div>

              <div class="contact-methods">
                <div class="method-item d-flex gap-3 mb-4">
                  <div class="method-icon"><i class="bi bi-envelope-paper-fill"></i></div>
                  <div class="method-details">
                    <span class="method-label d-block">Email Us</span>
                    <a href="mailto:info@fishnation.ng">info@fishnation.ng</a>
                  </div>
                </div>

                <div class="method-item d-flex gap-3 mb-4">
                  <div class="method-icon"><i class="bi bi-headset"></i></div>
                  <div class="method-details">
                    <span class="method-label d-block">Call Us</span>
                    <a href="tel:+234XXXXXXXXXX">+234 XXX XXX XXXX</a>
                  </div>
                </div>

                <div class="method-item d-flex gap-3 mb-4">
                  <div class="method-icon"><i class="bi bi-whatsapp"></i></div>
                  <div class="method-details">
                    <span class="method-label d-block">WhatsApp</span>
                    <a href="https://wa.me/234XXXXXXXXXX" target="_blank">Chat on WhatsApp</a>
                  </div>
                </div>

                <div class="method-item d-flex gap-3">
                  <div class="method-icon"><i class="bi bi-pin-map-fill"></i></div>
                  <div class="method-details">
                    <span class="method-label d-block">Location</span>
                    <span>Nigeria</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-7">
            <div class="form-card p-4 h-100">
              <div class="form-card-header d-flex align-items-center gap-3 mb-4">
                <div class="header-icon"><i class="bi bi-send-fill"></i></div>
                <div class="header-text">
                  <h4>Send Us a Message</h4>
                  <p>We typically respond within a few hours.</p>
                </div>
              </div>

              <form action="actions/contact-submit.php" method="post">
                <div class="row g-4">
                  <div class="col-md-6">
                    <div class="input-group-custom">
                      <label>Your Name</label>
                      <div class="input-wrapper d-flex align-items-center px-3">
                        <i class="bi bi-person me-2"></i>
                        <input type="text" name="name" class="w-100 border-0 bg-transparent py-3" required>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="input-group-custom">
                      <label>Email</label>
                      <div class="input-wrapper d-flex align-items-center px-3">
                        <i class="bi bi-envelope me-2"></i>
                        <input type="email" name="email" class="w-100 border-0 bg-transparent py-3" required>
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="input-group-custom">
                      <label>Subject</label>
                      <div class="input-wrapper d-flex align-items-center px-3">
                        <i class="bi bi-chat-square-text me-2"></i>
                        <input type="text" name="subject" class="w-100 border-0 bg-transparent py-3" required>
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="input-group-custom">
                      <label>Message</label>
                      <div class="input-wrapper textarea-wrapper d-flex px-3">
                        <i class="bi bi-pencil-square me-2 mt-3"></i>
                        <textarea name="message" rows="5" class="w-100 border-0 bg-transparent py-3" required></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-actions mt-4">
                  <button type="submit" class="btn-submit px-4 py-3">
                    Send Message <i class="bi bi-arrow-right-circle-fill"></i>
                  </button>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <footer id="footer" class="footer light-background">
    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-info">
          <a href="index.php" class="logo d-flex align-items-center mb-4">
            <span class="sitename">FishNation</span>
          </a>
          <p>
            FishNation supplies healthy fingerlings, dependable delivery, and farmer-first support built for profitable aquaculture.
          </p>

          <div class="social-links d-flex mt-4">
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-whatsapp"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-6 footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="#hero">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#services">Fingerlings</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 footer-links">
          <h4>Customer</h4>
          <ul>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="checkout.php">Cart</a></li>
            <li><a href="payment.php">Payment</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="footer-newsletter p-4">
            <h4>Get Stock Updates</h4>
            <p>Be the first to know when fresh fingerlings and new batches are available.</p>
            <form action="#" method="post">
              <div class="position-relative">
                <input type="email" name="email" placeholder="Your Email" required class="w-100 py-3 px-3 rounded-pill">
                <button type="submit" class="btn-subscribe position-absolute top-0 end-0 h-100 px-4 rounded-pill border-0">
                  <i class="bi bi-arrow-right"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="container footer-bottom mt-4">
      <div class="row gy-3">
        <div class="col-md-6">
          <div class="copyright">
            <p>© <strong class="sitename">FishNation</strong>. All Rights Reserved.</p>
          </div>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="legal-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms</a>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS -->
  <script src="assets/js/main.js"></script>

  <!-- Custom Sliders -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      AOS.init();

      new Swiper(".gallery-slider", {
        slidesPerView: 1,
        spaceBetween: 25,
        loop: true,
        autoplay: {
          delay: 4000,
          disableOnInteraction: false,
        },
        pagination: {
          el: ".gallery-slider .swiper-pagination",
          clickable: true,
        },
        navigation: {
          nextEl: ".gallery-slider .swiper-button-next",
          prevEl: ".gallery-slider .swiper-button-prev",
        },
        breakpoints: {
          768: {
            slidesPerView: 2
          },
          1200: {
            slidesPerView: 3
          }
        }
      });

      new Swiper(".testimonial-slider", {
        slidesPerView: 1,
        spaceBetween: 25,
        loop: true,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        pagination: {
          el: ".testimonial-slider .swiper-pagination",
          clickable: true,
        },
        breakpoints: {
          768: {
            slidesPerView: 2
          }
        }
      });

      const lightbox = GLightbox({
        selector: '.glightbox'
      });
    });
  </script>

</body>
</html>