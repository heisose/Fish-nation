<?php
include('config/db.php');

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal    = 0;
$cart_count  = 0;

foreach ($cart as $item) {
    $item_price  = $item['price_min'];
    $subtotal   += ($item_price * $item['quantity']);
    $cart_count += $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout — FishNation</title>

  <link href="assets/img/fish.jpeg" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Raleway:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    :root {
      --fn-dark:   #07150f;
      --fn-green:  #45A80D;
      --fn-lime:   #7ef29d;
      --fn-card:   rgba(7,21,15,0.85);
      --fn-border: rgba(69,168,13,0.22);
      --fn-muted:  rgba(216,243,220,0.45);
      --fn-text:   #d8f3dc;
      --fn-gold:   #f0c040;
      --fn-red:    #e85555;
      --fn-orange: #f07830;
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      background: #07150f;
      color: var(--fn-text);
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
    }

    h1,h2,h3,h4,h5,h6 {
      font-family: 'Raleway', sans-serif;
      color: #fff;
    }

    /* ── Hero ── */
    .checkout-hero {
      background:
        radial-gradient(ellipse at 20% 50%, rgba(69,168,13,0.15) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(126,242,157,0.07) 0%, transparent 50%),
        linear-gradient(180deg, #0a2e14 0%, #07150f 100%);
      padding: 70px 0 44px;
      text-align: center;
      border-bottom: 1px solid var(--fn-border);
    }
    .checkout-hero .eyebrow {
      display: inline-block;
      background: rgba(69,168,13,0.15);
      border: 1px solid rgba(69,168,13,0.3);
      color: var(--fn-lime);
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 5px 14px;
      border-radius: 999px;
      margin-bottom: 16px;
    }
    .checkout-hero h1 {
      font-family: 'Raleway', sans-serif;
      font-size: clamp(1.6rem, 3vw, 2.2rem);
      font-weight: 800;
      color: #fff;
      margin-bottom: 10px;
    }
    .checkout-hero h1 span { color: var(--fn-lime); }
    .checkout-hero p { color: var(--fn-muted); font-size: 0.92rem; }

    /* ── Layout ── */
    .checkout-body { padding: 40px 0 80px; }

    /* ── Cards ── */
    .fn-card {
      background: var(--fn-card);
      border: 1px solid var(--fn-border);
      border-radius: 20px;
      padding: 26px;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      margin-bottom: 20px;
    }
    .fn-card-title {
      font-family: 'Raleway', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .fn-card-title i { color: var(--fn-lime); }

    /* ── Cart item ── */
    .cart-item {
      display: grid;
      grid-template-columns: 88px 1fr;
      gap: 14px;
      padding: 16px 0;
      border-bottom: 1px solid rgba(69,168,13,0.1);
      transition: opacity 0.3s, transform 0.3s;
    }
    .cart-item:last-child { border-bottom: none; }
    .cart-item.removing   { opacity: 0; transform: translateX(30px); pointer-events: none; }

    @media (min-width: 640px) {
      .cart-item { grid-template-columns: 100px 1fr; }
    }

    .cart-img {
      width: 88px; height: 88px;
      object-fit: cover;
      border-radius: 14px;
      background: rgba(255,255,255,0.04);
      border: 1px solid var(--fn-border);
      flex-shrink: 0;
    }
    @media (min-width: 640px) {
      .cart-img { width: 100px; height: 100px; }
    }

    .cart-body { display: flex; flex-direction: column; gap: 5px; min-width: 0; }

    .cart-name {
      font-family: 'Raleway', sans-serif;
      font-size: 0.98rem;
      font-weight: 700;
      color: #fff;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .cart-meta {
      font-size: 0.78rem;
      color: var(--fn-muted);
      line-height: 1.5;
    }

    .cart-unit-price {
      font-size: 0.85rem;
      color: var(--fn-lime);
      font-weight: 600;
    }

    .cart-controls {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 6px;
    }

    .qty-input {
      width: 70px;
      padding: 8px 6px;
      background: rgba(255,255,255,0.05);
      border: 1px solid var(--fn-border);
      border-radius: 10px;
      color: var(--fn-text);
      font-family: 'Inter', sans-serif;
      font-size: 0.9rem;
      font-weight: 700;
      text-align: center;
      outline: none;
      transition: border-color 0.2s;
    }
    .qty-input:focus  { border-color: var(--fn-green); }
    .qty-input.saving { border-color: var(--fn-gold); background: rgba(240,192,64,0.08); }

    .item-total-wrap .r-label {
      font-size: 0.68rem;
      color: var(--fn-muted);
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }
    .item-total {
      font-family: 'Raleway', sans-serif;
      font-size: 0.95rem;
      font-weight: 800;
      color: #fff;
    }

    .btn-remove {
      background: rgba(232,85,85,0.1);
      border: 1px solid rgba(232,85,85,0.2);
      color: var(--fn-red);
      padding: 7px 11px;
      border-radius: 10px;
      font-size: 13px;
      cursor: pointer;
      transition: background 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      white-space: nowrap;
      margin-left: auto;
    }
    .btn-remove:hover    { background: rgba(232,85,85,0.18); }
    .btn-remove:disabled { opacity: 0.4; pointer-events: none; }

    /* ── Cart actions ── */
    .cart-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 20px;
      padding-top: 16px;
      border-top: 1px solid var(--fn-border);
    }

    .btn-outline-fn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 10px 16px;
      background: transparent;
      border: 1px solid var(--fn-border);
      border-radius: 12px;
      color: var(--fn-text);
      font-family: 'Inter', sans-serif;
      font-size: 0.83rem;
      font-weight: 500;
      text-decoration: none;
      cursor: pointer;
      transition: border-color 0.2s, background 0.2s;
    }
    .btn-outline-fn:hover { border-color: var(--fn-green); background: rgba(69,168,13,0.07); color: var(--fn-lime); }
    .btn-outline-fn.danger { border-color: rgba(232,85,85,0.3); color: var(--fn-red); }
    .btn-outline-fn.danger:hover { background: rgba(232,85,85,0.1); border-color: var(--fn-red); }

    /* ── Summary box ── */
    .summary-box {
      background: var(--fn-card);
      border: 1px solid var(--fn-border);
      border-radius: 20px;
      padding: 24px;
      backdrop-filter: blur(12px);
      position: sticky;
      top: 80px;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      font-size: 0.9rem;
      border-bottom: 1px solid rgba(69,168,13,0.08);
    }
    .summary-row:last-of-type { border-bottom: none; }
    .summary-row .label { color: var(--fn-muted); }
    .summary-row .value { color: var(--fn-text); font-weight: 600; }

    .summary-total {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 0 0;
      margin-top: 8px;
      border-top: 1px solid var(--fn-border);
    }
    .summary-total .label {
      font-family: 'Raleway', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      color: #fff;
    }
    .summary-total .value {
      font-family: 'Raleway', sans-serif;
      font-size: 1.3rem;
      font-weight: 800;
      color: var(--fn-lime);
    }

    /* ── Proceed button ── */
    .btn-proceed {
      width: 100%;
      padding: 14px;
      background: var(--fn-green);
      color: #fff;
      font-family: 'Raleway', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      border: none;
      border-radius: 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      margin-top: 20px;
      transition: background 0.2s, transform 0.15s;
    }
    .btn-proceed:hover  { background: #3a9008; color: #fff; transform: translateY(-1px); }
    .btn-proceed:active { transform: scale(0.98); }

    /* ── Empty state ── */
    .empty-box {
      background: var(--fn-card);
      border: 1px solid var(--fn-border);
      border-radius: 20px;
      padding: 60px 24px;
      text-align: center;
    }
    .empty-box i { font-size: 48px; color: var(--fn-green); display: block; margin-bottom: 16px; }
    .empty-box h3 { color: #fff; margin-bottom: 8px; }
    .empty-box p  { color: var(--fn-muted); font-size: 0.9rem; }

    /* ── Trust badges ── */
    .trust-badges {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 16px;
    }
    .trust-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 11px;
      background: rgba(69,168,13,0.08);
      border: 1px solid rgba(69,168,13,0.15);
      border-radius: 999px;
      font-size: 0.72rem;
      color: var(--fn-muted);
    }
    .trust-badge i { color: var(--fn-lime); font-size: 11px; }

    /* ── Footer ── */
    .fn-footer {
      background: #040e08;
      border-top: 1px solid var(--fn-border);
      padding: 48px 0 24px;
    }
    .fn-footer h4 {
      font-family: 'Raleway', sans-serif;
      font-size: 0.95rem; font-weight: 700;
      color: #fff; margin-bottom: 16px;
    }
    .fn-footer p, .fn-footer a, .fn-footer li {
      color: var(--fn-muted); font-size: 0.875rem;
      text-decoration: none; line-height: 1.9; list-style: none;
    }
    .fn-footer a:hover { color: var(--fn-lime); }
    .fn-footer ul { padding: 0; margin: 0; }
    .fn-footer-bottom {
      border-top: 1px solid var(--fn-border);
      margin-top: 32px; padding-top: 20px;
      text-align: center; color: var(--fn-muted); font-size: 0.82rem;
    }

    .scroll-top { background: var(--fn-green) !important; }
  </style>
</head>

<body>

<?php
$current_page = 'checkout';
include 'navbar.php';
?>

<main>

  <!-- Hero -->
  <section class="checkout-hero">
    <div class="container">
      <span class="eyebrow"><i class="bi bi-cart3"></i> Your Order</span>
      <h1>Your <span>Checkout</span></h1>
      <p>Review your selected fingerlings before proceeding to payment.</p>
    </div>
  </section>

  <!-- Body -->
  <section class="checkout-body">
    <div class="container">
      <div class="row g-4">

        <!-- LEFT: cart items -->
        <div class="col-lg-8">

          <?php if (!empty($cart)): ?>
            <div class="fn-card" data-aos="fade-up">
              <div class="fn-card-title">
                <i class="bi bi-cart3"></i>
                Cart Items
                <span style="font-size:0.78rem;font-weight:500;color:var(--fn-muted);margin-left:4px;">
                  (<?= $cart_count ?> item<?= $cart_count != 1 ? 's' : '' ?>)
                </span>
              </div>

              <div id="cart-items-container">
                <?php foreach ($cart as $item):
                  $item_price = $item['price_min'];
                  $item_total = $item_price * $item['quantity'];
                ?>
                <div class="cart-item" data-id="<?= $item['id'] ?>">

                  <?php
                    $img_src = (!empty($item['image']) && file_exists('uploads/' . $item['image']))
                      ? 'uploads/' . htmlspecialchars($item['image'])
                      : 'assets/img/fish.jpeg';
                  ?>
                  <img
                    src="<?= $img_src ?>"
                    class="cart-img"
                    alt="<?= htmlspecialchars($item['fish_name']) ?>"
                    onerror="this.src='assets/img/fish.jpeg'"
                  >

                  <div class="cart-body">
                    <div class="cart-name"><?= htmlspecialchars($item['fish_name']) ?></div>
                    <div class="cart-meta">
                      <?= htmlspecialchars($item['species']) ?>
                      &nbsp;·&nbsp; <?= htmlspecialchars($item['size_category']) ?>
                      &nbsp;·&nbsp; <?= htmlspecialchars($item['weight_range']) ?>
                    </div>
                    <div class="cart-unit-price" data-unit-price="<?= $item_price ?>">
                      ₦<?= number_format($item_price) ?> <span style="font-size:0.72rem;font-weight:400;color:var(--fn-muted)">/ unit</span>
                    </div>

                    <div class="cart-controls">
                      <input
                        type="number"
                        class="qty-input"
                        data-id="<?= $item['id'] ?>"
                        value="<?= $item['quantity'] ?>"
                        min="1" max="9999"
                      >
                      <div class="item-total-wrap">
                        <div class="r-label">Subtotal</div>
                        <div class="item-total">₦<?= number_format($item_total) ?></div>
                      </div>
                      <button
                        class="btn-remove remove-btn"
                        data-id="<?= $item['id'] ?>"
                        data-name="<?= htmlspecialchars($item['fish_name']) ?>"
                      >
                        <i class="bi bi-trash"></i>
                        <span class="d-none d-sm-inline">Remove</span>
                      </button>
                    </div>
                  </div>

                </div>
                <?php endforeach; ?>
              </div>

              <div class="cart-actions">
                <a href="shop.php" class="btn-outline-fn">
                  <i class="bi bi-bag-plus"></i> Add More Fish
                </a>
                <button class="btn-outline-fn danger" id="clear-cart-btn">
                  <i class="bi bi-trash3"></i> Clear Cart
                </button>
              </div>
            </div>

          <?php else: ?>
            <div class="empty-box" id="empty-cart-box" data-aos="fade-up">
              <i class="bi bi-cart-x"></i>
              <h3>Your Cart is Empty</h3>
              <p>You haven't added any fingerlings yet.</p>
              <a href="shop.php" style="display:inline-flex;align-items:center;gap:8px;margin-top:18px;padding:12px 24px;background:var(--fn-green);color:#fff;border-radius:12px;text-decoration:none;font-family:'Raleway',sans-serif;font-weight:700;font-size:0.9rem;">
                <i class="bi bi-bag-fill"></i> Shop Now
              </a>
            </div>
          <?php endif; ?>

        </div>

        <!-- RIGHT: summary -->
        <div class="col-lg-4">
          <div class="summary-box" data-aos="fade-left">
            <div class="fn-card-title" style="margin-bottom:16px;">
              <i class="bi bi-receipt"></i> Order Summary
            </div>

            <div class="summary-row">
              <span class="label">Items</span>
              <span class="value" id="summary-count"><?= $cart_count ?></span>
            </div>
            <div class="summary-row">
              <span class="label">Subtotal</span>
              <span class="value" id="summary-subtotal">₦<?= number_format($subtotal) ?></span>
            </div>
            <div class="summary-row">
              <span class="label">Delivery</span>
              <span class="value" style="font-size:0.78rem;color:var(--fn-muted);">At payment</span>
            </div>

            <div class="summary-total">
              <span class="label">Total</span>
              <span class="value" id="summary-total">₦<?= number_format($subtotal) ?></span>
            </div>

            <?php if (!empty($cart)): ?>
              <a href="payment.php" class="btn-proceed" id="checkout-btn">
                <i class="bi bi-credit-card-2-front-fill"></i> Proceed to Payment
              </a>
            <?php else: ?>
              <a href="shop.php" class="btn-proceed" id="checkout-btn">
                <i class="bi bi-bag-fill"></i> Shop Now
              </a>
            <?php endif; ?>

            <div class="trust-badges">
              <span class="trust-badge"><i class="bi bi-shield-check"></i> Secure checkout</span>
              <span class="trust-badge"><i class="bi bi-patch-check"></i> Healthy stock</span>
              <span class="trust-badge"><i class="bi bi-truck"></i> Fast delivery</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

</main>

<!-- Footer -->
<footer class="fn-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <a href="index.php" style="display:flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:14px;">
          <img src="assets/img/fish.jpeg" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--fn-green);" alt="">
          <span style="font-family:'Raleway',sans-serif;font-weight:700;font-size:1.1rem;color:#fff;">Fish<span style="color:#45A80D;">Nation</span><span style="color:#7ef29d;">.</span></span>
        </a>
        <p>FishNation supplies healthy, disease-free fingerlings to help fish farmers reduce mortality and increase profit.</p>
      </div>
      <div class="col-lg-2 col-md-6">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="shop.php">Shop</a></li>
          <li><a href="tools.php">Farm Tools</a></li>
          <li><a href="payment.php">Payment</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h4>Support</h4>
        <ul>
          <li><a href="index.php#contact">Contact Us</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h4>Need Help?</h4>
        <p>Questions about your order? Reach us on WhatsApp.</p>
        <a href="https://wa.me/234XXXXXXXXXX?text=Hello%20FishNation,%20I%20need%20help%20with%20my%20order"
           target="_blank"
           style="display:inline-flex;align-items:center;gap:8px;margin-top:12px;padding:10px 16px;background:rgba(69,168,13,0.12);border:1px solid var(--fn-border);border-radius:10px;color:var(--fn-lime);text-decoration:none;font-size:0.83rem;font-weight:600;">
          <i class="bi bi-whatsapp"></i> Chat with us
        </a>
      </div>
    </div>
  </div>
  <div class="fn-footer-bottom container">
    <p>© <?= date('Y') ?> <strong style="color:#fff;">FishNation</strong>. All Rights Reserved.</p>
  </div>
</footer>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
  <i class="bi bi-arrow-up-short"></i>
</a>
<div id="preloader"></div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/js/main.js"></script>

<script>
  AOS.init({ duration: 600, once: true });

  // ── Toastify helper ───────────────────────────────────────────
  function toast(msg, ok) {
    Toastify({
      text: msg,
      duration: 3500,
      close: true,
      gravity: 'bottom',
      position: 'right',
      stopOnFocus: true,
      style: {
        background: ok
          ? 'linear-gradient(135deg,#1f7a3d,#45A80D)'
          : 'linear-gradient(135deg,#b91c1c,#e85555)',
        borderRadius: '12px',
        fontFamily: "'Inter', sans-serif",
        fontSize: '14px',
        fontWeight: '500',
        padding: '13px 20px',
        boxShadow: '0 8px 24px rgba(0,0,0,0.3)'
      }
    }).showToast();
  }

  // ── Cart pill ─────────────────────────────────────────────────
  function bumpPill(count) {
    const el = document.getElementById('cart-count');
    if (!el) return;
    el.textContent = count;
    el.setAttribute('data-zero', count == 0 ? 'true' : 'false');
    el.classList.remove('bump');
    void el.offsetWidth;
    el.classList.add('bump');
  }

  // ── Naira formatter ───────────────────────────────────────────
  function naira(n) { return '₦' + Math.round(n).toLocaleString('en-NG'); }

  // ── Recalculate summary ───────────────────────────────────────
  function recalcSummary() {
    const items = document.querySelectorAll('.cart-item:not(.removing)');
    let subtotal = 0, totalItems = 0;

    items.forEach(item => {
      const unit = parseFloat(item.querySelector('[data-unit-price]').dataset.unitPrice);
      const qty  = parseInt(item.querySelector('.qty-input').value) || 1;
      item.querySelector('.item-total').textContent = naira(unit * qty);
      subtotal   += unit * qty;
      totalItems += qty;
    });

    document.getElementById('summary-count').textContent    = totalItems;
    document.getElementById('summary-subtotal').textContent = naira(subtotal);
    document.getElementById('summary-total').textContent    = naira(subtotal);
    bumpPill(totalItems);
  }

  // ── Empty state ───────────────────────────────────────────────
  function checkEmpty() {
    if (document.querySelectorAll('.cart-item').length === 0) {
      document.querySelector('.fn-card').innerHTML = `
        <div class="empty-box">
          <i class="bi bi-cart-x" style="font-size:48px;color:var(--fn-green);display:block;margin-bottom:16px;"></i>
          <h3>Your Cart is Empty</h3>
          <p style="color:var(--fn-muted);">Browse our stock and add fingerlings to your cart.</p>
          <a href="shop.php" style="display:inline-flex;align-items:center;gap:8px;margin-top:18px;padding:12px 24px;background:var(--fn-green);color:#fff;border-radius:12px;text-decoration:none;font-family:'Raleway',sans-serif;font-weight:700;font-size:0.9rem;">
            <i class="bi bi-bag-fill"></i> Shop Now
          </a>
        </div>`;
      document.getElementById('summary-count').textContent    = '0';
      document.getElementById('summary-subtotal').textContent = '₦0';
      document.getElementById('summary-total').textContent    = '₦0';
      bumpPill(0);
      const btn = document.getElementById('checkout-btn');
      if (btn) { btn.href = 'shop.php'; btn.innerHTML = '<i class="bi bi-bag-fill"></i> Shop Now'; }
    }
  }

  // ── AJAX helper ───────────────────────────────────────────────
  async function cartPost(fd) {
    const r = await fetch('cart-actions.php', {
      method: 'POST', body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    return r.json();
  }

  // ── Remove item ───────────────────────────────────────────────
  document.querySelectorAll('.remove-btn').forEach(btn => {
    btn.addEventListener('click', async function () {
      const id     = this.dataset.id;
      const name   = this.dataset.name;
      const itemEl = this.closest('.cart-item');
      this.disabled = true;
      const fd = new FormData();
      fd.append('action', 'remove');
      fd.append('product_id', id);
      try {
        const data = await cartPost(fd);
        if (data.success) {
          itemEl.classList.add('removing');
          setTimeout(() => { itemEl.remove(); recalcSummary(); checkEmpty(); }, 300);
          toast('🗑️ ' + name + ' removed from cart.', true);
        } else {
          toast(data.message || 'Could not remove item.', false);
          this.disabled = false;
        }
      } catch {
        toast('Network error — please try again.', false);
        this.disabled = false;
      }
    });
  });

  // ── Update quantity ───────────────────────────────────────────
  let qtyTimer = null;
  document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('change', function () {
      let qty = parseInt(this.value);
      if (isNaN(qty) || qty < 1) { qty = 1; this.value = 1; }
      this.classList.add('saving');
      clearTimeout(qtyTimer);
      const id = this.dataset.id;
      qtyTimer = setTimeout(async () => {
        const fd = new FormData();
        fd.append('action', 'update');
        fd.append(`quantities[${id}]`, qty);
        try {
          const data = await cartPost(fd);
          if (data.success) { recalcSummary(); toast('✓ Quantity updated.', true); }
          else toast(data.message || 'Could not update.', false);
        } catch { toast('Network error.', false); }
        finally { input.classList.remove('saving'); }
      }, 500);
    });
  });

  // ── Clear cart ────────────────────────────────────────────────
  const clearBtn = document.getElementById('clear-cart-btn');
  if (clearBtn) {
    clearBtn.addEventListener('click', async function () {
      if (!confirm('Clear your entire cart?')) return;
      this.textContent = 'Clearing…';
      this.disabled = true;
      const fd = new FormData();
      fd.append('action', 'clear');
      try {
        const data = await cartPost(fd);
        if (data.success) {
          document.querySelectorAll('.cart-item').forEach(i => {
            i.classList.add('removing');
            setTimeout(() => i.remove(), 300);
          });
          setTimeout(checkEmpty, 350);
          toast('🗑️ Cart cleared.', true);
        } else {
          toast(data.message || 'Could not clear cart.', false);
          this.innerHTML = '<i class="bi bi-trash3"></i> Clear Cart';
          this.disabled = false;
        }
      } catch {
        toast('Network error.', false);
        this.innerHTML = '<i class="bi bi-trash3"></i> Clear Cart';
        this.disabled = false;
      }
    });
  }
</script>

</body>
</html>
