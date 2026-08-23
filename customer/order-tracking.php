<?php
require_once dirname(__DIR__) . '/config/database.php';

if (!isset($_SESSION['customer_id'])) {
  redirect('/customer/login.php');
}

$pageTitle = 'Order Tracking — ATELIER';
include dirname(__DIR__) . '/includes/header.php';

$customerId = $_SESSION['customer_id'];
$orderId = (int)($_GET['id'] ?? 0);

$stmt = $mysqli->prepare('SELECT * FROM orders WHERE id = ? AND customer_id = ?');
$stmt->bind_param('ii', $orderId, $customerId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
  redirect('/customer/orders.php');
}

$items = $mysqli->query("SELECT * FROM order_items WHERE order_id = $orderId")->fetch_all(MYSQLI_ASSOC);
$history = $mysqli->query("SELECT * FROM order_status_history WHERE order_id = $orderId ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div class="section-header">
      <h1 class="section-title">Track Order</h1>
      <p class="section-subtitle">Order <?= sanitize($order['order_number']) ?></p>
    </div>

    <div class="admin-grid">
      <div>
        <div class="admin-card" style="margin-bottom: var(--space-6);">
          <div class="admin-card-header"><h2>Order Status</h2></div>
          <div style="padding: var(--space-6);">
            <div style="display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-4);">
              <span class="status-badge status-<?= $order['order_status'] ?>" style="font-size: var(--text-body); padding: var(--space-2) var(--space-4);"><?= ucfirst(str_replace('_', ' ', $order['order_status'])) ?></span>
              <span style="color: var(--color-text-tertiary);"><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
            </div>
            <?php if ($order['tracking_number']): ?>
              <p><strong>Tracking Number:</strong> <?= sanitize($order['tracking_number']) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <div class="admin-card">
          <div class="admin-card-header"><h2>Status History</h2></div>
          <div class="table-wrap">
            <table class="admin-table">
              <thead><tr><th>Status</th><th>Date</th></tr></thead>
              <tbody>
                <?php if (empty($history)): ?>
                  <tr><td colspan="2" style="text-align: center; padding: 40px; color: var(--color-text-tertiary);">No history yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($history as $h): ?>
                    <tr>
                      <td><span class="status-badge status-<?= $h['status'] ?>"><?= ucfirst(str_replace('_', ' ', $h['status'])) ?></span></td>
                      <td><?= date('M d, Y h:i A', strtotime($h['created_at'])) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div>
        <div class="admin-card" style="margin-bottom: var(--space-6);">
          <div class="admin-card-header"><h2>Shipping Address</h2></div>
          <div style="padding: var(--space-6);">
            <?php
              $address = json_decode($order['shipping_address'], true);
              if ($address):
            ?>
              <p><?= sanitize($address['name']) ?></p>
              <p><?= sanitize($address['address']) ?></p>
              <p><?= sanitize($address['city']) ?>, <?= sanitize($address['state']) ?> - <?= sanitize($address['postal_code']) ?></p>
              <p><?= sanitize($address['phone']) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <div class="admin-card">
          <div class="admin-card-header"><h2>Order Items</h2></div>
          <div class="table-wrap">
            <table class="admin-table">
              <thead><tr><th>Product</th><th>Qty</th><th>Total</th></tr></thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td><?= sanitize($item['product_name']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td><?= formatPrice($item['total_price']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div style="padding: var(--space-4) var(--space-6); border-top: 1px solid var(--color-bg-elevated); display: flex; justify-content: space-between; font-weight: 700;">
            <span>Total</span>
            <span><?= formatPrice($order['grand_total']) ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
