<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle = 'Register — ATELIER';
$pageDescription = 'Create your account.';
include dirname(__DIR__) . '/includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $firstName = sanitize($_POST['first_name'] ?? '');
  $lastName = sanitize($_POST['last_name'] ?? '');
  $email = sanitize($_POST['email'] ?? '');
  $phone = sanitize($_POST['phone'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';

  if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
    $error = 'Please fill in all required fields.';
  } elseif ($password !== $confirmPassword) {
    $error = 'Passwords do not match.';
  } elseif (strlen($password) < 6) {
    $error = 'Password must be at least 6 characters.';
  } else {
    $stmt = $mysqli->prepare('SELECT id FROM customers WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
      $error = 'An account with this email already exists.';
    } else {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $mysqli->prepare('INSERT INTO customers (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)');
      $stmt->bind_param('sssss', $firstName, $lastName, $email, $phone, $hashedPassword);
      $stmt->execute();

      $_SESSION['customer_id'] = $mysqli->insert_id;
      $_SESSION['customer_name'] = $firstName . ' ' . $lastName;
      redirect('/customer/account.php');
    }
  }
}
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div style="max-width: 420px; margin: 0 auto;">
      <div class="login-card">
        <a href="/" class="logo" style="text-align: center; display: block; margin-bottom: var(--space-8);">ATELIER</a>
        <h1 style="font-family: var(--font-display); font-size: var(--text-h2); text-align: center; margin-bottom: var(--space-2);">Create Account</h1>
        <p style="text-align: center; color: var(--color-text-secondary); margin-bottom: var(--space-8);">Join us for a premium shopping experience</p>

        <?php if ($error): ?>
          <div class="alert alert-error"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="form-grid">
            <div class="form-group">
              <label>First Name <span class="required">*</span></label>
              <input type="text" name="first_name" required value="<?= sanitize($_POST['first_name'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Last Name <span class="required">*</span></label>
              <input type="text" name="last_name" required value="<?= sanitize($_POST['last_name'] ?? '') ?>">
            </div>
            <div class="form-group full-width">
              <label>Email Address <span class="required">*</span></label>
              <input type="email" name="email" required value="<?= sanitize($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group full-width">
              <label>Phone</label>
              <input type="tel" name="phone" value="<?= sanitize($_POST['phone'] ?? '') ?>">
            </div>
            <div class="form-group full-width">
              <label>Password <span class="required">*</span></label>
              <input type="password" name="password" required minlength="6">
            </div>
            <div class="form-group full-width">
              <label>Confirm Password <span class="required">*</span></label>
              <input type="password" name="confirm_password" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-full" style="margin-top: var(--space-4);">Create Account</button>
        </form>

        <p style="text-align: center; margin-top: var(--space-6); font-size: var(--text-body-sm); color: var(--color-text-secondary);">
          Already have an account? <a href="<?= BASE_URL ?>/customer/login.php" style="color: var(--color-accent-primary); font-weight: 600;">Sign in</a>
        </p>
      </div>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
