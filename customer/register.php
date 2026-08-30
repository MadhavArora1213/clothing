<?php
require_once dirname(__DIR__) . '/config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $error = 'Invalid request. Please try again.';
  } elseif (rateLimit('register', 3, 600)) {
    $error = 'Too many registration attempts. Please try again after 10 minutes.';
  } else {
  $firstName = sanitize($_POST['first_name'] ?? '');
  $lastName = sanitize($_POST['last_name'] ?? '');
  $email = sanitize($_POST['email'] ?? '');
  $phone = sanitize($_POST['phone'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';

  if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
    $error = 'Please fill in all required fields.';
  } elseif (strlen($firstName) > 100 || strlen($lastName) > 100) {
    $error = 'Name is too long.';
  } elseif (strlen($email) > 254) {
    $error = 'Email address is too long.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } elseif (!empty($phone) && strlen($phone) > 20) {
    $error = 'Phone number is too long.';
  } elseif ($password !== $confirmPassword) {
    $error = 'Passwords do not match.';
  } elseif (strlen($password) < 6) {
    $error = 'Password must be at least 6 characters.';
  } else {
    $stmt = $mysqli->prepare('SELECT id FROM customers WHERE email = ?');
    if (!$stmt) {
      $error = 'A system error occurred. Please try again.';
    } else {
      $stmt->bind_param('s', $email);
      $stmt->execute();
      if ($stmt->get_result()->fetch_assoc()) {
        $error = 'An account with this email already exists.';
      } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare('INSERT INTO customers (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)');
        if ($stmt) {
          $stmt->bind_param('sssss', $firstName, $lastName, $email, $phone, $hashedPassword);
          $stmt->execute();

          session_regenerate_id(true);
          $_SESSION['customer_id'] = $mysqli->insert_id;
          $_SESSION['customer_name'] = $firstName . ' ' . $lastName;
          redirect('/customer/account.php');
        } else {
          $error = 'A system error occurred. Please try again.';
        }
      }
    }
  }
  }
}

