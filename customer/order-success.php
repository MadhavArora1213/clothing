<?php
require_once dirname(__DIR__) . '/config/database.php';

$pageTitle = 'Order Confirmed — ATELIER';
include dirname(__DIR__) . '/includes/header.php';

$orderId = $_SESSION['last_order_id'] ?? null;
$order = null;
if ($orderId) {
  $order = $mysqli->query("SELECT * FROM orders WHERE id = $orderId")->fetch_assoc();
  unset($_SESSION['last_order_id']);
}
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div style="max-width: 600px; margin: 0 auto; text-align: center;">
      <?php if ($order): ?>
        <div style="width: 80px; height: 80px; background: #DCFCE7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-6); color: #166534;">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h1 style="font-family: var(--font-display); font-size: var(--text-h2); margin-bottom: var(--space-4);">Order Confirmed!</h1>
        <p style="color: var(--color-text-secondary); margin-bottom: var(--space-6);">Thank you for your purchase. Your order <strong><?= sanitize($order['order_number']) ?></strong> has been placed successfully.</p>
        <div class="admin-card" style="text-align: left; margin-bottom: var(--space-8);">
          <div style="padding: var(--space-6);">
            <p><strong>Order Number:</strong> <?= sanitize($order['order_number']) ?></p>
            <p><strong>Amount:</strong> <?= formatPrice($order['grand_total']) ?></p>
            <p><strong>Payment:</strong> <?= ucfirst($order['payment_method']) ?></p>
            <p><strong>Status:</strong> <span class="status-badge status-<?= $order['order_status'] ?>"><?= ucfirst($order['order_status']) ?></span></p>
          </div>
        </div>
        <div style="display: flex; gap: var(--space-4); justify-content: center; flex-wrap: wrap;">
          <a href="/customer/orders.php" class="btn btn-primary">View Orders</a>
          <a href="/shop.php" class="btn btn-secondary">Continue Shopping</a>
        </div>
      <?php else: ?>
        <h1 style="font-family: var(--font-display); font-size: var(--text-h2); margin-bottom: var(--space-4);">No Order Found</h1>
        <p style="color: var(--color-text-secondary); margin-bottom: var(--space-8);">It looks like you haven't placed an order yet.</p>
        <a href="/shop.php" class="btn btn-primary">Start Shopping</a>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
