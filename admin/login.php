<?php
require_once dirname(__DIR__) . '/config/database.php';

if (isAdminLoggedIn()) {
  redirect(BASE_URL . '/admin/index.php');
}

$error = '';
$emailValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $error = 'Invalid request. Please try again.';
  } elseif (rateLimit('admin_login', 5, 300)) {
    $remaining = getRemainingAttempts('admin_login', 5, 300);
    $error = 'Too many failed attempts. Please wait a few minutes.';
  } else {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
      $error = 'Please enter both email and password.';
      $emailValue = htmlspecialchars($email);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = 'Please enter a valid email address.';
      $emailValue = htmlspecialchars($email);
    } else {
      $admin = null;
      if ($mysqli) {
        $stmt = $mysqli->prepare('SELECT id, name, email, password, role, is_active FROM admins WHERE email = ?');
        if ($stmt) {
          $stmt->bind_param('s', $email);
          $stmt->execute();
          $admin = $stmt->get_result()->fetch_assoc();
          $stmt->close();
        }
      }

      if ($admin && $admin['is_active'] && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $admin['role'];

        if ($mysqli) {
          $upd = $mysqli->prepare('UPDATE admins SET last_login = NOW() WHERE id = ?');
          if ($upd) {
            $upd->bind_param('i', $admin['id']);
            $upd->execute();
            $upd->close();
          }
        }

        redirect(BASE_URL . '/admin/index.php');
      } else {
        $error = 'Invalid email or password.';
        $emailValue = htmlspecialchars($email);
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — urban outfit</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      background: #0A0A0A;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      position: relative;
      overflow: hidden;
    }

    body::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(ellipse at 30% 20%, rgba(212, 175, 55, 0.06) 0%, transparent 50%),
                  radial-gradient(ellipse at 70% 80%, rgba(212, 175, 55, 0.03) 0%, transparent 50%);
      pointer-events: none;
    }

    .login-wrapper {
      width: 100%;
      max-width: 420px;
      position: relative;
      z-index: 1;
    }

    .login-brand {
      text-align: center;
      margin-bottom: 40px;
    }

    .login-brand-name {
      font-size: 28px;
      font-weight: 800;
      color: #FFFFFF;
      letter-spacing: -0.5px;
    }

    .login-brand-name span {
      color: #D4AF37;
    }

    .login-brand-sub {
      font-size: 11px;
      font-weight: 600;
      color: #555;
      text-transform: uppercase;
      letter-spacing: 3px;
      margin-top: 6px;
    }

    .login-card {
      background: #141414;
      border: 1px solid #222;
      border-radius: 20px;
      padding: 36px 32px;
    }

    .login-title {
      font-size: 18px;
      font-weight: 700;
      color: #FFF;
      margin-bottom: 4px;
    }

    .login-subtitle {
      font-size: 13px;
      color: #666;
      margin-bottom: 28px;
    }

    .field {
      margin-bottom: 20px;
    }

    .field-label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: #888;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .field-input {
      width: 100%;
      background: #1A1A1A;
      border: 1.5px solid #2A2A2A;
      border-radius: 12px;
      padding: 14px 16px;
      font-size: 14px;
      font-family: inherit;
      color: #FFF;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .field-input::placeholder {
      color: #444;
    }

    .field-input:focus {
      border-color: #D4AF37;
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
    }

    .error-msg {
      background: rgba(239, 68, 68, 0.08);
      border: 1px solid rgba(239, 68, 68, 0.2);
      color: #F87171;
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 500;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .error-msg svg {
      flex-shrink: 0;
    }

    .btn-submit {
      width: 100%;
      padding: 14px;
      background: #D4AF37;
      color: #0A0A0A;
      border: none;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s;
      margin-top: 4px;
    }

    .btn-submit:hover {
      background: #E5C04A;
    }

    .btn-submit:active {
      transform: scale(0.98);
    }

    .login-footer {
      text-align: center;
      margin-top: 24px;
    }

    .login-footer a {
      font-size: 13px;
      font-weight: 600;
      color: #D4AF37;
      text-decoration: none;
      transition: color 0.2s;
    }

    .login-footer a:hover {
      color: #E5C04A;
    }

    @media (max-width: 480px) {
      body { padding: 16px; }
      .login-card { padding: 28px 20px; }
    }
  </style>
</head>
<body>

  <div class="login-wrapper">
    <div class="login-brand">
      <div class="login-brand-name">urban <span>outfit</span></div>
      <div class="login-brand-sub">Admin Panel</div>
    </div>

    <div class="login-card">
      <h1 class="login-title">Welcome back</h1>
      <p class="login-subtitle">Sign in to manage your store</p>

      <?php if (!empty($error)): ?>
        <div class="error-msg">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <?= getCSRFInput() ?>

        <div class="field">
          <label class="field-label" for="email">Email</label>
          <input class="field-input" type="email" id="email" name="email" value="<?= $emailValue ?>" placeholder="you@example.com" required autofocus>
        </div>

        <div class="field">
          <label class="field-label" for="password">Password</label>
          <input class="field-input" type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-submit">Sign In</button>
      </form>
    </div>

    <div class="login-footer">
      <a href="<?= BASE_URL ?>/">&larr; Back to store</a>
    </div>
  </div>

</body>
</html>
