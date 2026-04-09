<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include('../config/db.php');

$msgs = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Messages</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { font-family:Arial,sans-serif; background:#f4f7fb; }
.sidebar { width:250px; height:100vh; background:#2e4d28; color:white; position:fixed; left:0; top:0; padding:30px 20px; }
.sidebar h2 { margin-bottom:30px; color:#a0d468; }
.sidebar a { display:block; color:white; text-decoration:none; margin-bottom:18px; padding:10px 12px; border-radius:8px; }
.sidebar a:hover { background: rgba(255,255,255,0.08); }
.main { margin-left:250px; padding:30px; }
h2 { color:#2e4d28; margin-bottom:20px; }
.card { background:white; padding:20px; margin-bottom:15px; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.05); }
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
<h2>Customer Messages</h2>

<?php while($row = $msgs->fetch_assoc()): ?>
<div class="card">
<strong><?php echo $row['name']; ?></strong> (<?php echo $row['email']; ?>)
<p><strong><?php echo $row['subject']; ?></strong></p>
<p><?php echo $row['message']; ?></p>
</div>
<?php endwhile; ?>
</div>

</body>
</html>