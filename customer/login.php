<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle = 'Login — ATELIER';
$pageDescription = 'Sign in to your account.';
include dirname(__DIR__) . '/includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = sanitize($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
    $error = 'Please enter both email and password.';
  } else {
    $stmt = $mysqli->prepare('SELECT id, first_name, last_name, email, password, is_active FROM customers WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();

    if ($customer && password_verify($password, $customer['password'])) {
      if (!$customer['is_active']) {
        $error = 'Your account has been deactivated.';
      } else {
        $_SESSION['customer_id'] = $customer['id'];
        $_SESSION['customer_name'] = $customer['first_name'] . ' ' . $customer['last_name'];

        $update = $mysqli->prepare('UPDATE customers SET last_login = NOW() WHERE id = ?');
        $update->bind_param('i', $customer['id']);
        $update->execute();

        $redirect = $_GET['redirect'] ?? '/customer/account.php';
        redirect($redirect);
      }
    } else {
      $error = 'Invalid email or password.';
    }
  }
}
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div style="max-width: 420px; margin: 0 auto;">
      <div class="login-card">
        <a href="/" class="logo" style="text-align: center; display: block; margin-bottom: var(--space-8);">ATELIER</a>
        <h1 style="font-family: var(--font-display); font-size: var(--text-h2); text-align: center; margin-bottom: var(--space-2);">Welcome Back</h1>
        <p style="text-align: center; color: var(--color-text-secondary); margin-bottom: var(--space-8);">Sign in to your account</p>

        <?php if ($error): ?>
          <div class="alert alert-error"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="you@example.com" required value="<?= sanitize($_POST['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
          </div>
          <button type="submit" class="btn btn-primary btn-full">Sign In</button>
        </form>

        <p style="text-align: center; margin-top: var(--space-6); font-size: var(--text-body-sm); color: var(--color-text-secondary);">
          Don't have an account? <a href="/customer/register.php" style="color: var(--color-accent-primary); font-weight: 600;">Register</a>
        </p>
      </div>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
