<?php
require_once dirname(__DIR__) . '/config/database.php';

$error = '';
$success = '';

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
  .login-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: calc(100vh - var(--header-height));
    margin-top: calc(-1 * var(--space-6));
  }

  /* ── Left Panel (Brand Hero) ── */
  .login-left {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    min-height: 600px;
  }
  .login-left-bg {
    position: absolute;
    inset: 0;
    background: url('https://images.unsplash.com/photo-1483985988355-763728e1935b?w=800&h=1000&fit=crop&crop=top') center/cover no-repeat;
  }
  .login-left-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(160deg, rgba(15,15,15,0.75) 0%, rgba(15,15,15,0.3) 50%, rgba(212,175,55,0.15) 100%);
  }
  .login-left-content {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: var(--space-10);
    max-width: 400px;
  }
  .login-left-brand {
    font-family: var(--font-display);
    font-size: clamp(36px, 4vw, 52px);
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.04em;
    margin-bottom: var(--space-3);
  }
  .login-left-tagline {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    color: var(--color-accent);
    margin-bottom: var(--space-6);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
  }
  .login-left-tagline::before,
  .login-left-tagline::after {
    content: '';
    width: 32px;
    height: 1px;
    background: var(--color-accent);
  }
  .login-left-desc {
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: var(--space-8);
  }
  .login-left-features {
    display: flex;
    gap: var(--space-6);
    justify-content: center;
  }
  .login-feature {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
  }
  .login-feature-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(6px);
  }
  .login-feature-icon svg { width: 18px; height: 18px; stroke: var(--color-accent); }
  .login-feature span {
    font-size: 11px;
    color: rgba(255,255,255,0.6);
    font-weight: 500;
  }

  /* ── Right Panel (Form) ── */
  .login-right {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-10);
    background: var(--color-bg);
  }
  .login-form-wrap {
    width: 100%;
    max-width: 400px;
  }
  .login-form-header {
    text-align: center;
    margin-bottom: var(--space-8);
  }
  .login-form-header .brand {
    font-family: var(--font-display);
    font-size: 22px;
    font-weight: 700;
    color: var(--color-text-main);
    margin-bottom: var(--space-2);
    letter-spacing: 0.02em;
  }
  .login-form-header h1 {
    font-family: var(--font-display);
    font-size: 28px;
    font-weight: 700;
    color: var(--color-text-main);
    margin-bottom: var(--space-2);
  }
  .login-form-header p {
    color: var(--color-text-muted);
    font-size: 14px;
  }

  .login-form .form-group {
    margin-bottom: var(--space-4);
  }
  .login-form .form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--color-text-main);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 6px;
  }
  .login-form .form-group input {
    width: 100%;
    padding: 13px 16px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    font-size: 14px;
    color: var(--color-text-main);
    background: var(--color-surface);
    transition: var(--transition);
    font-family: var(--font-body);
    box-sizing: border-box;
  }
  .login-form .form-group input:focus {
    outline: none;
    border-color: var(--color-accent);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
  }
  .login-form .form-group input::placeholder {
    color: var(--color-text-muted);
  }

  .login-submit {
    width: 100%;
    padding: 14px;
    background: var(--color-text-main);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    font-family: var(--font-body);
    letter-spacing: 0.02em;
    margin-top: var(--space-2);
  }
  .login-submit:hover { background: #333; transform: translateY(-1px); }

  .password-field {
    position: relative;
  }
  .password-field input {
    padding-right: 44px !important;
  }
  .toggle-pass {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--color-text-muted);
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
  }
  .toggle-pass:hover {
    color: var(--color-text-main);
  }

  .login-divider {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    margin: var(--space-6) 0;
    color: var(--color-text-muted);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
  }
  .login-divider::before,
  .login-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--color-border);
  }

  .login-social-btn {
    width: 100%;
    padding: 13px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    background: var(--color-surface);
    font-size: 14px;
    font-weight: 500;
    color: var(--color-text-main);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-family: var(--font-body);
  }
  .login-social-btn:hover { border-color: var(--color-text-main); background: var(--color-bg); }

  .login-footer {
    text-align: center;
    margin-top: var(--space-6);
    font-size: 13px;
    color: var(--color-text-muted);
  }
  .login-footer a {
    color: var(--color-accent);
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
  }
  .login-footer a:hover { opacity: 0.7; }

  .login-error {
    background: #FEF2F2;
    border: 1px solid #FECACA;
    color: #991B1B;
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    margin-bottom: var(--space-4);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  @media (max-width: 900px) {
    .login-split { grid-template-columns: 1fr; }
    .login-left { display: none; }
    .login-right { min-height: calc(100vh - var(--header-height)); }
  }
</style>

<div class="login-split">
  <!-- Left: Brand Hero -->
  <div class="login-left">
    <div class="login-left-bg"></div>
    <div class="login-left-content">
      <div class="login-left-brand">ATELIER</div>
      <div class="login-left-tagline">Fashion E-Commerce</div>
      <p class="login-left-desc">
        Discover curated collections that blend timeless elegance with contemporary design. Your style journey begins here.
      </p>
      <div class="login-left-features">
        <div class="login-feature">
          <div class="login-feature-icon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          </div>
          <span>Premium Quality</span>
        </div>
        <div class="login-feature">
          <div class="login-feature-icon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
          </div>
          <span>Free Shipping</span>
        </div>
        <div class="login-feature">
          <div class="login-feature-icon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zM12 12l6-3-6-3v6z"/></svg>
          </div>
          <span>Easy Returns</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Login Form -->
  <div class="login-right">
    <div class="login-form-wrap">
      <div class="login-form-header">
        <div class="brand">ATELIER</div>
        <h1>Welcome Back</h1>
        <p>Sign in to your account to continue</p>
      </div>

      <?php if ($error): ?>
        <div class="login-error">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
          <?= sanitize($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="login-form">
        <?= getCSRFInput() ?>
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="you@example.com" required value="<?= sanitize($_POST['email'] ?? '') ?>">
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
