<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include('../config/db.php');

$success = "";

if(isset($_POST['save'])){
    $fish_name = $_POST['fish_name'];
    $species = $_POST['species'];
    $size_category = $_POST['size_category'];
    $weight_range = $_POST['weight_range'];
    $price_min = $_POST['price_min'];
    $price_max = $_POST['price_max'];
    $stock_status = $_POST['stock_status'];

    $stmt = $conn->prepare("INSERT INTO products (fish_name, species, size_category, weight_range, price_min, price_max, stock_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdds", $fish_name, $species, $size_category, $weight_range, $price_min, $price_max, $stock_status);

    if($stmt->execute()){
        $success = "Product added successfully!";
    } else {
        $success = "Failed to add product!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product - FishNation Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { margin:0; font-family: Arial,sans-serif; background:#f4f7fb; }
.sidebar { width:250px; height:100vh; background:#2e4d28; color:white; position:fixed; left:0; top:0; padding:30px 20px; }
.sidebar h2 { margin-bottom:30px; color:#a0d468; }
.sidebar a { display:block; color:white; text-decoration:none; margin-bottom:18px; padding:10px 12px; border-radius:8px; transition:0.3s; }
.sidebar a:hover { background: rgba(255,255,255,0.08); }
.main { margin-left:250px; padding:30px; }
h1 { color:#2e4d28; }
.form-box { background:white; padding:30px; border-radius:14px; box-shadow:0 5px 20px rgba(0,0,0,0.06); max-width:700px; }
input, select, button { width:100%; padding:14px; margin-bottom:18px; border-radius:10px; border:1px solid #ccc; }
button { background:linear-gradient(135deg,#2e4d28,#a0d468); color:white; border:none; cursor:pointer; font-weight:bold; }
button:hover { opacity:0.85; }
.success { background:#d4edda; color:#155724; padding:12px; border-radius:10px; margin-bottom:20px; }
</style>
</head>
<body>

<div class="sidebar">
<h2>FishNation Admin</h2>
<a href="dashboard.php">Dashboard</a>
<a href="add-product.php">Add Product</a>
<a href="manage-products.php">Manage Products</a>
<a href="messages.php">Messages</a>
<a href="logout.php">Logout</a>
</div>

<div style="text-align:center;" class="main">
<h1>Add New Product</h1>
<p>Add Fingerlings available for sale.</p>

<div style="text-align:center;" class="form-box">
<?php if($success) echo "<div class='success'>$success</div>"; ?>
<form method="post">
<input type="text" name="fish_name" placeholder="Fish Name" required>
<input type="text" name="species" placeholder="Species" required>
<select name="size_category" required>
<option value="">Select Size</option>
<option>Fry</option>
<option>Fingerlings</option>
<option>Juveniles</option>
<option>Post-Juveniles</option>
</select>
<input type="text" name="weight_range" placeholder="Weight Range (e.g. 2-3g)" required>
<input type="number" name="price_min" placeholder="Minimum Price" required>
<input type="number" name="price_max" placeholder="Maximum Price" required>
<select name="stock_status" required>
<option value="">Stock Status</option>
<option>In Stock</option>
<option>Low Stock</option>
<option>Out of Stock</option>
</select>
<button type="submit" name="save">Add Product</button>
</form>
</div>
</div>
</body>
</html>