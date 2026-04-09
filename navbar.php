<?php
/**
 * FishNation — Reusable Navigation Bar
 * =====================================
 * HOW TO USE on every page:
 *
 *   <?php
 *     $current_page = 'home';   // 'home' | 'shop' | 'checkout' | 'gallery' | 'about'
 *     session_start();
 *     $cart_count = 0;          // set from your DB query
 *     include 'navbar.php';
 *   ?>
 */

$current_page = $current_page ?? 'home';
$cart_count   = $cart_count   ?? 0;

function nav_active(string $page, string $current): string {
    return $page === $current ? ' class="active"' : '';
}
?>

<style>
  /* ── Reset overflow so nothing bleeds off-screen ── */
  html, body {
    max-width: 100%;
    overflow-x: hidden;
  }

  /* ── Header ── */
  #fn-header {
    background: rgba(7, 21, 15, 0.92);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1050;
    width: 100%;
  }

  /* Push page content down so it doesn't hide behind the fixed nav */
  body {
    padding-top: 64px;
  }

  #fn-header .fn-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    height: 64px;
    max-width: 1200px;
    margin: 0 auto;
    box-sizing: border-box;
  }

  /* ── Logo ── */
  .fn-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    flex-shrink: 0;
  }

  .fn-logo img {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    object-fit: cover;
  }

  .fn-logo .fn-sitename {
    font-size: 1.25rem;
    font-weight: 700;
    color: #d8f3dc;
    margin: 0;
    white-space: nowrap;
  }

  .fn-logo .fn-nation  { color: #45A80D; }
  .fn-logo .fn-dot     { color: #7ef29d; }

  /* ── Desktop nav ── */
  #fn-nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  #fn-nav ul li a {
    color: #d8f3dc;
    font-weight: 500;
    font-size: 0.92rem;
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: color 0.2s, background 0.2s;
    white-space: nowrap;
  }

  #fn-nav ul li a:hover,
  #fn-nav ul li a.active {
    color: #7ef29d;
    background: rgba(126, 242, 157, 0.08);
  }

  /* ── Cart pill ── */
  .fn-cart-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    font-size: 0.68rem;
    font-weight: 700;
    border-radius: 999px;
    background: #45A80D;
    color: #fff;
    line-height: 1;
  }

  .fn-cart-pill[data-zero="true"] { display: none; }

  /* ── CTA button ── */
  .fn-cta {
    background: #45A80D;
    color: #fff !important;
    border-radius: 7px;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 8px 15px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background 0.2s, transform 0.15s;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .fn-cta:hover {
    background: #3a9008;
    transform: translateY(-1px);
  }

  /* ── Hamburger button ── */
  #fn-toggle {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 6px;
    color: #d8f3dc;
    font-size: 1.6rem;
    line-height: 1;
    margin-left: 8px;
  }

  /* ── MOBILE ── */
  @media (max-width: 991px) {

    #fn-nav {
      display: none;
      position: absolute;
      top: 64px;
      left: 0;
      right: 0;
      background: rgba(7, 21, 15, 0.97);
      border-top: 1px solid rgba(255,255,255,0.07);
      padding: 12px 0 16px;
      z-index: 999;
    }

    #fn-nav.open { display: block; }

    #fn-nav ul {
      flex-direction: column;
      gap: 2px;
      padding: 0 12px;
    }

    #fn-nav ul li a {
      font-size: 1rem;
      padding: 10px 14px;
      border-radius: 8px;
    }

    /* CTA inside dropdown on mobile */
    .fn-cta-mobile {
      display: flex !important;
      margin: 10px 12px 0;
      justify-content: center;
    }

    /* Hide header-level CTA on mobile */
    #fn-header .fn-cta-desktop { display: none; }

    #fn-toggle { display: flex; align-items: center; }
  }

  @media (min-width: 992px) {
    .fn-cta-mobile { display: none !important; }
  }
</style>

