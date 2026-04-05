<?php
include('config/db.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];

            header("Location: index.php");
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - FishNation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1f3b1f, #335c33);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .box {
            background: rgba(255,255,255,0.05);
            padding: 35px;
            border-radius: 25px;
            width: 350px;
            backdrop-filter: blur(10px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.5);
            border: 2px solid #4caf50;
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
            color: #a8d5ba;
        }
        input, button {
            width: 100%;
            padding: 14px;
            margin: 12px 0;
            border-radius: 12px;
            border: none;
            font-size: 15px;
        }
        input { 
            background: rgba(255,255,255,0.12); 
            color: #e6f0e6; 
        }
        input::placeholder { color: #c5e1c5; }
        button { 
            background: #2e4d28; 
            color: #fff; 
            font-weight: bold; 
            cursor: pointer; 
            transition: 0.3s;
        }
        button:hover { 
            background: #3e6a39;
        }
        a { color: #a8d5ba; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .msg { 
            margin-bottom: 12px; 
            padding: 10px;
            border-radius: 10px;
            background: rgba(255,0,0,0.2);
            color: #ffcccc;
            text-align: center;
        }
        p { text-align: center; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>FishNation Login</h2>
        <?php if ($message): ?>
            <div class="msg"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p>No account yet? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>