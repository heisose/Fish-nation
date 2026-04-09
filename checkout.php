<?php
include('config/db.php');

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0;
$cart_count = 0;

foreach ($cart as $item) {
    $item_price = $item['price_min'];
    $subtotal += ($item_price * $item['quantity']);
    $cart_count += $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - FishNation</title>
  <meta name="description" content="Review your FishNation cart and proceed to payment">

  <link href="assets/img/fish.jpeg" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    :root {
      --fish-primary:   #0b3d2e;
      --fish-secondary: #14532d;
      --fish-accent:    #1f7a4d;
      --fish-light:     #e8f5e9;
      --fish-bg:        #f4fbf6;
      --fish-text:      #163020;
      --fish-gold:      #d4af37;
      --fish-muted:     #6b7280;
      --white:          #ffffff;
    }

    body {
      background: linear-gradient(180deg, #f4fbf6 0%, #edf7ef 100%);
      color: var(--fish-text);
      font-family: 'Roboto', sans-serif;
    }

    h1,h2,h3,h4,h5,h6,.sitename,.section-title h2 {
      font-family: 'Raleway', sans-serif;
      color: var(--fish-primary);
      font-weight: 700;
    }

    .page-header {
      background: linear-gradient(135deg, rgba(11,61,46,0.95), rgba(20,83,45,0.88));
      color: #fff;
      padding: 100px 0 60px;
    }
    .page-header h1,
    .page-header p { color: #fff; }

    /* ── Cart pill (nav) ── */
    .cart-pill {
      background: var(--fish-gold);
      color: var(--fish-primary);
      padding: 4px 10px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 13px;
      margin-left: 6px;
      transition: transform 0.2s;
    }
    .cart-pill.bump { animation: cartBump 0.35s ease; }
    @keyframes cartBump {
      0%   { transform: scale(1); }
      50%  { transform: scale(1.35); }
      100% { transform: scale(1); }
    }

    /* ── Cards ── */
    .checkout-card {
      background: #fff;
      border-radius: 20px;
      padding: 24px;
      box-shadow: 0 8px 28px rgba(11,61,46,0.08);
      border: 1px solid rgba(20,83,45,0.08);
      margin-bottom: 20px;
    }

    /* ════════════════════════════════════════
       CART ITEM — mobile-first layout
       ════════════════════════════════════════ */
    .cart-item {
      display: grid;
      /* [image] [details+controls] */
      grid-template-columns: 88px 1fr;
      gap: 14px;
      padding: 16px 0;
      border-bottom: 1px solid #edf2ee;
      transition: opacity 0.3s, transform 0.3s;
    }
    .cart-item:last-child { border-bottom: none; }
    .cart-item.removing  { opacity: 0; transform: translateX(30px); pointer-events: none; }

    /* Thumbnail */
    .cart-img {
      width: 88px;
      height: 88px;
      object-fit: cover;
      border-radius: 14px;
      background: #eef8f1;
      flex-shrink: 0;
    }

    /* Right column */
    .cart-body { display: flex; flex-direction: column; gap: 6px; min-width: 0; }

    .fish-title {
      font-size: 1rem;
      font-weight: 700;
      color: var(--fish-primary);
      line-height: 1.3;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .fish-meta {
      font-size: 12px;
      color: #4b5563;
      line-height: 1.4;
    }

    /* Price + qty + remove row */
    .cart-controls {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 4px;
    }

    .price-tag {
      font-weight: 800;
      color: var(--fish-secondary);
      font-size: 0.95rem;
      white-space: nowrap;
    }

    .item-total-label {
      font-size: 11px;
      color: var(--fish-muted);
      margin-top: 1px;
    }

    .qty-input {
      width: 72px;
      border-radius: 10px;
      border: 1px solid #d1d5db;
      padding: 8px 6px;
      text-align: center;
      font-weight: 700;
      font-size: 0.9rem;
      transition: border-color 0.2s;
    }
    .qty-input:focus {
      border-color: var(--fish-accent);
      outline: none;
      box-shadow: 0 0 0 3px rgba(31,122,77,0.12);
    }
    .qty-input.saving { border-color: var(--fish-gold); background: #fffbf0; }

    .btn-danger-soft {
      background: rgba(220,38,38,0.08);
      color: #b91c1c;
      border: none;
      padding: 8px 11px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 13px;
      cursor: pointer;
      transition: background 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      white-space: nowrap;
    }
    .btn-danger-soft:hover    { background: rgba(220,38,38,0.14); }
    .btn-danger-soft:disabled { opacity: 0.5; pointer-events: none; }

    /* ── Desktop: wider image + side-by-side price/qty ── */
    @media (min-width: 640px) {
      .cart-item { grid-template-columns: 110px 1fr; gap: 18px; }
      .cart-img  { width: 110px; height: 110px; }
    }

    /* ── Summary box ── */
    .summary-box {
      background: linear-gradient(180deg, #fff 0%, #f6fcf8 100%);
      border-radius: 20px;
      padding: 24px;
      box-shadow: 0 8px 28px rgba(11,61,46,0.08);
      border: 1px solid rgba(20,83,45,0.08);
      position: sticky;
      top: 80px;
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 14px;
      font-size: 15px;
    }
    .summary-total {
      font-size: 1.3rem;
      font-weight: 800;
      color: var(--fish-primary);
      border-top: 1px solid #e5e7eb;
      padding-top: 16px;
      margin-top: 16px;
    }

    /* ── Buttons ── */
    .btn-fishnation {
      display: inline-block;
      background: linear-gradient(135deg, var(--fish-primary), var(--fish-secondary));
      color: #fff;
      border: none;
      padding: 13px 18px;
      border-radius: 14px;
      font-weight: 700;
      transition: 0.3s;
      text-align: center;
      text-decoration: none;
      cursor: pointer;
    }
    .btn-fishnation:hover {
      background: linear-gradient(135deg, var(--fish-secondary), var(--fish-accent));
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 12px 24px rgba(11,61,46,0.18);
    }
    .btn-fishnation.loading { opacity: 0.7; pointer-events: none; }

    .btn-outline-fish {
      display: inline-block;
      background: transparent;
      color: var(--fish-primary);
      border: 2px solid var(--fish-primary);
      padding: 10px 16px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 14px;
      transition: 0.3s;
      text-decoration: none;
    }
    .btn-outline-fish:hover { background: var(--fish-primary); color: #fff; }

    /* ── Empty state ── */
    .empty-box {
      background: #fff;
      border-radius: 20px;
      padding: 50px 24px;
      text-align: center;
      box-shadow: 0 8px 28px rgba(11,61,46,0.08);
    }
    .empty-box i { font-size: 48px; color: var(--fish-accent); margin-bottom: 16px; }

    /* ── Toast ── */
    #cart-toast {
      position: fixed;
      bottom: 24px;
      right: 16px;
      left: 16px;
      max-width: 360px;
      margin: 0 auto;
      background: var(--fish-primary);
      color: #fff;
      padding: 13px 20px;
      border-radius: 14px;
      font-size: 14px;
      font-weight: 600;
      box-shadow: 0 8px 24px rgba(0,0,0,0.18);
      z-index: 9999;
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.3s, transform 0.3s;
      pointer-events: none;
      text-align: center;
    }
    #cart-toast.show  { opacity: 1; transform: translateY(0); }
    #cart-toast.error { background: #b91c1c; }

    @media (min-width: 480px) {
      #cart-toast { left: auto; right: 24px; text-align: left; }
    }

    /* ── Footer ── */
    .footer { background: #08291f !important; color: rgba(255,255,255,0.85); }
    .footer .sitename,.footer h4,.footer a,.footer p,.footer span { color: rgba(255,255,255,0.9) !important; }
    .footer a:hover { color: var(--fish-gold) !important; }
  </style>
</head>

<body class="index-page">

  <?php
    $current_page = 'checkout';
    include 'navbar.php';
  ?>

  <main class="main">

    <!-- Hero -->
    <section class="page-header">
      <div class="container text-center" data-aos="fade-up">
        <h1 class="display-5 fw-bold">Your Checkout</h1>
        <p class="mt-2 fs-5">Review your selected fingerlings before payment.</p>
      </div>
    </section>

    <!-- Checkout Section -->
    <section class="section py-4 py-md-5">
      <div class="container">
        <div class="row g-4">

          <!-- LEFT: CART ITEMS -->
          <div class="col-lg-8">

            <?php if (!empty($cart)): ?>
              <div class="checkout-card" data-aos="fade-up">
                <h3 class="mb-4">
                  Cart Items
                  <span style="font-size:14px;font-weight:500;color:var(--fish-muted);margin-left:8px;">
                    (<?= $cart_count ?> item<?= $cart_count != 1 ? 's' : '' ?>)
                  </span>
                </h3>

                <div id="cart-items-container">
                  <?php foreach ($cart as $item):
                    $item_price = $item['price_min'];
                    $item_total = $item_price * $item['quantity'];
                  ?>
                  <div class="cart-item" data-id="<?= $item['id'] ?>">

                    <!-- Thumbnail -->
                    <img
                      src="uploads/<?= htmlspecialchars($item['image']) ?>"
                      class="cart-img"
                      alt="<?= htmlspecialchars($item['fish_name']) ?>"
                    >

                    <!-- Details -->
                    <div class="cart-body">
                      <div class="fish-title"><?= htmlspecialchars($item['fish_name']) ?></div>

                      <div class="fish-meta">
                        <?= htmlspecialchars($item['species']) ?>
                        &nbsp;·&nbsp;
                        <?= htmlspecialchars($item['size_category']) ?>
                        &nbsp;·&nbsp;
                        <?= htmlspecialchars($item['weight_range']) ?>
                      </div>

                      <!-- Unit price -->
                      <div class="price-tag" data-unit-price="<?= $item_price ?>">
                        ₦<?= number_format($item_price) ?> <span style="font-weight:400;font-size:12px;color:var(--fish-muted)">/ unit</span>
                      </div>

                      <!-- Controls: qty + total + remove -->
                      <div class="cart-controls">
                        <div>
                          <input
                            type="number"
                            class="form-control qty-input"
                            data-id="<?= $item['id'] ?>"
                            value="<?= $item['quantity'] ?>"
                            min="1"
                            max="9999"
                          >
                        </div>

                        <div>
                          <div class="item-total-label">Subtotal</div>
                          <div class="price-tag item-total">₦<?= number_format($item_total) ?></div>
                        </div>

                        <button
                          class="btn-danger-soft remove-btn ms-auto"
                          data-id="<?= $item['id'] ?>"
                          data-name="<?= htmlspecialchars($item['fish_name']) ?>"
                          title="Remove"
                        >
                          <i class="bi bi-trash"></i>
                          <span class="d-none d-sm-inline">Remove</span>
                        </button>
                      </div>
                    </div>

                  </div>
                  <?php endforeach; ?>
                </div><!-- #cart-items-container -->

                <div class="mt-4 d-flex flex-wrap gap-2" id="cart-actions-row">
                  <a href="shop.php" class="btn-outline-fish">
                    <i class="bi bi-bag-plus"></i> Add More Fish
                  </a>
                  <button class="btn-danger-soft" id="clear-cart-btn">
                    <i class="bi bi-trash3"></i> Clear Cart
                  </button>
                </div>
              </div>

            <?php else: ?>
              <div class="empty-box" id="empty-cart-box" data-aos="fade-up">
                <i class="bi bi-cart-x d-block"></i>
                <h3>Your Cart is Empty</h3>
                <p class="text-muted">You haven't added any fingerlings yet.</p>
                <a href="shop.php" class="btn-fishnation mt-3">Go to Shop</a>
              </div>
            <?php endif; ?>

          </div>

          <!-- RIGHT: ORDER SUMMARY -->
          <div class="col-lg-4">
            <div class="summary-box" data-aos="fade-left">
              <h3 class="mb-4">Order Summary</h3>

              <div class="summary-row">
                <span>Items</span>
                <span id="summary-count"><?= $cart_count ?></span>
              </div>
              <div class="summary-row">
                <span>Subtotal</span>
                <span id="summary-subtotal">₦<?= number_format($subtotal) ?></span>
              </div>
              <div class="summary-row">
                <span>Delivery</span>
                <span style="color:var(--fish-muted);font-size:13px;">Calculated at payment</span>
              </div>

              <div class="summary-row summary-total">
                <span>Total</span>
                <span id="summary-total">₦<?= number_format($subtotal) ?></span>
              </div>

              <?php if (!empty($cart)): ?>
                <a href="payment.php" class="btn-fishnation w-100 mt-4" id="checkout-btn">
                  <i class="bi bi-credit-card-2-front-fill"></i> Proceed to Payment
                </a>
              <?php else: ?>
                <a href="shop.php" class="btn-fishnation w-100 mt-4" id="checkout-btn">
                  <i class="bi bi-bag-fill"></i> Shop Now
                </a>
              <?php endif; ?>

              <p class="mt-3 text-muted small text-center">
                <i class="bi bi-shield-check text-success"></i>
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
          <a href="index.php" class="logo d-flex align-items-center mb-3">
            <span class="sitename">FishNation</span>
          </a>
          <p>Premium fingerlings for serious fish farmers. Healthy stock, dependable supply, and trusted delivery.</p>
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
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="container footer-bottom text-center">
      <p>© <?= date("Y") ?> <strong class="sitename">FishNation</strong>. All Rights Reserved.</p>
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

  <script>
    const cartCountEl = document.getElementById('cart-count');
    const toast       = document.getElementById('cart-toast');
    let toastTimer    = null;

    function showToast(msg, isError = false) {
      toast.textContent = msg;
      toast.classList.toggle('error', isError);
      toast.classList.add('show');
      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => toast.classList.remove('show'), 3000);
    }

    function bumpCartPill(count) {
      if (!cartCountEl) return;
      cartCountEl.textContent = count;
      cartCountEl.classList.remove('bump');
      void cartCountEl.offsetWidth;
      cartCountEl.classList.add('bump');
    }

    function formatNaira(n) {
      return '₦' + Math.round(n).toLocaleString('en-NG');
    }

    function recalculateSummary() {
      const items = document.querySelectorAll('.cart-item:not(.removing)');
      let subtotal = 0, totalItems = 0;

      items.forEach(item => {
        const unitPrice = parseFloat(item.querySelector('.price-tag[data-unit-price]').dataset.unitPrice);
        const qty       = parseInt(item.querySelector('.qty-input').value) || 1;
        const line      = unitPrice * qty;
        item.querySelector('.item-total').textContent = formatNaira(line);
        subtotal   += line;
        totalItems += qty;
      });

      document.getElementById('summary-count').textContent    = totalItems;
      document.getElementById('summary-subtotal').textContent = formatNaira(subtotal);
      document.getElementById('summary-total').textContent    = formatNaira(subtotal);
      bumpCartPill(totalItems);
    }

    function checkIfEmpty() {
      if (document.querySelectorAll('.cart-item').length === 0) {
        document.querySelector('.checkout-card').innerHTML = `
          <div class="empty-box">
            <i class="bi bi-cart-x d-block" style="font-size:48px;color:var(--fish-accent);margin-bottom:16px;"></i>
            <h3>Your Cart is Empty</h3>
            <p class="text-muted">Browse available stock and start shopping.</p>
            <a href="shop.php" class="btn-fishnation mt-3">Go to Shop</a>
          </div>`;
        document.getElementById('summary-count').textContent    = '0';
        document.getElementById('summary-subtotal').textContent = '₦0';
        document.getElementById('summary-total').textContent    = '₦0';
        bumpCartPill(0);
        const btn = document.getElementById('checkout-btn');
        if (btn) { btn.href = 'shop.php'; btn.innerHTML = '<i class="bi bi-bag-fill"></i> Shop Now'; }
      }
    }

    async function cartPost(formData) {
      const r = await fetch('cart-actions.php', {
        method: 'POST', body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      return r.json();
    }

    // Remove item
    document.querySelectorAll('.remove-btn').forEach(btn => {
      btn.addEventListener('click', async function () {
        const id = this.dataset.id, name = this.dataset.name;
        const itemEl = this.closest('.cart-item');
        this.disabled = true;
        const fd = new FormData();
        fd.append('action', 'remove');
        fd.append('product_id', id);
        try {
          const data = await cartPost(fd);
          if (data.success) {
            itemEl.classList.add('removing');
            setTimeout(() => { itemEl.remove(); recalculateSummary(); checkIfEmpty(); }, 300);
            showToast(`${name} removed.`);
          } else {
            showToast(data.message || 'Could not remove item.', true);
            this.disabled = false;
          }
        } catch { showToast('Something went wrong.', true); this.disabled = false; }
      });
    });

    // Update quantity
    let qtyTimer = null;
    document.querySelectorAll('.qty-input').forEach(input => {
      input.addEventListener('change', function () {
        const id = this.dataset.id;
        let qty = parseInt(this.value);
        if (isNaN(qty) || qty < 1) { qty = 1; this.value = 1; }
        this.classList.add('saving');
        clearTimeout(qtyTimer);
        qtyTimer = setTimeout(async () => {
          const fd = new FormData();
          fd.append('action', 'update');
          fd.append(`quantities[${id}]`, qty);
          try {
            const data = await cartPost(fd);
            if (data.success) { recalculateSummary(); showToast('Quantity updated.'); }
            else showToast(data.message || 'Could not update.', true);
          } catch { showToast('Something went wrong.', true); }
          finally { input.classList.remove('saving'); }
        }, 500);
      });
    });

    // Clear cart
    const clearBtn = document.getElementById('clear-cart-btn');
    if (clearBtn) {
      clearBtn.addEventListener('click', async function () {
        if (!confirm('Clear your entire cart?')) return;
        this.classList.add('loading');
        this.textContent = 'Clearing…';
        const fd = new FormData();
        fd.append('action', 'clear');
        try {
          const data = await cartPost(fd);
          if (data.success) {
            document.querySelectorAll('.cart-item').forEach(i => { i.classList.add('removing'); setTimeout(() => i.remove(), 300); });
            setTimeout(checkIfEmpty, 350);
            showToast('Cart cleared.');
          } else {
            showToast(data.message || 'Could not clear cart.', true);
            this.classList.remove('loading');
            this.innerHTML = '<i class="bi bi-trash3"></i> Clear Cart';
          }
        } catch {
          showToast('Something went wrong.', true);
          this.classList.remove('loading');
          this.innerHTML = '<i class="bi bi-trash3"></i> Clear Cart';
        }
      });
    }
  </script>

</body>
</html>
