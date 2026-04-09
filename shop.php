<?php
include('config/db.php');

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop - FishNation</title>
  <meta name="description" content="Browse healthy fingerlings available at FishNation">
  <meta name="keywords" content="FishNation, fingerlings, catfish, juvenile fish, fingerlings">

  <link href="assets/img/fish.jpeg" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

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
      background: linear-gradient(180deg, #45A80D 0%, #45A80D 100%);
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
      background: rgba(10, 61, 45, 0.95) !important;
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

    .mobile-nav-toggle { color: #fff !important; }

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
      background: linear-gradient(135deg, rgba(11,61,46,0.95), rgba(20,83,45,0.88)),
                  url('assets/img/hero-bg.jpg') center/cover no-repeat;
      color: #fff;
      padding: 130px 0 90px;
      position: relative;
      overflow: hidden;
    }

    .page-header::after {
      content: "";
      position: absolute;
      width: 300px; height: 300px;
      background: rgba(255,255,255,0.05);
      border-radius: 50%;
      top: -50px; right: -50px;
    }

    .page-header h1,
    .page-header p { color: #fff; position: relative; z-index: 2; }

    .section-title p { color: var(--fish-muted); }

    .shop-card {
      background: var(--white);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 12px 35px rgba(11, 61, 46, 0.08);
      transition: all 0.35s ease;
      height: 100%;
      border: 1px solid rgba(20, 83, 45, 0.08);
    }

    .shop-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 45px rgba(11, 61, 46, 0.16);
    }

    .shop-image-wrap {
      height: 250px; overflow: hidden;
      background: #eef8f1;
      display: flex; align-items: center; justify-content: center;
    }

    .shop-image-wrap img {
      width: 100%; height: 100%;
      object-fit: cover; transition: 0.4s ease;
    }

    .shop-card:hover .shop-image-wrap img { transform: scale(1.06); }

    .shop-content { padding: 24px; }

    .fish-badge {
      display: inline-block;
      background: rgba(31, 122, 77, 0.1);
      color: var(--fish-accent);
      padding: 6px 14px; border-radius: 999px;
      font-size: 13px; font-weight: 700; margin-bottom: 14px;
    }

    .shop-content h4 { font-size: 1.35rem; margin-bottom: 12px; color: var(--fish-primary); }

    .fish-meta { margin-bottom: 16px; }
    .fish-meta p { margin-bottom: 8px; font-size: 15px; color: #374151; }

    .price-tag {
      font-size: 1.15rem; font-weight: 800;
      color: var(--fish-secondary); margin-bottom: 14px;
    }

    .status-badge {
      display: inline-block; padding: 8px 14px;
      border-radius: 999px; font-size: 13px; font-weight: 700; margin-bottom: 18px;
    }

    .status-in  { background: rgba(34,197,94,0.14);  color: #15803d; }
    .status-low { background: rgba(245,158,11,0.16); color: #b45309; }
    .status-out { background: rgba(239,68,68,0.14);  color: #b91c1c; }

    .btn-fishnation {
      display: inline-block; width: 100%;
      background: linear-gradient(135deg, var(--fish-primary), var(--fish-secondary));
      color: #fff; border: none;
      padding: 13px 18px; border-radius: 14px;
      font-weight: 700; transition: 0.3s ease;
      text-align: center; text-decoration: none;
      cursor: pointer;
    }

    .btn-fishnation:hover {
      background: linear-gradient(135deg, var(--fish-secondary), var(--fish-accent));
      color: #fff; transform: translateY(-2px);
      box-shadow: 0 12px 24px rgba(11, 61, 46, 0.18);
    }

    /* Loading spinner state on button */
    .btn-fishnation.loading {
      opacity: 0.7;
      pointer-events: none;
    }

    .cart-pill {
      background: var(--fish-gold);
      color: var(--fish-primary);
      padding: 8px 14px; border-radius: 999px;
      font-weight: 700; margin-left: 10px; font-size: 14px;
      transition: transform 0.2s ease;
    }

    /* Bounce animation when cart updates */
    .cart-pill.bump {
      animation: cartBump 0.35s ease;
    }

    @keyframes cartBump {
      0%   { transform: scale(1); }
      50%  { transform: scale(1.35); }
      100% { transform: scale(1); }
    }

    .empty-box {
      background: #fff; border-radius: 24px;
      padding: 60px 30px; text-align: center;
      box-shadow: 0 12px 35px rgba(11, 61, 46, 0.08);
    }

    .empty-box i { font-size: 50px; color: var(--fish-accent); margin-bottom: 20px; }

    /* Toast notification */
    #cart-toast {
      position: fixed;
      bottom: 30px; right: 30px;
      background: var(--fish-primary);
      color: #fff;
      padding: 14px 22px;
      border-radius: 14px;
      font-size: 15px; font-weight: 600;
      box-shadow: 0 8px 24px rgba(0,0,0,0.18);
      z-index: 9999;
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.3s ease, transform 0.3s ease;
      pointer-events: none;
    }

    #cart-toast.show {
      opacity: 1;
      transform: translateY(0);
    }

    #cart-toast.error {
      background: #b91c1c;
    }

    .footer { background: #08291f !important; color: rgba(255,255,255,0.85); }
    .footer .sitename, .footer h4, .footer a, .footer p, .footer span { color: rgba(255,255,255,0.9) !important; }
    .footer a:hover { color: var(--fish-gold) !important; }
  </style>
</head>

<body class="index-page">

  <!-- Header -->
  <?php
$current_page = 'shop';
$cart_count = /* your cart count query */
include 'navbar.php';
?>

  <main class="main">

    <!-- Hero -->
    <section class="page-header">
      <div class="container text-center" data-aos="fade-up">
        <h1 class="display-5 fw-bold">Available fingerlings</h1>
        <p class="mt-3 fs-5">
          Browse healthy, disease-free fingerlings ready for stocking and delivery.
        </p>
      </div>
    </section>

    <!-- Shop Section -->
    <section class="section py-5">
      <div class="container">

        <div class="section-title text-center mb-5" data-aos="fade-up">
          <h2>FishNation Shop</h2>
          <p>Choose from our carefully raised and quality-checked fish stock.</p>
        </div>

        <div class="row g-4">

          <?php if($products && $products->num_rows > 0): ?>
            <?php while($row = $products->fetch_assoc()): ?>

              <?php
                $statusClass = 'status-in';
                if (stripos($row['stock_status'], 'low') !== false) $statusClass = 'status-low';
                elseif (stripos($row['stock_status'], 'out') !== false) $statusClass = 'status-out';
              ?>

              <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="shop-card">

                  <div class="shop-image-wrap">
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['fish_name']); ?>">
                  </div>

                  <div class="shop-content">
                    <span class="fish-badge">
                      <i class="bi bi-droplet-fill"></i> Premium Stock
                    </span>

                    <h4><?php echo htmlspecialchars($row['fish_name']); ?></h4>

                    <div class="fish-meta">
                      <p><strong>Species:</strong> <?php echo htmlspecialchars($row['species']); ?></p>
                      <p><strong>Size:</strong> <?php echo htmlspecialchars($row['size_category']); ?></p>
                      <p><strong>Weight:</strong> <?php echo htmlspecialchars($row['weight_range']); ?></p>
                    </div>

                    <div class="price-tag">
                      ₦<?php echo number_format($row['price_min']); ?> - ₦<?php echo number_format($row['price_max']); ?>
                    </div>

                    <span class="status-badge <?php echo $statusClass; ?>">
                      <?php echo htmlspecialchars($row['stock_status']); ?>
                    </span>

                    <!-- ✅ Replaced <form> with a single button using data attributes -->
                    <button
                      class="btn-fishnation add-to-cart-btn"
                      data-product-id="<?php echo $row['id']; ?>"
                      data-fish-name="<?php echo htmlspecialchars($row['fish_name']); ?>"
                    >
                      <i class="bi bi-cart-plus-fill"></i> Add to Cart
                    </button>

                  </div>
                </div>
              </div>

            <?php endwhile; ?>
          <?php else: ?>
            <div class="col-12">
              <div class="empty-box" data-aos="fade-up">
                <i class="bi bi-basket"></i>
                <h3>No Fish Available Yet</h3>
                <p class="text-muted">Products will appear here once they are added from the admin dashboard.</p>
                <a href="index.php" class="btn-fishnation mt-3" style="max-width: 260px;">Back to Home</a>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </section>

  </main>

  <!-- Footer (unchanged) -->
  <footer id="footer" class="footer light-background">
    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-info">
          <a href="index.php" class="logo d-flex align-items-center mb-4">
            <img style="border-radius:50%" src="assets/img/fish.jpeg" width="10%" height="40vh" alt="">
            <span class="sitename">FishNation</span>
          </a>
          <p>FishNation supplies healthy, disease-free fingerlings to help fish farmers reduce mortality and increase profit.</p>
          <div class="social-links d-flex mt-4">
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-whatsapp"></i></a>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#about">About</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="checkout.php">Checkout</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-6 footer-links">
          <h4>Customer Support</h4>
          <ul>
            <li><a href="index.php#contact">Contact Us</a></li>
            <li><a href="payment.php">Payment Info</a></li>
            <li><a href="checkout.php">Track Cart</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="footer-newsletter">
            <h4>Stay Updated</h4>
            <p>Get updates on new stock and fish availability.</p>
            <form action="#" method="post">
              <div class="position-relative">
                <input type="email" name="email" placeholder="Your Email" required class="form-control">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="container footer-bottom">
      <div class="text-center">
        <p>© <?php echo date("Y"); ?> <strong class="sitename">FishNation</strong>. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <a href="#" style="background-color:green" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i  class="bi bi-arrow-up-short "></i>
  </a>
  <div id="preloader"></div>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <!-- ✅ AJAX Cart Logic -->
  <script>
    const cartCountEl = document.getElementById('cart-count');
    const toast       = document.getElementById('cart-toast');
    let toastTimer    = null;

    function showToast(message, isError = false) {
      toast.textContent = message;
      toast.classList.toggle('error', isError);
      toast.classList.add('show');

      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => toast.classList.remove('show'), 3000);
    }

    function bumpCartPill(newCount) {
      cartCountEl.textContent = newCount;
      cartCountEl.classList.remove('bump');
      // Force reflow so animation re-triggers even on consecutive clicks
      void cartCountEl.offsetWidth;
      cartCountEl.classList.add('bump');
    }

    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
      btn.addEventListener('click', async function () {
        const productId = this.dataset.productId;
        const fishName  = this.dataset.fishName;
        const originalHTML = this.innerHTML;

        // Show loading state
        this.classList.add('loading');
        this.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

        try {
          const formData = new FormData();
          formData.append('product_id', productId);
          formData.append('action', 'add');

          const response = await fetch('cart-actions.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }  // ← add this line
            });

          // cart-actions.php should return JSON: { success: true, cart_count: 5 }
          const data = await response.json();

          if (data.success) {
            bumpCartPill(data.cart_count);
            showToast(`✓ ${fishName} added to cart!`);
          } else {
            showToast(data.message || 'Could not add item.', true);
          }

        } catch (err) {
          showToast('Something went wrong. Please try again.', true);
          console.error('Cart AJAX error:', err);
        } finally {
          // Restore button
          this.classList.remove('loading');
          this.innerHTML = originalHTML;
        }
      });
    });
  </script>

</body>
</html>