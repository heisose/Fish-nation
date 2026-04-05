<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include('../config/db.php');

if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}

$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Products</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { font-family:Arial,sans-serif; background:#f4f7fb; }
.sidebar { width:250px; height:100vh; background:#2e4d28; color:white; position:fixed; left:0; top:0; padding:30px 20px; }
.sidebar h2 { margin-bottom:30px; color:#a0d468; }
.sidebar a { display:block; color:white; text-decoration:none; margin-bottom:18px; padding:10px 12px; border-radius:8px; }
.sidebar a:hover { background: rgba(255,255,255,0.08); }
.main { margin-left:250px; padding:30px; }
h2 { color:#2e4d28; margin-bottom:20px; }
table { width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 5px 20px rgba(0,0,0,0.05); }
th, td { padding:12px; text-align:left; border-bottom:1px solid #eee; }
img { width:80px; border-radius:6px; }
a.action { padding:6px 10px; border-radius:6px; color:white; text-decoration:none; font-weight:bold; background:linear-gradient(135deg,#2e4d28,#a0d468); }
a.action:hover { opacity:0.85; }
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
<h2>Manage Products</h2>

<table>
<tr>
<th>Image</th>
<th>Name</th>
<th>Price</th>
<th>Stock</th>
<th>Actions</th>
</tr>

<?php while($row = $products->fetch_assoc()): ?>
<tr>
<td><img src="../uploads/<?php echo $row['image']; ?>"></td>
<td><?php echo $row['fish_name']; ?></td>
<td>₦<?php echo $row['price_min']; ?> - ₦<?php echo $row['price_max']; ?></td>
<td><?php echo $row['stock_status']; ?></td>
<td>
<a class="action" href="edit-product.php?id=<?php echo $row['id']; ?>">Edit</a>
<a class="action" href="?delete=<?php echo $row['id']; ?>">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>

</div>
</body>
</html>