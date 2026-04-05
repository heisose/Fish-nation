<?php
session_start();
include('config/db.php');

// Ensure cart is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit();
}

$cart = $_SESSION['cart'];
$subtotal = 0;

foreach ($cart as $item) {
    $subtotal += ($item['price_min'] * $item['quantity']);
}

$delivery_fee = 3000;
$total = $subtotal + $delivery_fee;

// Save checkout info in session if posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['checkout_name'] = $_POST['customer_name'] ?? '';
    $_SESSION['checkout_phone'] = $_POST['phone'] ?? '';
    $_SESSION['checkout_location'] = $_POST['location'] ?? '';
    $_SESSION['checkout_notes'] = $_POST['notes'] ?? '';
}

$customer_name = $_SESSION['checkout_name'] ?? '';
$phone = $_SESSION['checkout_phone'] ?? '';
$location = $_SESSION['checkout_location'] ?? '';
$notes = $_SESSION['checkout_notes'] ?? '';

// Handle Bank Transfer confirmation
if (isset($_GET['method']) && $_GET['method'] === 'bank') {
    // Save order and payment to database
    $user_id = $_SESSION['user_id'] ?? 0; // Optional: associate with user if logged in

    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, full_name, phone, location, notes, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Paid', NOW())");
    $stmt_order->bind_param("issssd", $user_id, $customer_name, $phone, $location, $notes, $total);

    if ($stmt_order->execute()) {
        $order_id = $stmt_order->insert_id;

        // Save order items
        foreach ($cart as $item) {
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt_item->bind_param("iii", $order_id, $item['id'], $item['quantity']);
            $stmt_item->execute();
            $stmt_item->close();
        }

        // Save payment record
        $stmt_payment = $conn->prepare("INSERT INTO payments (order_id, amount, payment_date) VALUES (?, ?, NOW())");
        $stmt_payment->bind_param("id", $order_id, $total);
        $stmt_payment->execute();
        $stmt_payment->close();

        // Clear cart
        unset($_SESSION['cart']);

        // Redirect to success page
        header("Location: order_success.php?order_id=$order_id");
        exit();
    } else {
        echo "<p class='text-danger text-center mt-4'>Failed to save your order. Please try again.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Payment - FishNation</title>

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    :root {
      --fish-primary: #0d5c3f;
      --fish-dark: #083826;
      --fish-light: #f4fbf7;
      --fish-accent: #1c8c5f;
      --fish-gold: #d6a84f;
      --fish-text: #1f2d2a;
    }

    body { background: linear-gradient(180deg, #f4fbf7 0%, #eaf5ef 100%); color: var(--fish-text); font-family: 'Roboto', sans-serif; }
    .section-title h2, .page-title { color: var(--fish-dark); font-weight: 800; }
    .payment-card, .summary-card { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 12px 35px rgba(0,0,0,0.08); border: 1px solid rgba(13, 92, 63, 0.08); }
    .payment-method-box { border: 2px solid #e5efe9; border-radius: 16px; padding: 20px; margin-bottom: 20px; transition: 0.3s ease; background: #fff; }
    .payment-method-box:hover { border-color: var(--fish-accent); transform: translateY(-2px); }
    .payment-method-box h5 { color: var(--fish-dark); font-weight: 700; }
    .bank-box { background: var(--fish-light); border-left: 5px solid var(--fish-primary); border-radius: 14px; padding: 20px; margin-top: 15px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 15px; }
    .summary-row.total { border-top: 1px solid #e7eee9; padding-top: 15px; margin-top: 15px; font-size: 20px; font-weight: 800; color: var(--fish-dark); }
    .btn-fishnation { background: linear-gradient(135deg, var(--fish-primary), var(--fish-accent)); color: #fff; border: none; padding: 14px 26px; border-radius: 50px; font-weight: 700; text-decoration: none; display: inline-block; transition: 0.3s ease; }
    .btn-fishnation:hover { transform: translateY(-2px); color: #fff; box-shadow: 0 10px 24px rgba(13, 92, 63, 0.25); }
    .btn-outline-fish { border: 2px solid var(--fish-primary); color: var(--fish-primary); background: transparent; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 700; display: inline-block; }
    .badge-soft { background: rgba(13, 92, 63, 0.1); color: var(--fish-primary); padding: 6px 12px; border-radius: 30px; font-size: 13px; font-weight: 600; }
    .payment-note { background: #fff8e8; border-left: 5px solid var(--fish-gold); padding: 16px; border-radius: 12px; font-size: 14px; margin-top: 20px; }
  </style>
</head>

<body>
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <span class="badge-soft">Secure Payment</span>
      <h1 class="page-title mt-3">Complete Your Payment</h1>
      <p class="text-muted">Choose a payment method and complete your FishNation order.</p>
    </div>

    <div class="row g-4">
      <!-- LEFT -->
      <div class="col-lg-8">
        <div class="payment-card">
          <h3 class="mb-4">Payment Options</h3>

          <!-- BANK TRANSFER -->
          <div class="payment-method-box">
            <h5><i class="bi bi-bank2"></i> Bank Transfer</h5>
            <p class="text-muted mb-2">Transfer the exact amount below and send proof of payment.</p>

            <div class="bank-box">
              <p><strong>Bank Name:</strong> Your Bank Name</p>
              <p><strong>Account Name:</strong> FishNation</p>
              <p><strong>Account Number:</strong> 0123456789</p>
              <p><strong>Amount:</strong> ₦<?php echo number_format($total); ?></p>
            </div>

            <div class="mt-4">
              <a href="?method=bank" class="btn-fishnation">
                <i class="bi bi-check-circle"></i> I Have Paid
              </a>
            </div>
          </div>

          <!-- PAYSTACK -->
          <div class="payment-method-box">
            <h5><i class="bi bi-lightning-charge-fill"></i> Paystack</h5>
            <p class="text-muted">Use your card, bank transfer, USSD, or mobile money with Paystack.</p>
            <div class="payment-note"><strong>Developer Note:</strong> Integrate Paystack here with PK LIVE and callback URL.</div>
            <div class="mt-4">
              <a href="#" class="btn-outline-fish"><i class="bi bi-credit-card"></i> Pay with Paystack (Coming Soon)</a>
            </div>
          </div>

          <!-- FLUTTERWAVE -->
          <div class="payment-method-box">
            <h5><i class="bi bi-wallet2"></i> Flutterwave</h5>
            <p class="text-muted">Accept card, transfer, USSD and mobile payments via Flutterwave.</p>
            <div class="payment-note"><strong>Developer Note:</strong> Replace with Flutterwave button using your Public Key.</div>
            <div class="mt-4">
              <a href="#" class="btn-outline-fish"><i class="bi bi-credit-card-2-front"></i> Pay with Flutterwave (Coming Soon)</a>
            </div>
          </div>

        </div>
      </div>

      <!-- RIGHT -->
      <div class="col-lg-4">
        <div class="summary-card">
          <h4 class="mb-4">Order Summary</h4>

          <?php foreach ($cart as $item): ?>
            <div class="summary-row">
              <span><?php echo htmlspecialchars($item['fish_name']); ?> <small class="text-muted">(x<?php echo $item['quantity']; ?>)</small></span>
              <span>₦<?php echo number_format($item['price_min'] * $item['quantity']); ?></span>
            </div>
          <?php endforeach; ?>

          <hr>
          <div class="summary-row"><span>Subtotal</span><span>₦<?php echo number_format($subtotal); ?></span></div>
          <div class="summary-row"><span>Delivery Fee</span><span>₦<?php echo number_format($delivery_fee); ?></span></div>
          <div class="summary-row total"><span>Total</span><span>₦<?php echo number_format($total); ?></span></div>

          <div class="mt-4">
            <h6>Customer Info</h6>
            <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
            <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
            <p class="mb-1"><strong>Location:</strong> <?php echo htmlspecialchars($location); ?></p>
            <?php if (!empty($notes)): ?>
              <p class="mb-0"><strong>Notes:</strong> <?php echo htmlspecialchars($notes); ?></p>
            <?php endif; ?>
          </div>

          <div class="mt-4">
            <a href="checkout.php" class="btn-outline-fish w-100 text-center"><i class="bi bi-arrow-left"></i> Back to Checkout</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>