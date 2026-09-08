<?php
require_once dirname(__DIR__) . '/config/database.php';

$error = '';
$success = '';
$emailSent = false;
$resetToken = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $error = 'Invalid request. Please try again.';
  } elseif (rateLimit('password_reset', 3, 300)) {
    $error = 'Too many reset attempts. Please wait a few minutes.';
  } else {
    $action = $_POST['action'] ?? '';

    if ($action === 'request_reset') {
      $email = sanitize($_POST['email'] ?? '');
      if (empty($email) || !validateEmail($email)) {
        $error = 'Please enter a valid email address.';
      } else {
        $stmt = $mysqli->prepare('SELECT id FROM customers WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();

        if ($customer) {
          // Ensure password_reset_tokens table exists
          $checkTable = $mysqli->query("SHOW TABLES LIKE 'password_reset_tokens'");
          if ($checkTable && $checkTable->num_rows === 0) {
            $mysqli->query("CREATE TABLE IF NOT EXISTS password_reset_tokens (
              id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              customer_id INT UNSIGNED NOT NULL,
              token VARCHAR(64) NOT NULL UNIQUE,
              expires_at DATETIME NOT NULL,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
              INDEX idx_token (token),
              INDEX idx_customer (customer_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
          }

          $token = bin2hex(random_bytes(32));
          $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

          $del = $mysqli->prepare('DELETE FROM password_reset_tokens WHERE customer_id = ?');
          $del->bind_param('i', $customer['id']);
          $del->execute();

          $ins = $mysqli->prepare('INSERT INTO password_reset_tokens (customer_id, token, expires_at) VALUES (?, ?, ?)');
          if (!$ins) {
            error_log('forgot-password: INSERT prepare failed: ' . $mysqli->error);
            $error = 'A system error occurred. Please try again.';
          } else {
            $ins->bind_param('iss', $customer['id'], $token, $expiry);
            if (!$ins->execute()) {
              error_log('forgot-password: INSERT execute failed: ' . $ins->error);
              $error = 'A system error occurred. Please try again.';
            } else {
              $resetUrl = BASE_URL . '/customer/forgot-password.php?token=' . $token;

          // Send email
          $emailSubject = 'Reset Your Password — Urban Outfit Collection';
          $emailHtml = '
          <!DOCTYPE html>
          <html>
          <head><meta charset="UTF-8"></head>
          <body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
            <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
              <div style="background:#0f172a;padding:30px;text-align:center;">
                <h1 style="color:#000;font-size:24px;margin:0;">Urban Outfit Collection</h1>
              </div>
              <div style="padding:30px;">
                <h2 style="color:#0f172a;font-size:20px;margin:0 0 16px;">Reset Your Password</h2>
                <p style="color:#555;font-size:14px;line-height:1.6;">We received a request to reset the password for your account. Click the button below to set a new password. This link will expire in 1 hour.</p>
                <div style="text-align:center;margin:30px 0;">
                  <a href="' . $resetUrl . '" style="display:inline-block;padding:14px 32px;background:#000;color:#fff;font-weight:700;text-decoration:none;border-radius:8px;font-size:14px;">Reset Password</a>
                </div>
                <p style="color:#999;font-size:12px;line-height:1.5;">If you did not request a password reset, please ignore this email. Your password will remain unchanged.</p>
                <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
                <p style="color:#aaa;font-size:11px;text-align:center;">Urban Outfit Collection — Fashion E-Commerce</p>
              </div>
            </div>
          </body>
          </html>';

          $emailText = "Reset Your Password\n\nClick this link to reset: " . $resetUrl . "\n\nThis link expires in 1 hour.";

          $emailSent = sendEmail($email, $emailSubject, $emailHtml, $emailText);

              if ($emailSent) {
                $success = 'A password reset link has been sent to your email address. Please check your inbox.';
              } else {
                $success = 'Email could not be sent. Use this link to reset: <a href="' . $resetUrl . '" style="color:#000;font-weight:600;text-decoration:underline;">Click here to reset password</a>';
              }
              $emailSent = true;
            }
          }
        } else {
          $success = 'If an account with that email exists, a password reset link has been sent.';
          $emailSent = true;
        }
      }
    } elseif ($action === 'reset_password') {
      $token = $_POST['reset_token'] ?? '';
      $newPassword = $_POST['new_password'] ?? '';
      $confirmPassword = $_POST['confirm_password'] ?? '';

      if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'Please fill in all fields.';
      } elseif (strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters.';
      } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
      } else {
        $stmt = $mysqli->prepare('SELECT customer_id, expires_at FROM password_reset_tokens WHERE token = ?');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $tokenData = $stmt->get_result()->fetch_assoc();

        if (!$tokenData) {
          $error = 'Invalid or expired reset token.';
        } elseif (strtotime($tokenData['expires_at']) < time()) {
          $error = 'Reset token has expired. Please request a new one.';
          $del = $mysqli->prepare('DELETE FROM password_reset_tokens WHERE token = ?');
          $del->bind_param('s', $token);
          $del->execute();
        } else {
          $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
          $update = $mysqli->prepare('UPDATE customers SET password = ? WHERE id = ?');
          $update->bind_param('si', $hashed, $tokenData['customer_id']);
          $update->execute();

          $del = $mysqli->prepare('DELETE FROM password_reset_tokens WHERE token = ?');
          $del->bind_param('s', $token);
          $del->execute();

          $_SESSION['flash_success'] = 'Password reset successfully! You can now login with your new password.';
          redirect('/customer/login.php');
        }
      }
    }
  }
}

