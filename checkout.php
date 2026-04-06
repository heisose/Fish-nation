<?php
include('config/db.php');

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0;
$cart_count = 0;

foreach ($cart as $item) {
    $item_price = $item['price_min']; // using minimum price as checkout price
    $subtotal += ($item_price * $item['quantity']);
    $cart_count += $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Checkout - FishNation</title>
  <meta name="description" content="Review your FishNation cart and proceed to payment">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    :root {
      --fish-primary: #0b3d2e;
      --fish-secondary: #14532d;
      --fish-accent: #1f7a4d;
      --fish-light: #e8f5e9;
      --fish-bg: #f4fbf6;
      --fish-text: #163020;
      --fish-gold: #d4af37;
      --fish-muted: #6b7280;
      --white: #ffffff;
    }

    body {
      background: linear-gradient(180deg, #f4fbf6 0%, #edf7ef 100%);
      color: var(--fish-text);
      font-family: 'Roboto', sans-serif;
    }

    h1, h2, h3, h4, h5, h6,
    .sitename,
    .section-title h2 {
      font-family: 'Raleway', sans-serif;
      color: var(--fish-primary);
      font-weight: 700;
    }

    .header {
      background: rgba(11, 61, 46, 0.95) !important;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .header .logo h1,
    .header .logo span,
    .navmenu a,
    .navmenu .active,
    .navmenu li:hover > a {
      color: #fff !important;
    }

    .mobile-nav-toggle {
      color: #fff !important;
    }

    .btn-getstarted {
      background: var(--fish-gold) !important;
      color: var(--fish-primary) !important;
      border-radius: 999px;
      padding: 12px 22px !important;
      font-weight: 700;
      border: none;
      transition: 0.3s ease;
      text-decoration: none;
    }

    .btn-getstarted:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
    }

    .page-header {
      background: linear-gradient(135deg, rgba(11,61,46,0.95), rgba(20,83,45,0.88));
      color: #fff;
      padding: 120px 0 80px;
    }

    .page-header h1,
    .page-header p {
      color: #fff;
    }

    .cart-pill {
      background: var(--fish-gold);
      color: var(--fish-primary);
      padding: 8px 14px;
      border-radius: 999px;
      font-weight: 700;
      margin-left: 10px;
      font-size: 14px;
    }

    .checkout-card {
      background: #fff;
      border-radius: 24px;
      padding: 28px;
      box-shadow: 0 12px 35px rgba(11, 61, 46, 0.08);
      border: 1px solid rgba(20, 83, 45, 0.08);
      margin-bottom: 25px;
    }

    .cart-item {
      border-bottom: 1px solid #edf2ee;
      padding: 20px 0;
    }

    .cart-item:last-child {
      border-bottom: none;
    }

    .cart-img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 16px;
      background: #eef8f1;
    }

    .fish-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--fish-primary);
      margin-bottom: 8px;
    }

    .fish-meta {
      font-size: 14px;
      color: #4b5563;
      margin-bottom: 4px;
    }

    .price-tag {
      font-weight: 800;
      color: var(--fish-secondary);
      font-size: 1.05rem;
    }

    .qty-input {
      width: 90px;
      border-radius: 12px;
      border: 1px solid #d1d5db;
      padding: 10px;
      text-align: center;
      font-weight: 700;
    }

    .btn-fishnation {
      display: inline-block;
      background: linear-gradient(135deg, var(--fish-primary), var(--fish-secondary));
      color: #fff;
      border: none;
      padding: 13px 18px;
      border-radius: 14px;
      font-weight: 700;
      transition: 0.3s ease;
      text-align: center;
      text-decoration: none;
    }

    .btn-fishnation:hover {
      background: linear-gradient(135deg, var(--fish-secondary), var(--fish-accent));
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 12px 24px rgba(11, 61, 46, 0.18);
    }

    .btn-outline-fish {
      display: inline-block;
      background: transparent;
      color: var(--fish-primary);
      border: 2px solid var(--fish-primary);
      padding: 12px 18px;
      border-radius: 14px;
      font-weight: 700;
      transition: 0.3s ease;
      text-decoration: none;
    }

    .btn-outline-fish:hover {
      background: var(--fish-primary);
      color: #fff;
    }

    .btn-danger-soft {
      background: rgba(220, 38, 38, 0.08);
      color: #b91c1c;
      border: none;
      padding: 10px 14px;
      border-radius: 12px;
      font-weight: 700;
    }

    .btn-danger-soft:hover {
      background: rgba(220, 38, 38, 0.14);
    }

    .summary-box {
      background: linear-gradient(180deg, #ffffff 0%, #f6fcf8 100%);
      border-radius: 24px;
      padding: 28px;
      box-shadow: 0 12px 35px rgba(11, 61, 46, 0.08);
      border: 1px solid rgba(20, 83, 45, 0.08);
      position: sticky;
      top: 110px;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 16px;
      font-size: 16px;
    }

    .summary-total {
      font-size: 1.4rem;
      font-weight: 800;
      color: var(--fish-primary);
      border-top: 1px solid #e5e7eb;
      padding-top: 18px;
      margin-top: 18px;
    }

    .empty-box {
      background: #fff;
      border-radius: 24px;
      padding: 60px 30px;
      text-align: center;
      box-shadow: 0 12px 35px rgba(11, 61, 46, 0.08);
    }

    .empty-box i {
      font-size: 50px;
      color: var(--fish-accent);
      margin-bottom: 20px;
    }

    .footer {
      background: #08291f !important;
      color: rgba(255,255,255,0.85);
    }

    .footer .sitename,
    .footer h4,
    .footer a,
    .footer p,
    .footer span {
      color: rgba(255,255,255,0.9) !important;
    }

    .footer a:hover {
      color: var(--fish-gold) !important;
    }
  </style>
</head>

<body class="index-page">

  <!-- Header -->
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">
        <img style="border-radius:50%" src="assets/img/fish.jpeg" width="100%" alt="">
         <h1 class="sitename">Fish</h1><span style="color: #45A80D;">Nation</span><span style="color: #7ef29d;">.</span>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="shop.php">Shop</a></li>
          <li><a href="payment.php">Payment</a></li>
          <li><a href="index.php#contact">Contact</a></li>
          <li>
            <a href="checkout.php">
              <i class="bi bi-cart3"></i> Cart
              <span class="cart-pill"><?php echo $cart_count; ?></span>
            </a>
          </li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="shop.php">
        <i class="bi bi-bag-plus-fill"></i> Continue Shopping
      </a>

    </div>
  </header>

  <main class="main">

    <!-- Header Section -->
    <section class="page-header">
      <div class="container text-center" data-aos="fade-up">
        <h1 class="display-5 fw-bold">Your Checkout</h1>
        <p class="mt-3 fs-5">Review your selected fingerlings before payment.</p>
      </div>
    </section>

    <!-- Checkout Section -->
    <section class="section py-5">
      <div class="container">
        <div class="row g-4">

          <!-- LEFT: CART ITEMS -->
          <div class="col-lg-8">

            <?php if (!empty($cart)): ?>
              <div class="checkout-card" data-aos="fade-up">
                <h3 class="mb-4">Cart Items</h3>
                <div class="checkout-card" data-aos="fade-up">
                <h3 class="mb-4">Cart Items</h3>

                <form action="cart-actions.php" method="POST">
                    <input type="hidden" name="action" value="update">

                    <?php foreach ($cart as $item): 
                    $item_price = $item['price_min'];
                    $item_total = $item_price * $item['quantity'];
                    ?>
                    <div class="cart-item">
                        <div class="row align-items-center g-3">

                        <div class="col-md-2">
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="cart-img" alt="<?php echo htmlspecialchars($item['fish_name']); ?>">
                        </div>

                        <div class="col-md-4">
                            <div class="fish-title"><?php echo htmlspecialchars($item['fish_name']); ?></div>
                            <div class="fish-meta"><strong>Species:</strong> <?php echo htmlspecialchars($item['species']); ?></div>
                            <div class="fish-meta"><strong>Size:</strong> <?php echo htmlspecialchars($item['size_category']); ?></div>
                            <div class="fish-meta"><strong>Weight:</strong> <?php echo htmlspecialchars($item['weight_range']); ?></div>
                        </div>

                        <div class="col-md-2">
                            <div class="price-tag">₦<?php echo number_format($item_price); ?></div>
                        </div>

                        <div class="col-md-2">
                            <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="form-control qty-input">
                        </div>

                        <div class="col-md-2 text-md-end">
                            <div class="price-tag mb-2">₦<?php echo number_format($item_total); ?></div>
                            <a href="cart-remove.php?id=<?php echo $item['id']; ?>" class="btn-danger-soft text-decoration-none">
                            <i class="bi bi-trash"></i>
                            </a>
                        </div>

                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="mt-4 d-flex flex-wrap gap-3">
                    <button type="submit" class="btn-fishnation">
                        <i class="bi bi-arrow-repeat"></i> Update Cart
                    </button>
                    <a href="shop.php" class="btn-outline-fish">
                        <i class="bi bi-bag-plus"></i> Add More Fish
                    </a>
                    </div>
                </form>
                </div>
            <?php else: ?>
              <div class="empty-box" data-aos="fade-up">
                <i class="bi bi-cart-x"></i>
                <h3>Your Cart is Empty</h3>
                <p class="text-muted">You haven’t added any fingerlings yet. Browse available stock and start shopping.</p>
                <a href="shop.php" class="btn-fishnation mt-3">
                  Go to Shop
                </a>
              </div>
            <?php endif; ?>

          </div>

          <!-- RIGHT: SUMMARY -->
          <div class="col-lg-4">
            <div class="summary-box" data-aos="fade-left">
              <h3 class="mb-4">Order Summary</h3>

              <div class="summary-row">
                <span>Items</span>
                <span><?php echo $cart_count; ?></span>
              </div>

              <div class="summary-row">
                <span>Subtotal</span>
                <span>₦<?php echo number_format($subtotal); ?></span>
              </div>

              <div class="summary-row">
                <span>Delivery</span>
                <span>Calculated at payment</span>
              </div>

              <div class="summary-row summary-total">
                <span>Total</span>
                <span>₦<?php echo number_format($subtotal); ?></span>
              </div>

              <?php if (!empty($cart)): ?>
                <a href="payment.php" class="btn-fishnation w-100 mt-4">
                  <i class="bi bi-credit-card-2-front-fill"></i> Proceed to Payment
                </a>
              <?php else: ?>
                <a href="shop.php" class="btn-fishnation w-100 mt-4">
                  <i class="bi bi-bag-fill"></i> Shop Now
                </a>
              <?php endif; ?>

              <p class="mt-3 text-muted small">
                Secure checkout. Healthy stock. Reliable delivery.
              </p>
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
            Premium fingerlings for serious fish farmers. Healthy stock, dependable supply, and trusted delivery.
          </p>
        </div>

        <div class="col-lg-2 col-md-6 footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="checkout.php">Checkout</a></li>
            <li><a href="payment.php">Payment</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 footer-links">
          <h4>Support</h4>
          <ul>
            <li><a href="index.php#contact">Contact Us</a></li>
            <li><a href="payment.php">Payment Details</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="container footer-bottom text-center">
      <p>© <?php echo date("Y"); ?> <strong class="sitename">FishNation</strong>. All Rights Reserved.</p>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>
</html>