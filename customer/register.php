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
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $_SESSION['pending_registration'] = [
              'first_name' => $firstName,
              'last_name'  => $lastName,
              'email'      => $email,
              'phone'      => $phone,
              'password'   => password_hash($password, PASSWORD_DEFAULT),
              'otp'        => $otp,
              'expires_at' => time() + 600,
            ];

            $otpHtml = '
            <!DOCTYPE html>
            <html>
            <head><meta charset="UTF-8"></head>
            <body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
              <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
                <div style="background:#000;padding:30px;text-align:center;">
                  <h1 style="color:#fff;font-size:24px;margin:0;">Urban Outfit Collection</h1>
                </div>
                <div style="padding:30px;text-align:center;">
                  <h2 style="color:#000;font-size:20px;margin:0 0 16px;">Verify Your Email</h2>
                  <p style="color:#555;font-size:14px;line-height:1.6;">Use the following OTP to verify your account. This code expires in 10 minutes.</p>
                  <div style="margin:30px 0;">
                    <span style="display:inline-block;font-size:36px;font-weight:700;letter-spacing:12px;color:#000;background:#f8f8f8;padding:16px 28px;border-radius:8px;border:2px dashed #000;">' . $otp . '</span>
                  </div>
                  <p style="color:#999;font-size:12px;line-height:1.5;">If you did not create an account, please ignore this email.</p>
                  <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
                  <p style="color:#aaa;font-size:11px;text-align:center;">Urban Outfit Collection — Mukerian, Punjab</p>
                </div>
              </div>
            </body>
            </html>';

            $otpText = "Your OTP for Urban Outfit Collection: {$otp}\nThis code expires in 10 minutes.";
            sendEmail($email, 'Verify Your Email — Urban Outfit Collection', $otpHtml, $otpText);

            redirect('/customer/verify-otp.php');
          }
        }
      }
  }
}

$pageTitle       = 'Create Account — Urban Outfit Collection';
$pageDescription = 'Create your free account to track orders, save your wishlist and enjoy faster checkout.';
$pageRobots      = 'noindex, nofollow';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ====================== REGISTER PAGE ====================== */
.reg-page {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: calc(100vh - var(--header-height, 70px));
}

/* Left Panel */
.reg-left {
  background: #000;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px;
  position: relative;
  overflow: hidden;
}
.reg-left::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -20%;
  width: 140%;
  height: 200%;
  background: radial-gradient(ellipse at center, rgba(255,255,255,0.03) 0%, transparent 60%);
  pointer-events: none;
}
.reg-left-content {
  position: relative;
  z-index: 2;
  text-align: center;
  max-width: 320px;
}
.reg-brand {
  font-family: 'Inter', var(--font-body);
  font-size: 48px;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.03em;
  margin-bottom: 8px;
}
.reg-tagline {
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
.reg-tagline::before,
.reg-tagline::after {
  content: '';
  width: 24px;
  height: 1px;
  background: rgba(255,255,255,0.2);
}
.reg-desc {
  font-size: 14px;
  color: rgba(255,255,255,0.5);
  line-height: 1.7;
  margin-bottom: 40px;
}
.reg-features {
  display: flex;
  gap: 32px;
  justify-content: center;
}
.reg-feature {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}
.reg-feature-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
}
.reg-feature-icon svg { width: 18px; height: 18px; stroke: #fff; }
.reg-feature span {
  font-size: 11px;
  color: rgba(255,255,255,0.5);
  font-weight: 500;
}

/* Right Panel */
.reg-right {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px;
  background: #fff;
  overflow-y: auto;
}
.reg-form-wrap {
  width: 100%;
  max-width: 380px;
}
.reg-form-header {
  text-align: center;
  margin-bottom: 32px;
}
.reg-form-header h1 {
  font-family: 'Inter', var(--font-body);
  font-size: 28px;
  font-weight: 800;
  color: #000;
  margin: 0 0 8px;
  letter-spacing: -0.02em;
}
.reg-form-header p {
  font-size: 14px;
  color: #999;
  margin: 0;
}

/* Form */
.reg-form .form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 16px;
}
.reg-form .form-row.full { grid-template-columns: 1fr; }
.reg-form .field label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  color: #000;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 6px;
}
.reg-form .field label .req { color: #DC2626; }
.reg-form .field input {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #e5e5e5;
  border-radius: 10px;
  font-size: 14px;
  color: #000;
  background: #fafafa;
  transition: all 0.2s;
  font-family: 'Inter', var(--font-body);
  box-sizing: border-box;
}
.reg-form .field input:focus {
  outline: none;
  border-color: #000;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(0,0,0,0.06);
}
.reg-form .field input::placeholder { color: #bbb; }

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
.reg-submit {
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
  margin-top: 8px;
}
.reg-submit:hover {
  background: #222;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Footer */
.reg-footer {
  text-align: center;
  margin-top: 24px;
  font-size: 13px;
  color: #999;
}
.reg-footer a {
  color: #000;
  font-weight: 700;
  text-decoration: none;
  transition: opacity 0.2s;
}
.reg-footer a:hover { opacity: 0.6; }

/* Error */
.reg-error {
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

/* Responsive */
@media (max-width: 900px) {
  .reg-page { grid-template-columns: 1fr; }
  .reg-left { display: none; }
  .reg-right {
    min-height: calc(100vh - var(--header-height, 70px));
    padding: 32px 24px;
  }
}
@media (max-width: 480px) {
  .reg-right { padding: 24px 16px; }
  .reg-form .form-row { grid-template-columns: 1fr; }
}
</style>

<div class="reg-page">
  <!-- Left: Brand -->
  <div class="reg-left">
    <div class="reg-left-content">
      <div class="reg-brand">UOC</div>
      <div class="reg-tagline">Join the Movement</div>
      <p class="reg-desc">
        Create your account to unlock exclusive collections, personalized recommendations, and member-only offers.
      </p>
      <div class="reg-features">
        <div class="reg-feature">
          <div class="reg-feature-icon">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          </div>
          <span>Premium Quality</span>
        </div>
        <div class="reg-feature">
          <div class="reg-feature-icon">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          </div>
          <span>Free Shipping</span>
        </div>
        <div class="reg-feature">
          <div class="reg-feature-icon">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/></svg>
          </div>
          <span>Easy Returns</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Form -->
  <div class="reg-right">
    <div class="reg-form-wrap">
      <div class="reg-form-header">
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
