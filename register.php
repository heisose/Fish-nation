<?php
include('config/db.php');

$message = "";
$msg_type = "error";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($password) < 8) {
        $message = "Password must be at least 8 characters!";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "Email already registered!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $email, $phone, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['user_id']   = $conn->insert_id;
                $_SESSION['user_name'] = $full_name;
                header("Location: shop.php");
                exit();
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Register — FishNation</title>
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
    .alert-success {
      background: rgba(69,168,13,0.15);
      border: 1px solid rgba(69,168,13,0.35);
      color: #a8f0a0;
    }
    .alert-success a { color: #7ef29d; font-weight: 600; }

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

    /* Password strength bar */
    .pw-strength { margin-top: -8px; margin-bottom: 16px; }
    .pw-strength-bar {
      height: 3px; border-radius: 2px;
      background: rgba(255,255,255,0.08);
      overflow: hidden;
    }
    .pw-strength-fill {
      height: 100%; width: 0%; border-radius: 2px;
      transition: width 0.3s, background 0.3s;
    }
    .pw-strength-label {
      font-size: 0.75rem; color: rgba(216,243,220,0.4);
      margin-top: 4px; min-height: 16px;
    }

    .btn-submit {
      width: 100%; padding: 13px; margin-top: 6px;
      background: #45A80D; color: #fff;
      font-size: 0.95rem; font-weight: 600;
      border: none; border-radius: 10px; cursor: pointer;
      transition: background 0.2s, transform 0.15s;
    }
    .btn-submit:hover  { background: #3a9008; }
    .btn-submit:active { transform: scale(0.98); }

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
      <h2>Create account</h2>
      <p class="subtitle">Join FishNation and start ordering today</p>

      <?php if ($message): ?>
        <div class="alert alert-<?= $msg_type ?>"><?= $message ?></div>
      <?php endif; ?>

      <form method="POST" autocomplete="on">

        <div class="field">
          <i class="bi bi-person fi"></i>
          <input type="text" name="full_name" placeholder="Full name" required autocomplete="name">
        </div>

        <div class="field">
          <i class="bi bi-envelope fi"></i>
          <input type="email" name="email" placeholder="Email address" required autocomplete="email">
        </div>

        <div class="field">
          <i class="bi bi-telephone fi"></i>
          <input type="tel" name="phone" placeholder="Phone number (optional)" autocomplete="tel">
        </div>

        <div class="field">
          <i class="bi bi-lock fi"></i>
          <input type="password" name="password" id="pw" placeholder="Password (min. 8 characters)" required
                 autocomplete="new-password" minlength="8" oninput="checkStrength(this.value)">
          <button type="button" class="toggle-pw" onclick="togglePw('pw','pw-icon')" aria-label="Toggle password">
            <i class="bi bi-eye" id="pw-icon"></i>
          </button>
        </div>

        <div class="pw-strength">
          <div class="pw-strength-bar"><div class="pw-strength-fill" id="pw-bar"></div></div>
          <div class="pw-strength-label" id="pw-label"></div>
        </div>

        <div class="field">
          <i class="bi bi-lock-fill fi"></i>
          <input type="password" name="confirm_password" id="cpw" placeholder="Confirm password" required
                 autocomplete="new-password">
          <button type="button" class="toggle-pw" onclick="togglePw('cpw','cpw-icon')" aria-label="Toggle confirm password">
            <i class="bi bi-eye" id="cpw-icon"></i>
          </button>
        </div>

        <button type="submit" class="btn-submit">Create Account</button>
      </form>
    </div>

    <p class="auth-footer">
      Already have an account? <a href="login.php">Sign in</a>
    </p>

  </div>

  <script>
    function togglePw(id, iconId) {
      const el = document.getElementById(id);
      const ic = document.getElementById(iconId);
      if (el.type === 'password') { el.type = 'text'; ic.className = 'bi bi-eye-slash'; }
      else                        { el.type = 'password'; ic.className = 'bi bi-eye'; }
    }

    function checkStrength(val) {
      const bar   = document.getElementById('pw-bar');
      const label = document.getElementById('pw-label');
      let score = 0;
      if (val.length >= 8)            score++;
      if (/[A-Z]/.test(val))          score++;
      if (/[0-9]/.test(val))          score++;
      if (/[^A-Za-z0-9]/.test(val))   score++;

      const levels = [
        { w: '0%',   bg: 'transparent', text: '' },
        { w: '25%',  bg: '#e25c5c',     text: 'Weak' },
        { w: '50%',  bg: '#f0a040',     text: 'Fair' },
        { w: '75%',  bg: '#7ef29d',     text: 'Good' },
        { w: '100%', bg: '#45A80D',     text: 'Strong' },
      ];
      const l = val.length === 0 ? levels[0] : levels[score] || levels[1];
      bar.style.width      = l.w;
      bar.style.background = l.bg;
      label.textContent    = l.text;
      label.style.color    = l.bg === 'transparent' ? 'rgba(216,243,220,0.4)' : l.bg;
    }
  </script>
</body>
</html>
