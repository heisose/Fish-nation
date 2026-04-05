<?php
include('config/db.php'); // make sure path is correct relative to this file

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // ADD TO CART
    if ($action === 'add') {
        $product_id = intval($_POST['product_id']);
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($product = $result->fetch_assoc()) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'fish_name' => $product['fish_name'],
                    'species' => $product['species'],
                    'size_category' => $product['size_category'],
                    'weight_range' => $product['weight_range'],
                    'price_min' => $product['price_min'],
                    'price_max' => $product['price_max'],
                    'stock_status' => $product['stock_status'],
                    'image' => $product['image'],
                    'quantity' => 1
                ];
            }
        }

        header("Location: checkout.php");
        exit();
    }

    // REMOVE FROM CART
    if ($action === 'remove') {
        $product_id = intval($_POST['product_id']);
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        header("Location: checkout.php");
        exit();
    }

    // UPDATE QUANTITY
    if ($action === 'update') {
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $product_id => $qty) {
                $product_id = intval($product_id);
                $qty = intval($qty);
                if (isset($_SESSION['cart'][$product_id])) {
                    if ($qty <= 0) {
                        unset($_SESSION['cart'][$product_id]);
                    } else {
                        $_SESSION['cart'][$product_id]['quantity'] = $qty;
                    }
                }
            }
        }
        header("Location: checkout.php");
        exit();
    }

    // CLEAR CART
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        header("Location: checkout.php");
        exit();
    }
}

// fallback
header("Location: shop.php");
exit();
?>