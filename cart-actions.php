<?php
include('config/db.php');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function cart_total_count() {
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function json_response($success, $message = '', $extra = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(
        ['success' => $success, 'message' => $message, 'cart_count' => cart_total_count()],
        $extra
    ));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action   = isset($_POST['action'])   ? trim($_POST['action'])   : '';
    $redirect = isset($_POST['redirect']) ? trim($_POST['redirect']) : 'shop.php';

    // =========================
    // ADD TO CART
    // =========================
    if ($action === 'add') {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

        if ($product_id > 0) {
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($product = $result->fetch_assoc()) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += 1;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'id'            => $product['id'],
                        'fish_name'     => $product['fish_name'],
                        'species'       => $product['species'],
                        'size_category' => $product['size_category'],
                        'weight_range'  => $product['weight_range'],
                        'price_min'     => $product['price_min'],
                        'price_max'     => $product['price_max'],
                        'stock_status'  => $product['stock_status'],
                        'image'         => $product['image'],
                        'quantity'      => 1
                    ];
                }

                if (is_ajax()) {
                    json_response(true, $product['fish_name'] . ' added to cart!');
                }

            } else {
                if (is_ajax()) json_response(false, 'Product not found.');
            }

        } else {
            if (is_ajax()) json_response(false, 'Invalid product.');
        }

        header("Location: " . $redirect);
        exit();
    }

    // =========================
    // REMOVE FROM CART
    // =========================
    if ($action === 'remove') {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            if (is_ajax()) json_response(true, 'Item removed from cart.');
        } else {
            if (is_ajax()) json_response(false, 'Item not found in cart.');
        }

        header("Location: " . $redirect);
        exit();
    }

    // =========================
    // UPDATE QUANTITY
    // =========================
    if ($action === 'update') {
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $product_id => $qty) {
                $product_id = intval($product_id);
                $qty        = intval($qty);

                if (isset($_SESSION['cart'][$product_id])) {
                    if ($qty <= 0) {
                        unset($_SESSION['cart'][$product_id]);
                    } else {
                        $_SESSION['cart'][$product_id]['quantity'] = $qty;
                    }
                }
            }
        }

        if (is_ajax()) json_response(true, 'Cart updated.');

        header("Location: " . $redirect);
        exit();
    }

    // =========================
    // CLEAR CART
    // =========================
    if ($action === 'clear') {
        $_SESSION['cart'] = [];

        if (is_ajax()) json_response(true, 'Cart cleared.');

        header("Location: " . $redirect);
        exit();
    }
}

// fallback
header("Location: index.php");
exit();
?>
