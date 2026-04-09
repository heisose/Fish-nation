<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include('../config/db.php');

// Secure update of order status
if(isset($_POST['update'])){
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

$totalOrders = $conn->query("SELECT * FROM orders")->num_rows;
$totalProducts = $conn->query("SELECT * FROM products")->num_rows;
$totalMessages = $conn->query("SELECT * FROM contact_messages")->num_rows;

$orders = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FishNation Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:Arial, sans-serif; }
body { background: #f4f7fb; color:#333; }
.sidebar { width:250px; height:100vh; background:#2e4d28; color:white; position:fixed; left:0; top:0; padding:30px 20px; }
.sidebar h2 { margin-bottom:30px; font-size:24px; color:#a0d468; }
.sidebar a { display:block; color:white; text-decoration:none; margin-bottom:18px; padding:10px 12px; border-radius:8px; transition:0.3s; }
.sidebar a:hover { background: rgba(255,255,255,0.08); }
.main { margin-left:250px; padding:30px; }
.topbar { margin-bottom:30px; }
.topbar h1 { font-size:30px; margin-bottom:8px; color:#2e4d28; }
.topbar p { color: #555; }
.cards { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:20px; margin-bottom:35px; }
.card { background:white; padding:25px; border-radius:14px; box-shadow:0 5px 20px rgba(0,0,0,0.06); }
.card h3 { font-size:16px; color:#666; margin-bottom:10px; }
.card p { font-size:30px; font-weight:bold; color:#2e4d28; }
.section-box { background:white; padding:25px; border-radius:14px; box-shadow:0 5px 20px rgba(0,0,0,0.06); }
.section-box h2 { margin-bottom:20px; color:#2e4d28; }
table { width:100%; border-collapse:collapse; }
table th, table td { padding:14px; border-bottom:1px solid #eee; text-align:left; vertical-align:top; }
table th { background:#f9fafc; }
.status-badge { padding:6px 12px; border-radius:20px; font-size:13px; font-weight:bold; display:inline-block; }
.new { background:#a0d468; color:#1e3b18; }
.processing { background:#f6c23e; color:#5a3e00; }
.delivered { background:#4fc1e9; color:#01445c; }
select, button { padding:8px 12px; border-radius:8px; border:1px solid #ccc; margin-top:5px; }
button { background:#2e4d28; color:white; border:none; cursor:pointer; }
button:hover { background:#1f3219; }
.quick-links { margin-top:30px; display:flex; gap:15px; flex-wrap:wrap; }
.quick-links a { background:#a0d468; color:#2e4d28; padding:12px 18px; border-radius:10px; text-decoration:none; font-weight:bold; }
@media(max-width:768px){ .sidebar{position:relative;width:100%;height:auto;} .main{margin-left:0;} }
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
<div class="topbar">
<h1>Dashboard</h1>
<p>Manage your products, orders and customer messages.</p>
</div>

<!-- Stats -->
<div class="cards">
<div class="card"><h3>Total Orders</h3><p><?php echo $totalOrders; ?></p></div>
<div class="card"><h3>Total Products</h3><p><?php echo $totalProducts; ?></p></div>
<div class="card"><h3>Contact Messages</h3><p><?php echo $totalMessages; ?></p></div>
</div>

<!-- Orders Table -->
<div class="section-box">
<h2>Customer Orders</h2>

<?php if($orders->num_rows > 0): ?>
<table>
<thead>
<tr>
<th>Customer</th>
<th>Fish Type</th>
<th>Size</th>
<th>Qty</th>
<th>Location</th>
<th>Status</th>
<th>Update</th>
</tr>
</thead>
<tbody>
<?php while($row = $orders->fetch_assoc()): ?>
<tr>
<td><strong><?php echo htmlspecialchars($row['customer_name']); ?></strong><br><?php echo htmlspecialchars($row['phone']); ?></td>
<td><?php echo htmlspecialchars($row['fish_type']); ?></td>
<td><?php echo htmlspecialchars($row['size_category']); ?></td>
<td><?php echo htmlspecialchars($row['quantity']); ?></td>
<td><?php echo htmlspecialchars($row['location']); ?></td>
<td>
<span class="status-badge 
<?php echo strtolower($row['status']); ?>">
<?php echo htmlspecialchars($row['status']); ?>
</span>
</td>
<td>
<form method="post">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
<select name="status" required>
<option value="New" <?php if($row['status']=='New') echo 'selected'; ?>>New</option>
<option value="Processing" <?php if($row['status']=='Processing') echo 'selected'; ?>>Processing</option>
<option value="Delivered" <?php if($row['status']=='Delivered') echo 'selected'; ?>>Delivered</option>
</select><br>
<button type="submit" name="update">Update</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php else: ?>
<p>No customer orders yet.</p>
<?php endif; ?>
</div>

<div class="quick-links">
<a href="add-product.php">+ Add New Product</a>
<a href="messages.php">View Messages</a>
</div>

</div>
</body>
</html>