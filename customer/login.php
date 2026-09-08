<?php
require_once dirname(__DIR__) . '/config/database.php';

$error = '';
$success = '';

// Flash message from password reset
if (!empty($_SESSION['flash_success'])) {
  $success = $_SESSION['flash_success'];
  unset($_SESSION['flash_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $error = 'Invalid request. Please try again.';
  } elseif (rateLimit('login', 5, 300)) {
    $error = 'Too many login attempts. Please try again after 5 minutes.';
  } else {
  $email = sanitize($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    $error = 'Please enter both email and password.';
  } elseif (strlen($email) > 254) {
    $error = 'Email address is too long.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } else {
    $stmt = $mysqli->prepare('SELECT id, first_name, last_name, email, password, is_active FROM customers WHERE email = ?');
    if (!$stmt) {
      $error = 'A system error occurred. Please try again.';
    } else {
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $customer = $stmt->get_result()->fetch_assoc();

      if ($customer && password_verify($password, $customer['password'])) {
        if (!$customer['is_active']) {
          $error = 'Your account has been deactivated.';
        } else {
          session_regenerate_id(true);
          $_SESSION['customer_id'] = $customer['id'];
          $_SESSION['customer_name'] = $customer['first_name'] . ' ' . $customer['last_name'];

          $update = $mysqli->prepare('UPDATE customers SET last_login = NOW() WHERE id = ?');
          if ($update) {
            $update->bind_param('i', $customer['id']);
            $update->execute();
          }

          $redirect = $_GET['redirect'] ?? '/customer/account.php';
          redirect($redirect);
        }
      } else {
        $error = 'Invalid email or password.';
      }
    }
  }
  }
}

$pageTitle       = 'Sign In — Urban Outfit Collection';
$pageDescription = 'Sign in to your Urban Outfit Collection account to track orders, manage wishlist and checkout faster.';
$pageRobots      = 'noindex, nofollow';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ====================== LOGIN PAGE ====================== */
.login-page {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: calc(100vh - var(--header-height, 70px));
}

/* Left Panel */
.login-left {
  background: #000;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px;
  position: relative;
  overflow: hidden;
}
.login-left::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -20%;
  width: 140%;
  height: 200%;
  background: radial-gradient(ellipse at center, rgba(255,255,255,0.03) 0%, transparent 60%);
  pointer-events: none;
}
.login-left-content {
  position: relative;
  z-index: 2;
  text-align: center;
  max-width: 320px;
}
.login-brand {
  font-family: 'Inter', var(--font-body);
  font-size: 48px;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.03em;
  margin-bottom: 8px;
}
.login-tagline {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: rgba(255,255,255,0.4);
  margin-bottom: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
}
.login-tagline::before,
.login-tagline::after {
  content: '';
  width: 24px;
  height: 1px;
  background: rgba(255,255,255,0.2);
}
.login-desc {
  font-size: 14px;
  color: rgba(255,255,255,0.5);
  line-height: 1.7;
  margin-bottom: 40px;
}
.login-features {
  display: flex;
  gap: 32px;
  justify-content: center;
}
.login-feature {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}
.login-feature-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
}
.login-feature-icon svg { width: 18px; height: 18px; stroke: #fff; }
.login-feature span {
  font-size: 11px;
  color: rgba(255,255,255,0.5);
  font-weight: 500;
}

/* Right Panel */
.login-right {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px;
  background: #fff;
}
.login-form-wrap {
  width: 100%;
  max-width: 360px;
}
.login-form-header {
  text-align: center;
  margin-bottom: 36px;
}
.login-form-header h1 {
  font-family: 'Inter', var(--font-body);
  font-size: 28px;
  font-weight: 800;
  color: #000;
  margin: 0 0 8px;
  letter-spacing: -0.02em;
}
.login-form-header p {
  font-size: 14px;
  color: #999;
  margin: 0;
}

/* Form */
.login-form .form-group {
  margin-bottom: 16px;
}
.login-form .form-group label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  color: #000;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 6px;
}
.login-form .form-group input {
  width: 100%;
  padding: 13px 16px;
  border: 1px solid #e5e5e5;
  border-radius: 10px;
  font-size: 14px;
  color: #000;
  background: #fafafa;
  transition: all 0.2s;
  font-family: 'Inter', var(--font-body);
  box-sizing: border-box;
}
.login-form .form-group input:focus {
  outline: none;
  border-color: #000;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(0,0,0,0.06);
}
.login-form .form-group input::placeholder {
  color: #bbb;
}

