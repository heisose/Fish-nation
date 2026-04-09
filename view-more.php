<?php

include('config/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product.");
}

$product_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($product['fish_name']); ?> - Product Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #093e27;
      font-family: Arial, sans-serif;
    }

    .product-card {
      background: lightgrey;
      border-radius: 18px;
      padding: 30px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }

    .product-image {
      width: 100%;
      height: 420px;
      object-fit: cover;
      border-radius: 16px;
    }

    .product-title {
      font-size: 2rem;
      font-weight: 700;
      color: #14532d;
      margin-bottom: 20px;
    }

    .product-info p {
      font-size: 1rem;
      margin-bottom: 12px;
      color: #333;
    }

    .price-box {
      font-size: 1.3rem;
      font-weight: 700;
      color: #198754;
      margin: 20px 0;
    }

    .btn-success {
      background-color: #198754;
      border: none;
      padding: 12px 24px;
      border-radius: 10px;
    }

    .btn-success:hover {
      background-color: #146c43;
    }

    .btn-outline-secondary {
      padding: 12px 24px;
      border-radius: 10px;
    }

    .status-badge {
      font-size: 0.95rem;
      padding: 8px 14px;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="product-card">
    <div class="row g-4 align-items-start">

      <div class="col-md-6">
        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" 
             alt="<?php echo htmlspecialchars($product['fish_name']); ?>" 
             class="product-image shadow-sm">
      </div>

      <div class="col-md-6">
        <h2 class="product-title"><?php echo htmlspecialchars($product['fish_name']); ?></h2>

        <div class="product-info">
          <p><strong>Species:</strong> <?php echo htmlspecialchars($product['species']); ?></p>
          
          <p><strong>Size:</strong> <?php echo htmlspecialchars($product['size_category']); ?></p>
          <p><strong>Weight:</strong> <?php echo htmlspecialchars($product['weight_range']); ?></p>

          <p class="price-box">
            Price: ₦<?php echo number_format($product['price_min']); ?> - ₦<?php echo number_format($product['price_max']); ?>
          </p>

          <p>
            <strong>Status:</strong>
            <span class="badge bg-success status-badge">
              <?php echo htmlspecialchars($product['stock_status']); ?>
            </span>
          </p>

          <?php if (!empty($product['description'])): ?>
            <p>
              <strong>Description:</strong><br>
              <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>
          <?php endif; ?>
        </div>

        <div class="mt-4 d-flex flex-wrap gap-2">
          <?php if(isset($_SESSION['user_id'])): ?>
            <form action="cart-actions.php" method="POST" class="d-inline">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
              <input type="hidden" name="redirect" value="checkout.php">
              <button type="submit" class="btn btn-success">
                Add to Cart
              </button>
            </form>
          <?php else: ?>
            <a href="login.php" class="btn btn-success">
              Login to Order
            </a>
          <?php endif; ?>

          <a href="index.php" class="btn btn-outline-secondary">
            Back
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

</body>
</html>