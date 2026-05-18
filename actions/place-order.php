<?php
include('../config/db.php');

$name = $_POST['customer_name'];
$phone = $_POST['phone'];
$fish = $_POST['fish_type'];
$size = $_POST['size_category'];
$qty = $_POST['quantity'];
$loc = $_POST['location'];

$conn->query("INSERT INTO orders 
(customer_name, phone, fish_type, size_category, quantity, location)
VALUES ('$name','$phone','$fish','$size','$qty','$loc')");

header("Location: ../index.php");
?>