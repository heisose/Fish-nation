<?php
include('config/db.php');

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

header("Location: checkout.php");
exit();
?>