$validToken = false;
if (!empty($resetToken)) {
  if (!$mysqli) {
    $error = 'Database connection failed.';
  } else {
    // Ensure table exists
    $checkTable = $mysqli->query("SHOW TABLES LIKE 'password_reset_tokens'");
    if ($checkTable && $checkTable->num_rows === 0) {
      $mysqli->query("CREATE TABLE IF NOT EXISTS password_reset_tokens (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_id INT UNSIGNED NOT NULL,
        token VARCHAR(64) NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_token (token),
        INDEX idx_customer (customer_id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    $stmt = $mysqli->prepare('SELECT customer_id, expires_at FROM password_reset_tokens WHERE token = ?');
    if (!$stmt) {
      error_log('forgot-password: token query prepare failed: ' . $mysqli->error);
      $error = 'A system error occurred.';
    } else {
      $stmt->bind_param('s', $resetToken);
      $stmt->execute();
      $tokenData = $stmt->get_result()->fetch_assoc();
      if ($tokenData && strtotime($tokenData['expires_at']) >= time()) {
        $validToken = true;
      } else {
        error_log('forgot-password: token not found or expired. Token length=' . strlen($resetToken));
        $error = 'Invalid or expired reset token.';
      }
    }
  }
}

$pageTitle    = 'Reset Password — Urban Outfit Collection';
$pageDescription = 'Reset your Urban Outfit Collection account password securely.';
$pageRobots   = 'noindex, nofollow';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
  .reset-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: calc(100vh - var(--header-height));
    margin-top: calc(-1 * var(--space-6));
  }
  .reset-left {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    min-height: 600px;
    background: var(--color-text-main);
  }
  .reset-left-content {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: var(--space-10);
    max-width: 400px;
  }
  .reset-left-brand {
    font-family: var(--font-display);
    font-size: clamp(36px, 4vw, 52px);
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.04em;
    margin-bottom: var(--space-3);
  }
  .reset-left-tagline {
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
  .reset-left-tagline::before,
  .reset-left-tagline::after {
    content: '';
    width: 32px;
    height: 1px;
    background: var(--color-accent);
  }
  .reset-left-desc {
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    line-height: 1.7;
  }
  .reset-right {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-10);
    background: var(--color-bg);
  }
  .reset-form-wrap {
    width: 100%;
    max-width: 420px;
  }
  .reset-form-header {
    text-align: center;
    margin-bottom: var(--space-8);
  }
  .reset-form-header .brand {
    font-family: var(--font-display);
    font-size: 22px;
    font-weight: 700;
    color: var(--color-text-main);
    margin-bottom: var(--space-2);
    letter-spacing: 0.02em;
  }
  .reset-form-header h1 {
    font-family: var(--font-display);
    font-size: 28px;
    font-weight: 700;
    color: var(--color-text-main);
    margin-bottom: var(--space-2);
  }
  .reset-form-header p {
    color: var(--color-text-muted);
    font-size: 14px;
  }
  .reset-form .form-group {
    margin-bottom: var(--space-4);
  }
  .reset-form .form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--color-text-main);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 6px;
  }
  .reset-form .form-group input {
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
  .reset-form .form-group input:focus {
    outline: none;
    border-color: var(--color-accent);
    box-shadow: 0 0 0 3px rgba(0,0,0,0.12);
  }
  .reset-form .form-group input::placeholder {
    color: var(--color-text-muted);
  }
  .reset-submit {
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
  .reset-submit:hover { background: #333; transform: translateY(-1px); }
  .reset-footer {
    text-align: center;
    margin-top: var(--space-6);
    font-size: 13px;
    color: var(--color-text-muted);
  }
  .reset-footer a {
    color: var(--color-accent);
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
  }
  .reset-footer a:hover { opacity: 0.7; }
  .reset-error {
    background: #FEF2F2;
    border: 1px solid #FECACA;
    color: #991B1B;
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    margin-bottom: var(--space-4);
  }
  .reset-success {
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    color: #166534;
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    margin-bottom: var(--space-4);
    line-height: 1.6;
  }
  .reset-success a { color: #166534; font-weight: 700; text-decoration: underline; }

  @media (max-width: 900px) {
    .reset-split { grid-template-columns: 1fr; }
    .reset-left { display: none; }
    .reset-right { min-height: calc(100vh - var(--header-height)); }
  }
</style>

<div class="reset-split">
  <!-- Left: Brand Panel -->
  <div class="reset-left">
    <div class="reset-left-content">
      <div class="reset-left-brand">UOC</div>
      <div class="reset-left-tagline">Urban Outfit Collection</div>
      <p class="reset-left-desc">
        Don't worry, it happens to the best of us. Enter your email and we'll help you get back into your account.
      </p>
    </div>
  </div>

  <!-- Right: Reset Form -->
  <div class="reset-right">
    <div class="reset-form-wrap">
      <div class="reset-form-header">
        <div class="brand">UOC</div>
        <h1>Forgot Password</h1>
        <p><?= $validToken ? 'Enter your new password below' : 'Enter your email to receive a reset link' ?></p>
      </div>

      <?php if ($error): ?>
        <div class="reset-error"><?= esc($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="reset-success"><?= $success ?></div>
      <?php endif; ?>

      <?php if ($validToken): ?>
        <!-- Step 2: Password Reset Form -->
        <form method="POST" class="reset-form">
          <?= getCSRFInput() ?>
          <input type="hidden" name="action" value="reset_password">
          <input type="hidden" name="reset_token" value="<?= esc($resetToken) ?>">
          <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Min. 6 characters" required minlength="6">
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required minlength="6">
          </div>
          <button type="submit" class="reset-submit">Reset Password</button>
        </form>
      <?php elseif ($emailSent): ?>
        <!-- Step 3: Confirmation -->
        <div style="text-align: center; padding: var(--space-6) 0;">
          <svg width="48" height="48" fill="none" stroke="#16A34A" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 16px;"><path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
          <p style="color: var(--color-text-muted); font-size: 14px; line-height: 1.7;">Check your email for the reset link.</p>
        </div>
      <?php else: ?>
        <!-- Step 1: Email Request Form -->
        <form method="POST" class="reset-form">
          <?= getCSRFInput() ?>
          <input type="hidden" name="action" value="request_reset">
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="you@example.com" required value="<?= esc($_POST['email'] ?? '') ?>">
          </div>
          <button type="submit" class="reset-submit">Send Reset Link</button>
        </form>
      <?php endif; ?>

      <div class="reset-footer">
        Remember your password? <a href="<?= BASE_URL ?>/customer/login.php">Login</a>
      </div>
    </div>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
