<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include('../config/db.php');

$id = intval($_GET['id']); // sanitize id
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

$success = "";
$error = "";

if(isset($_POST['update'])){
    $fish_name = $_POST['fish_name'];
    $species = $_POST['species'];
    $size_category = $_POST['size_category'];
    $weight_range = $_POST['weight_range'];
    $price_min = $_POST['price_min'];
    $price_max = $_POST['price_max'];
    $stock_status = $_POST['stock_status'];

    // Image handling
    $image_name = $product['image']; // default current image
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','webp'];
        if(in_array(strtolower($ext), $allowed)){
            $new_name = uniqid('fish_').'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/".$new_name);
            $image_name = $new_name;
        } else {
            $error = "Invalid image format. Only JPG, PNG, WEBP allowed.";
        }
    }

    if(!$error){
        $stmt_update = $conn->prepare("UPDATE products SET fish_name=?, species=?, size_category=?, weight_range=?, price_min=?, price_max=?, stock_status=?, image=? WHERE id=?");
        $stmt_update->bind_param("ssssddssi", $fish_name, $species, $size_category, $weight_range, $price_min, $price_max, $stock_status, $image_name, $id);

        if($stmt_update->execute()){
            $success = "Product updated successfully!";
            $product = array_merge($product, $_POST, ['image'=>$image_name]); // refresh values
        } else {
            $error = "Failed to update product!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product - FishNation Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { font-family: Arial,sans-serif; background:#f4f7fb; margin:0; }
.sidebar { width:250px; height:100vh; background:#2e4d28; color:white; position:fixed; padding:30px 20px; }
.sidebar h2 { margin-bottom:30px; color:#a0d468; }
.sidebar a { display:block; color:white; text-decoration:none; margin-bottom:18px; padding:10px 12px; border-radius:8px; }
.sidebar a:hover { background: rgba(255,255,255,0.08); }
.main { margin-left:250px; padding:30px; }
h1 { color:#2e4d28; margin-bottom:10px; }
.form-box { background:white; padding:30px; border-radius:14px; box-shadow:0 5px 20px rgba(0,0,0,0.06); max-width:700px; }
input, select, button { width:100%; padding:14px; margin-bottom:18px; border-radius:10px; border:1px solid #ccc; font-size:15px; }
button { background: linear-gradient(135deg,#2e4d28,#a0d468); color:white; border:none; cursor:pointer; font-weight:bold; }
button:hover { opacity:0.85; }
.success { background:#d4edda; color:#155724; padding:12px; border-radius:10px; margin-bottom:20px; }
.error { background:#f8d7da; color:#721c24; padding:12px; border-radius:10px; margin-bottom:20px; }
img.preview { max-width:150px; margin-bottom:15px; border-radius:8px; }
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

<div class="main">
<h1>Edit Product</h1>

<div class="form-box">
<?php 
if($success) echo "<div class='success'>$success</div>";
if($error) echo "<div class='error'>$error</div>";
?>

<form method="post" enctype="multipart/form-data">
<input type="text" name="fish_name" placeholder="Fish Name" value="<?php echo htmlspecialchars($product['fish_name']); ?>" required>
<input type="text" name="species" placeholder="Species" value="<?php echo htmlspecialchars($product['species']); ?>" required>

<select name="size_category" required>
<option value="">Select Size</option>
<option <?php if($product['size_category']=='Fry') echo 'selected'; ?>>Fry</option>
<option <?php if($product['size_category']=='Fingerlings') echo 'selected'; ?>>Fingerlings</option>
<option <?php if($product['size_category']=='Juveniles') echo 'selected'; ?>>Juveniles</option>
<option <?php if($product['size_category']=='Post-Juveniles') echo 'selected'; ?>>Post-Juveniles</option>
</select>

<input type="text" name="weight_range" placeholder="Weight Range" value="<?php echo htmlspecialchars($product['weight_range']); ?>" required>
<input type="number" name="price_min" placeholder="Minimum Price" value="<?php echo htmlspecialchars($product['price_min']); ?>" required>
<input type="number" name="price_max" placeholder="Maximum Price" value="<?php echo htmlspecialchars($product['price_max']); ?>" required>

<select name="stock_status" required>
<option value="">Stock Status</option>
<option <?php if($product['stock_status']=='In Stock') echo 'selected'; ?>>In Stock</option>
<option <?php if($product['stock_status']=='Low Stock') echo 'selected'; ?>>Low Stock</option>
<option <?php if($product['stock_status']=='Out of Stock') echo 'selected'; ?>>Out of Stock</option>
</select>

<?php if($product['image']): ?>
<img src="../uploads/<?php echo $product['image']; ?>" class="preview" alt="Current Image">
<?php endif; ?>
<input type="file" name="image" accept="image/*">

<button type="submit" name="update">Update Product</button>
</form>
</div>

</div>
</body>
</html>