<?php
require_once dirname(__DIR__) . '/config/database.php';

$error = '';
$success = '';
$emailSent = false;
$resetToken = $_GET['token'] ?? '';

// Step 1: Request password reset (email form)
// Step 2: Validate token and show password form
// Step 3: Reset password with valid token

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $error = 'Invalid request. Please try again.';
  } elseif (rateLimit('password_reset', 3, 300)) {
    $error = 'Too many reset attempts. Please wait a few minutes.';
  } else {
    $action = $_POST['action'] ?? '';

    if ($action === 'request_reset') {
      // Step 1: User submits email, generate token, "send" email
      $email = sanitize($_POST['email'] ?? '');
      if (empty($email) || !validateEmail($email)) {
        $error = 'Please enter a valid email address.';
      } else {
        $stmt = $mysqli->prepare('SELECT id FROM customers WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();

        if ($customer) {
          // Generate secure token
          $token = bin2hex(random_bytes(32));
          $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

          // Delete any existing tokens for this customer
          $del = $mysqli->prepare('DELETE FROM password_reset_tokens WHERE customer_id = ?');
          $del->bind_param('i', $customer['id']);
          $del->execute();

          // Store token
          $ins = $mysqli->prepare('INSERT INTO password_reset_tokens (customer_id, token, expires_at) VALUES (?, ?, ?)');
          $ins->bind_param('isi', $customer['id'], $token, $expiry);
          $ins->execute();

          // In production, send email here. For now, show the token link.
          $resetUrl = BASE_URL . '/customer/forgot-password.php?token=' . $token;
          $success = 'Password reset link generated. In production, this would be emailed. For testing: <a href="' . $resetUrl . '" style="color:#D4AF37;font-weight:600;">Click here to reset password</a>';
          $emailSent = true;
        } else {
          // Don't reveal if email exists
          $success = 'If an account with that email exists, a password reset link has been sent.';
          $emailSent = true;
        }
      }
    } elseif ($action === 'reset_password') {
      // Step 3: User submits new password with valid token
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
        // Validate token
        $stmt = $mysqli->prepare('SELECT customer_id, expires_at FROM password_reset_tokens WHERE token = ?');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $tokenData = $stmt->get_result()->fetch_assoc();

        if (!$tokenData) {
          $error = 'Invalid or expired reset token.';
        } elseif (strtotime($tokenData['expires_at']) < time()) {
          $error = 'Reset token has expired. Please request a new one.';
          // Delete expired token
          $del = $mysqli->prepare('DELETE FROM password_reset_tokens WHERE token = ?');
          $del->bind_param('s', $token);
          $del->execute();
        } else {
          // Valid token - reset password
          $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
          $update = $mysqli->prepare('UPDATE customers SET password = ? WHERE id = ?');
          $update->bind_param('si', $hashed, $tokenData['customer_id']);
          $update->execute();

          // Delete used token
          $del = $mysqli->prepare('DELETE FROM password_reset_tokens WHERE token = ?');
          $del->bind_param('s', $token);
          $del->execute();

          $success = 'Password reset successfully! You can now login with your new password.';
        }
      }
    }
  }
}

// Check if we have a valid token in URL (show password form)
$validToken = false;
if (!empty($resetToken) && empty($error)) {
  $stmt = $mysqli->prepare('SELECT customer_id, expires_at FROM password_reset_tokens WHERE token = ?');
  $stmt->bind_param('s', $resetToken);
  $stmt->execute();
  $tokenData = $stmt->get_result()->fetch_assoc();
  if ($tokenData && strtotime($tokenData['expires_at']) >= time()) {
    $validToken = true;
  } else {
    $error = 'Invalid or expired reset token.';
  }
}

$pageTitle    = 'Reset Password — Urban Outfit Collection';
$pageDescription = 'Reset your Urban Outfit Collection account password securely.';
$pageRobots   = 'noindex, nofollow';
include dirname(__DIR__) . '/includes/header.php';
?>

<main class="page-shell">
  <div class="container">
    <div class="auth-layout reveal-up">
      <div class="auth-visual">
        <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1200&h=1500&fit=crop" alt="Fashion reset visual" loading="eager">
        <div class="auth-visual-copy">
          <strong>RESET PASSWORD</strong>
          <span>Don't worry, we'll help you get back in.</span>
        </div>
      </div>
      <div>
      <div class="login-card">
        <a href="/" class="logo" style="text-align: center; display: block; margin-bottom: var(--space-8);">YOUR BRAND</a>
        <h1 style="font-family: var(--font-display); font-size: var(--text-h2); text-align: center; margin-bottom: var(--space-2);">Forgot Password</h1>
        <p style="text-align: center; color: var(--color-text-secondary); margin-bottom: var(--space-8);">
          <?= $validToken ? 'Enter your new password' : 'Enter your email to receive a reset link' ?>
        </p>

        <?php if ($error): ?>
          <div class="alert alert-error"><?= sanitize($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($validToken): ?>
          <!-- Step 3: Password Reset Form -->
          <form method="POST" action="">
            <?= getCSRFInput() ?>
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" name="reset_token" value="<?= sanitize($resetToken) ?>">
            <div class="form-group">
              <label>New Password</label>
              <input type="password" name="new_password" placeholder="Min. 6 characters" required minlength="6">
            </div>
            <div class="form-group">
              <label>Confirm Password</label>
              <input type="password" name="confirm_password" placeholder="Confirm new password" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary btn-full">Reset Password</button>
          </form>
        <?php elseif ($emailSent): ?>
          <!-- Email sent confirmation -->
          <p style="text-align: center; margin-top: var(--space-4); font-size: var(--text-body-sm); color: var(--color-text-secondary);">
            Check your email for the reset link. <?= $success ?>
          </p>
        <?php else: ?>
          <!-- Step 1: Email Request Form -->
          <form method="POST" action="">
            <?= getCSRFInput() ?>
            <input type="hidden" name="action" value="request_reset">
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" placeholder="you@example.com" required value="<?= sanitize($_POST['email'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-full">Send Reset Link</button>
          </form>
        <?php endif; ?>

        <p style="text-align: center; margin-top: var(--space-6); font-size: var(--text-body-sm); color: var(--color-text-secondary);">
          Remember your password? <a href="<?= BASE_URL ?>/customer/login.php" style="color: var(--color-accent-primary); font-weight: 600;">Login</a>
        </p>
      </div>
      </div>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
