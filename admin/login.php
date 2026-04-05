<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FishNation Admin Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { margin:0; font-family:Arial,sans-serif; background: linear-gradient(135deg,#2e4d28,#a0d468); height:100vh; display:flex; justify-content:center; align-items:center; }
.login-box { background:white; padding:40px; border-radius:18px; width:100%; max-width:400px; box-shadow:0 10px 30px rgba(0,0,0,0.2); }
.login-box h2 { text-align:center; margin-bottom:10px; color:#2e4d28; }
.login-box p { text-align:center; color:#555; margin-bottom:25px; }
.login-box input { width:100%; padding:14px; margin-bottom:18px; border:1px solid #ccc; border-radius:10px; }
.login-box button { width:100%; padding:14px; border:none; border-radius:10px; background:linear-gradient(135deg,#2e4d28,#a0d468); color:white; font-size:16px; cursor:pointer; font-weight:bold; }
.login-box button:hover { opacity:0.85; }
</style>
</head>
<body>

<div class="login-box">
<h2>FishNation Admin</h2>
<p>Sign in to manage your hatchery system</p>
<form method="post" action="../actions/login-process.php">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Sign In</button>
</form>
</div>
</body>
</html>