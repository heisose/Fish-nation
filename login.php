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
<html lang="en">
<head>
  <title>Login — FishNation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      background: radial-gradient(ellipse at top left, #0a2e14 0%, #07150f 60%, #020d08 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
    }

    .auth-wrap { width: 100%; max-width: 420px; }

    .auth-logo {
      display: flex; align-items: center; justify-content: center;
      gap: 10px; margin-bottom: 32px; text-decoration: none;
    }
    .auth-logo img {
      width: 44px; height: 44px;
      border-radius: 50%; object-fit: cover;
      border: 2px solid #45A80D;
    }
    .auth-logo .brand { font-size: 1.5rem; font-weight: 700; color: #d8f3dc; letter-spacing: -0.5px; }
    .auth-logo .brand .g   { color: #45A80D; }
    .auth-logo .brand .dot { color: #7ef29d; }

    .auth-card {
      background: rgba(7, 21, 15, 0.85);
      border: 1px solid rgba(69, 168, 13, 0.25);
      border-radius: 20px;
      padding: 36px 32px;
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
    }

    .auth-card h2 {
      text-align: center; color: #d8f3dc;
      font-size: 1.35rem; font-weight: 600; margin-bottom: 6px;
    }
    .auth-card .subtitle {
      text-align: center; color: rgba(216,243,220,0.45);
      font-size: 0.85rem; margin-bottom: 28px;
    }

    .alert {
      padding: 10px 14px; border-radius: 10px;
      font-size: 0.875rem; margin-bottom: 20px; text-align: center;
    }
    .alert-error {
      background: rgba(220,50,50,0.15);
      border: 1px solid rgba(220,50,50,0.3);
      color: #ffb3b3;
    }

    .field { position: relative; margin-bottom: 16px; }
    .field .fi {
      position: absolute; left: 14px; top: 50%;
      transform: translateY(-50%);
      color: rgba(216,243,220,0.35); font-size: 15px; pointer-events: none;
    }
    .field input {
      width: 100%;
      padding: 13px 14px 13px 40px;
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(69,168,13,0.2);
      border-radius: 10px;
      color: #d8f3dc; font-size: 0.92rem;
      outline: none;
      transition: border-color 0.2s, background 0.2s;
    }
    .field input::placeholder { color: rgba(216,243,220,0.3); }
    .field input:focus {
      border-color: #45A80D;
      background: rgba(255,255,255,0.09);
    }
    .toggle-pw {
      position: absolute; right: 14px; top: 50%;
      transform: translateY(-50%);
      color: rgba(216,243,220,0.35); cursor: pointer;
      font-size: 15px; background: none; border: none; padding: 0;
      transition: color 0.2s;
    }
    .toggle-pw:hover { color: #7ef29d; }

    .btn-submit {
      width: 100%; padding: 13px; margin-top: 6px;
      background: #45A80D; color: #fff;
      font-size: 0.95rem; font-weight: 600;
      border: none; border-radius: 10px; cursor: pointer;
      transition: background 0.2s, transform 0.15s;
    }
    .btn-submit:hover  { background: #3a9008; }
    .btn-submit:active { transform: scale(0.98); }

    .divider {
      display: flex; align-items: center; gap: 10px; margin: 22px 0;
    }
    .divider hr { flex: 1; border: none; border-top: 1px solid rgba(255,255,255,0.08); }
    .divider span { color: rgba(216,243,220,0.3); font-size: 0.8rem; }

    .wa-link {
      display: flex; align-items: center; justify-content: center; gap: 8px;
      padding: 12px;
      border: 1px solid rgba(69,168,13,0.2); border-radius: 10px;
      color: #d8f3dc; text-decoration: none; font-size: 0.875rem;
      transition: border-color 0.2s, background 0.2s;
    }
    .wa-link:hover { border-color: #45A80D; background: rgba(69,168,13,0.07); }

    .auth-footer {
      text-align: center; font-size: 0.875rem;
      color: rgba(216,243,220,0.45); margin-top: 20px;
    }
    .auth-footer a { color: #7ef29d; text-decoration: none; font-weight: 500; }
    .auth-footer a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="auth-wrap">

    <a href="index.php" class="auth-logo">
      <img src="assets/img/fish.jpeg" alt="FishNation">
      <span class="brand">Fish<span class="g">Nation</span><span class="dot">.</span></span>
    </a>

    <div class="auth-card">
      <h2>Welcome back</h2>
      <p class="subtitle">Sign in to your FishNation account</p>

      <?php if ($message): ?>
        <div class="alert alert-error"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <form method="POST" autocomplete="on">
        <div class="field">
          <i class="bi bi-envelope fi"></i>
          <input type="email" name="email" placeholder="Email address" required autocomplete="email">
        </div>

        <div class="field">
          <i class="bi bi-lock fi"></i>
          <input type="password" name="password" id="pw" placeholder="Password" required autocomplete="current-password">
          <button type="button" class="toggle-pw" onclick="togglePw()" aria-label="Toggle password">
            <i class="bi bi-eye" id="pw-icon"></i>
          </button>
        </div>

        <button type="submit" class="btn-submit">Sign In</button>
      </form>

      <div class="divider"><hr><span>or</span><hr></div>

      <a href="https://wa.me/234XXXXXXXXXX?text=Hello%20FishNation,%20I%20need%20help%20with%20my%20account"
         target="_blank" rel="noopener" class="wa-link">
        <i class="bi bi-whatsapp" style="color:#45A80D;font-size:16px;"></i>
        Need help? Contact us on WhatsApp
      </a>
    </div>

    <p class="auth-footer">
      No account yet? <a href="register.php">Create one here</a>
    </p>

  </div>

  <script>
    function togglePw() {
      const pw = document.getElementById('pw');
      const ic = document.getElementById('pw-icon');
      if (pw.type === 'password') { pw.type = 'text'; ic.className = 'bi bi-eye-slash'; }
      else                        { pw.type = 'password'; ic.className = 'bi bi-eye'; }
    }
  </script>
</body>
</html>
