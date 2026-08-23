<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php');
}

$pageTitle = 'My Account — ATELIER';
include dirname(__DIR__) . '/includes/header.php';

$customerId = $_SESSION['customer_id'];
$customer = $mysqli->query("SELECT * FROM customers WHERE id = $customerId")->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $firstName = sanitize($_POST['first_name'] ?? '');
  $lastName = sanitize($_POST['last_name'] ?? '');
  $phone = sanitize($_POST['phone'] ?? '');

  if (empty($firstName) || empty($lastName)) {
    $error = 'First and last name are required.';
  } else {
    $stmt = $mysqli->prepare('UPDATE customers SET first_name = ?, last_name = ?, phone = ? WHERE id = ?');
    $stmt->bind_param('sssi', $firstName, $lastName, $phone, $customerId);
    $stmt->execute();
    $_SESSION['customer_name'] = $firstName . ' ' . $lastName;
    $success = 'Profile updated successfully.';
    $customer['first_name'] = $firstName;
    $customer['last_name'] = $lastName;
    $customer['phone'] = $phone;
  }
}

$addresses = $mysqli->query("SELECT * FROM addresses WHERE customer_id = $customerId ORDER BY is_default DESC, created_at DESC")->fetch_all(MYSQLI_ASSOC);

$orders = $mysqli->query("SELECT * FROM orders WHERE customer_id = $customerId ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div class="section-header">
      <h1 class="section-title">My Account</h1>
      <p class="section-subtitle">Welcome, <?= sanitize($customer['first_name']) ?>.</p>
    </div>

    <div class="admin-grid">
      <div>
        <div class="admin-card" style="margin-bottom: var(--space-6);">
          <div class="admin-card-header"><h2>Profile Information</h2></div>
          <div style="padding: var(--space-6);">
            <?php if ($error): ?>
              <div class="alert alert-error" style="margin-bottom: var(--space-4);"><?= sanitize($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
              <div class="alert alert-success" style="margin-bottom: var(--space-4);"><?= sanitize($success) ?></div>
            <?php endif; ?>
            <form method="POST">
              <div class="form-grid">
                <div class="form-group">
                  <label>First Name</label>
                  <input type="text" name="first_name" value="<?= sanitize($customer['first_name']) ?>">
                </div>
                <div class="form-group">
                  <label>Last Name</label>
                  <input type="text" name="last_name" value="<?= sanitize($customer['last_name']) ?>">
                </div>
                <div class="form-group full-width">
                  <label>Email</label>
                  <input type="email" value="<?= sanitize($customer['email']) ?>" disabled>
                </div>
                <div class="form-group full-width">
                  <label>Phone</label>
                  <input type="tel" name="phone" value="<?= sanitize($customer['phone'] ?? '') ?>">
                </div>
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Profile</button>
              </div>
            </form>
          </div>
        </div>

        <div class="admin-card">
          <div class="admin-card-header"><h2>Saved Addresses</h2></div>
          <div style="padding: var(--space-6);">
            <?php if (empty($addresses)): ?>
              <p style="color: var(--color-text-tertiary);">No saved addresses yet.</p>
            <?php else: ?>
              <?php foreach ($addresses as $address): ?>
                <div style="padding: var(--space-4); border: 1px solid var(--color-accent-tertiary); border-radius: var(--radius-md); margin-bottom: var(--space-3);">
                  <strong><?= sanitize($address['label']) ?></strong>
                  <p style="margin-top: var(--space-2); color: var(--color-text-secondary); font-size: var(--text-body-sm);">
                    <?= sanitize($address['full_name']) ?><br>
                    <?= sanitize($address['address_line1']) ?><br>
                    <?= sanitize($address['city']) ?>, <?= sanitize($address['state']) ?> - <?= sanitize($address['postal_code']) ?><br>
                    <?= sanitize($address['phone']) ?>
                  </p>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div>
        <div class="admin-card">
          <div class="admin-card-header"><h2>Recent Orders</h2></div>
          <div class="table-wrap">
            <table class="admin-table">
              <thead>
                <tr><th>Order #</th><th>Amount</th><th>Status</th><th>Date</th></tr>
              </thead>
              <tbody>
                <?php if (empty($orders)): ?>
                  <tr><td colspan="4" style="text-align: center; padding: 40px; color: var(--color-text-tertiary);">No orders yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($orders as $order): ?>
                    <tr>
                      <td><a href="/customer/orders.php?id=<?= $order['id'] ?>"><?= sanitize($order['order_number']) ?></a></td>
                      <td><?= formatPrice($order['grand_total']) ?></td>
                      <td><span class="status-badge status-<?= $order['order_status'] ?>"><?= ucfirst(str_replace('_', ' ', $order['order_status'])) ?></span></td>
                      <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div style="padding: var(--space-4) var(--space-6); border-top: 1px solid var(--color-bg-elevated);">
            <a href="/customer/orders.php" class="btn btn-secondary" style="width: 100%;">View All Orders</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