$pageTitle = 'Register — ATELIER';
$pageDescription = 'Create your account.';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
  .reg-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: calc(100vh - var(--header-height));
    margin-top: calc(-1 * var(--space-6));
  }

  .reg-left {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    min-height: 700px;
  }
  .reg-left-bg {
    position: absolute;
    inset: 0;
    background: url('https://images.unsplash.com/photo-1487222477894-8943e31ef7b2?w=800&h=1000&fit=crop&crop=top') center/cover no-repeat;
  }
  .reg-left-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(160deg, rgba(15,15,15,0.75) 0%, rgba(15,15,15,0.3) 50%, rgba(212,175,55,0.15) 100%);
  }
  .reg-left-content {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: var(--space-10);
    max-width: 400px;
  }
  .reg-left-brand {
    font-family: var(--font-display);
    font-size: clamp(36px, 4vw, 52px);
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.04em;
    margin-bottom: var(--space-3);
  }
  .reg-left-tagline {
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
  .reg-left-tagline::before,
  .reg-left-tagline::after {
    content: '';
    width: 32px;
    height: 1px;
    background: var(--color-accent);
  }
  .reg-left-desc {
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    line-height: 1.7;
  }

  .reg-right {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-10);
    background: var(--color-bg);
    overflow-y: auto;
  }
  .reg-form-wrap {
    width: 100%;
    max-width: 420px;
  }
  .reg-form-header {
    text-align: center;
    margin-bottom: var(--space-6);
  }
  .reg-form-header .brand {
    font-family: var(--font-display);
    font-size: 22px;
    font-weight: 700;
    color: var(--color-text-main);
    margin-bottom: var(--space-2);
  }
  .reg-form-header h1 {
    font-family: var(--font-display);
    font-size: 26px;
    font-weight: 700;
    color: var(--color-text-main);
    margin-bottom: var(--space-2);
  }
  .reg-form-header p {
    color: var(--color-text-muted);
    font-size: 14px;
  }

  .reg-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-3);
    margin-bottom: var(--space-4);
  }
  .reg-form .form-row.full { grid-template-columns: 1fr; }
  .reg-form .field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--color-text-main);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 6px;
  }
  .reg-form .field label .req { color: #DC2626; }
  .reg-form .field input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    font-size: 14px;
    color: var(--color-text-main);
    background: var(--color-surface);
    transition: var(--transition);
    font-family: var(--font-body);
    box-sizing: border-box;
  }
  .reg-form .field input:focus {
    outline: none;
    border-color: var(--color-accent);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
  }
  .reg-form .field input::placeholder { color: var(--color-text-muted); }

  .reg-submit {
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
    margin-top: var(--space-2);
  }
  .reg-submit:hover { background: #333; transform: translateY(-1px); }

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

  .reg-footer {
    text-align: center;
    margin-top: var(--space-6);
    font-size: 13px;
    color: var(--color-text-muted);
  }
  .reg-footer a {
    color: var(--color-accent);
    font-weight: 600;
    text-decoration: none;
  }
  .reg-footer a:hover { opacity: 0.7; }

  .reg-error {
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
    .reg-split { grid-template-columns: 1fr; }
    .reg-left { display: none; }
    .reg-right { min-height: calc(100vh - var(--header-height)); }
  }
</style>

<div class="reg-split">
  <div class="reg-left">
    <div class="reg-left-bg"></div>
    <div class="reg-left-content">
      <div class="reg-left-brand">ATELIER</div>
      <div class="reg-left-tagline">Join the Movement</div>
      <p class="reg-left-desc">
        Create your account and unlock access to exclusive collections, personalized recommendations, and member-only offers.
      </p>
    </div>
  </div>

  <div class="reg-right">
    <div class="reg-form-wrap">
      <div class="reg-form-header">
        <div class="brand">ATELIER</div>
        <h1>Create Account</h1>
        <p>Join us for a premium shopping experience</p>
      </div>

      <?php if ($error): ?>
        <div class="reg-error">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
          <?= sanitize($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="reg-form">
        <?= getCSRFInput() ?>
        <div class="form-row">
          <div class="field">
            <label>First Name <span class="req">*</span></label>
            <input type="text" name="first_name" required placeholder="John" value="<?= sanitize($_POST['first_name'] ?? '') ?>">
          </div>
          <div class="field">
            <label>Last Name <span class="req">*</span></label>
            <input type="text" name="last_name" required placeholder="Doe" value="<?= sanitize($_POST['last_name'] ?? '') ?>">
          </div>
        </div>
        <div class="form-row full">
          <div class="field">
            <label>Email Address <span class="req">*</span></label>
            <input type="email" name="email" required placeholder="you@example.com" value="<?= sanitize($_POST['email'] ?? '') ?>">
          </div>
        </div>
        <div class="form-row full">
          <div class="field">
            <label>Phone Number</label>
            <input type="tel" name="phone" placeholder="+91 XXXXX XXXXX" value="<?= sanitize($_POST['phone'] ?? '') ?>">
          </div>
        </div>
        <div class="form-row full">
          <div class="field">
            <label>Password <span class="req">*</span></label>
            <div class="password-field">
              <input type="password" name="password" required minlength="6" placeholder="Min. 6 characters">
              <button type="button" class="toggle-pass" onclick="togglePassword(this)">
                <svg class="eye-open" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="eye-closed" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
          </div>
        </div>
        <div class="form-row full">
          <div class="field">
            <label>Confirm Password <span class="req">*</span></label>
            <div class="password-field">
              <input type="password" name="confirm_password" required placeholder="Re-enter password">
              <button type="button" class="toggle-pass" onclick="togglePassword(this)">
                <svg class="eye-open" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="eye-closed" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
          </div>
        </div>
        <button type="submit" class="reg-submit">Create Account</button>
      </form>

      <div class="reg-footer">
        Already have an account? <a href="<?= BASE_URL ?>/customer/login.php">Sign In</a>
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
