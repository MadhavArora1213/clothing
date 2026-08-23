<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php');
}

$pageTitle = 'My Orders — ATELIER';
include dirname(__DIR__) . '/includes/header.php';

$customerId = $_SESSION['customer_id'];
$orders = $mysqli->query("SELECT * FROM orders WHERE customer_id = $customerId ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div class="section-header">
      <h1 class="section-title">My Orders</h1>
    </div>

    <?php if (empty($orders)): ?>
      <div class="text-center" style="padding: 80px 0; color: var(--color-text-tertiary);">
        <p style="font-size: 18px; margin-bottom: var(--space-6);">You haven't placed any orders yet.</p>
        <a href="/shop.php" class="btn btn-primary">Start Shopping</a>
      </div>
    <?php else: ?>
      <div class="admin-card">
        <div class="table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $order): ?>
                <tr>
                  <td style="font-weight: 600;"><?= sanitize($order['order_number']) ?></td>
                  <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                  <td><?= formatPrice($order['grand_total']) ?></td>
                  <td><span class="status-badge status-<?= $order['payment_status'] ?>"><?= ucfirst($order['payment_status']) ?></span></td>
                  <td><span class="status-badge status-<?= $order['order_status'] ?>"><?= ucfirst(str_replace('_', ' ', $order['order_status'])) ?></span></td>
                  <td><a href="/customer/order-tracking.php?id=<?= $order['id'] ?>" class="btn btn-secondary btn-sm">Track Order</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
