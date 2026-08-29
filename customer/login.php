<?php
require_once dirname(__DIR__) . '/config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = sanitize($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    $error = 'Please enter both email and password.';
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

$pageTitle = 'Login — ATELIER';
$pageDescription = 'Sign in to your account.';
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
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="you@example.com" required value="<?= sanitize($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="login-submit">Sign In</button>
      </form>

      <div class="login-divider">or</div>

      <button class="login-social-btn" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
        Continue with Google
      </button>

      <div class="login-footer">
        Don't have an account? <a href="<?= BASE_URL ?>/customer/register.php">Create Account</a>
      </div>
    </div>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