/* Password field */
.password-field { position: relative; }
.password-field input { padding-right: 44px !important; }
.toggle-pass {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #999;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}
.toggle-pass:hover { color: #000; }

/* Submit */
.login-submit {
  width: 100%;
  padding: 14px;
  background: #000;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  font-family: 'Inter', var(--font-body);
  letter-spacing: 0.02em;
  margin-top: 8px;
}
.login-submit:hover {
  background: #222;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Links */
.login-links {
  text-align: center;
  margin-top: 20px;
}
.login-links a {
  font-size: 13px;
  color: #000;
  font-weight: 600;
  text-decoration: none;
  transition: opacity 0.2s;
}
.login-links a:hover { opacity: 0.6; }

.login-divider {
  display: flex;
  align-items: center;
  gap: 16px;
  margin: 24px 0;
  color: #ccc;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.login-divider::before,
.login-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #eee;
}

/* Footer */
.login-footer {
  text-align: center;
  margin-top: 24px;
  font-size: 13px;
  color: #999;
}
.login-footer a {
  color: #000;
  font-weight: 700;
  text-decoration: none;
  transition: opacity 0.2s;
}
.login-footer a:hover { opacity: 0.6; }

/* Error / Success */
.login-error {
  background: #FEF2F2;
  border: 1px solid #FECACA;
  color: #991B1B;
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.login-success {
  background: #F0FDF4;
  border: 1px solid #BBF7D0;
  color: #166534;
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Responsive */
@media (max-width: 900px) {
  .login-page { grid-template-columns: 1fr; }
  .login-left { display: none; }
  .login-right {
    min-height: calc(100vh - var(--header-height, 70px));
    padding: 32px 24px;
  }
}
@media (max-width: 480px) {
  .login-right { padding: 24px 16px; }
}
</style>

<div class="login-page">
  <!-- Left: Brand -->
  <div class="login-left">
    <div class="login-left-content">
      <div class="login-brand">UOC</div>
      <div class="login-tagline">Urban Outfit Collection</div>
      <p class="login-desc">
        Fashion and clothing store for men, women and kids. Mukerian, Punjab.
      </p>
      <div class="login-features">
        <div class="login-feature">
          <div class="login-feature-icon">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          </div>
          <span>Premium Quality</span>
        </div>
        <div class="login-feature">
          <div class="login-feature-icon">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          </div>
          <span>Free Shipping</span>
        </div>
        <div class="login-feature">
          <div class="login-feature-icon">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/></svg>
          </div>
          <span>Easy Returns</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Form -->
  <div class="login-right">
    <div class="login-form-wrap">
      <div class="login-form-header">
        <h1>Welcome Back</h1>
        <p>Sign in to your account to continue</p>
      </div>

      <?php if ($error): ?>
        <div class="login-error">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
          <?= esc($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="login-success">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
          <?= esc($success) ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="login-form">
        <?= getCSRFInput() ?>
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="you@example.com" required value="<?= esc($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Password</label>
          <div class="password-field">
            <input type="password" name="password" placeholder="Enter your password" required>
            <button type="button" class="toggle-pass" onclick="togglePassword(this)">
              <svg class="eye-open" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-closed" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>
        <button type="submit" class="login-submit">Sign In</button>
      </form>

      <div class="login-links">
        <a href="<?= BASE_URL ?>/customer/forgot-password.php">Forgot Password?</a>
      </div>

      <div class="login-footer">
        Don't have an account? <a href="<?= BASE_URL ?>/customer/register.php">Create Account</a>
      </div>
    </div>
  </div>
</div>

<script>
function togglePassword(btn) {
  const input = btn.parentElement.querySelector('input');
  const eyeOpen = btn.querySelector('.eye-open');
  const eyeClosed = btn.querySelector('.eye-closed');
  if (input.type === 'password') {
    input.type = 'text';
    eyeOpen.style.display = 'none';
    eyeClosed.style.display = 'block';
  } else {
    input.type = 'password';
    eyeOpen.style.display = 'block';
    eyeClosed.style.display = 'none';
  }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
