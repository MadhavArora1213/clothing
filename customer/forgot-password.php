<?php
require_once dirname(__DIR__) . '/config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = sanitize($_POST['email'] ?? '');
  $newPassword = $_POST['new_password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';

  if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
    $error = 'Please fill in all fields.';
  } elseif (strlen($newPassword) < 6) {
    $error = 'Password must be at least 6 characters.';
  } elseif ($newPassword !== $confirmPassword) {
    $error = 'Passwords do not match.';
  } else {
    $stmt = $mysqli->prepare('SELECT id FROM customers WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();

    if ($customer) {
      $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
      $update = $mysqli->prepare('UPDATE customers SET password = ? WHERE id = ?');
      $update->bind_param('si', $hashed, $customer['id']);
      $update->execute();
      $success = 'Password reset successfully! You can now login with your new password.';
    } else {
      $error = 'No account found with that email address.';
    }
  }
}

$pageTitle = 'Forgot Password';
$pageDescription = 'Reset your account password.';
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
        <p style="text-align: center; color: var(--color-text-secondary); margin-bottom: var(--space-8);">Enter your email and set a new password</p>

        <?php if ($error): ?>
          <div class="alert alert-error"><?= sanitize($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success"><?= sanitize($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="you@example.com" required value="<?= sanitize($_POST['email'] ?? '') ?>">
          </div>
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

        <p style="text-align: center; margin-top: var(--space-6); font-size: var(--text-body-sm); color: var(--color-text-secondary);">
          Remember your password? <a href="<?= BASE_URL ?>/customer/login.php" style="color: var(--color-accent-primary); font-weight: 600;">Login</a>
        </p>
      </div>
      </div>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