<header id="fn-header">
  <div class="fn-inner">

    <!-- Logo -->
    <a href="index.php" class="fn-logo">
      <img src="assets/img/fish.jpeg" alt="FishNation">
      <h1 class="fn-sitename">
        Fish<span class="fn-nation">Nation</span><span class="fn-dot">.</span>
      </h1>
    </a>

    <!-- Nav -->
    <nav id="fn-nav" role="navigation" aria-label="Main navigation">
      <ul>
        <li><a href="index.php#about"<?= nav_active('about',   $current_page) ?>>About</a></li>
        <li><a href="shop.php"<?= nav_active('shop',    $current_page) ?>>View Product</a></li>
        <li><a href="index.php#gallery"<?= nav_active('gallery', $current_page) ?>>Gallery</a></li>
        <li><a href="index.php#contact"<?= nav_active('contact', $current_page) ?>>Contact</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li>
            <a href="checkout.php"<?= nav_active('checkout', $current_page) ?>>
              <i class="bi bi-cart3"></i> Cart
              <span class="fn-cart-pill" id="cart-count"
                    data-zero="<?= $cart_count == 0 ? 'true' : 'false' ?>">
                <?= (int) $cart_count ?>
              </span>
            </a>
          </li>
          <li>
            <a href="logout.php" title="Log out">
              <i class="bi bi-box-arrow-left"></i> Logout
            </a>
          </li>
        <?php else: ?>
          <li>
            <a href="login.php" title="Log in">
              <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- CTA inside dropdown on mobile -->
      <div class="fn-cta-mobile">
        <?php if ($current_page === 'checkout'): ?>
          <a class="fn-cta" href="shop.php">
            <i class="bi bi-bag-plus-fill"></i> Continue Shopping
          </a>
        <?php elseif ($current_page === 'shop'): ?>
          <a class="fn-cta" href="checkout.php">
            <i class="bi bi-bag-check-fill"></i> Checkout
          </a>
        <?php else: ?>
          <a class="fn-cta"
             href="https://wa.me/234XXXXXXXXXX?text=Hello%20FishNation,%20I%20want%20to%20order%20fingerlings"
             target="_blank" rel="noopener">
            <i class="bi bi-whatsapp"></i> Order via WhatsApp
          </a>
        <?php endif; ?>
      </div>
    </nav>

    <!-- CTA on desktop (right side of header) -->
    <div class="fn-cta-desktop">
      <?php if ($current_page === 'checkout'): ?>
        <a class="fn-cta" href="shop.php">
          <i class="bi bi-bag-plus-fill"></i> Continue Shopping
        </a>
      <?php elseif ($current_page === 'shop'): ?>
        <a class="fn-cta" href="checkout.php">
          <i class="bi bi-bag-check-fill"></i> Checkout
        </a>
      <?php else: ?>
        <a class="fn-cta"
           href="https://wa.me/234XXXXXXXXXX?text=Hello%20FishNation,%20I%20want%20to%20order%20fingerlings"
           target="_blank" rel="noopener">
          <i class="bi bi-whatsapp"></i> Order via WhatsApp
        </a>
      <?php endif; ?>
    </div>

    <!-- Hamburger -->
    <button id="fn-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="fn-nav">
      <i class="bi bi-list" id="fn-toggle-icon"></i>
    </button>

  </div>
</header>

<!-- Toast container -->
<div id="cart-toast"></div>

<script>
  (function () {
    const btn  = document.getElementById('fn-toggle');
    const nav  = document.getElementById('fn-nav');
    const icon = document.getElementById('fn-toggle-icon');

    btn.addEventListener('click', function () {
      const isOpen = nav.classList.toggle('open');
      btn.setAttribute('aria-expanded', isOpen);
      icon.className = isOpen ? 'bi bi-x-lg' : 'bi bi-list';
    });

    // Close nav when a link is tapped on mobile
    nav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        nav.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        icon.className = 'bi bi-list';
      });
    });

    // Close nav on outside click
    document.addEventListener('click', function (e) {
      if (!nav.contains(e.target) && !btn.contains(e.target)) {
        nav.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        icon.className = 'bi bi-list';
      }
    });
  })();
</script>
