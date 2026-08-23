<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
$stmt = $mysqli->prepare('SELECT o.*, c.first_name, c.last_name, c.email, c.phone FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) redirect('/admin/orders/');

$items = $mysqli->query("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $id")->fetch_all(MYSQLI_ASSOC);

$history = $mysqli->query("SELECT * FROM order_status_history WHERE order_id = $id ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newStatus = sanitize($_POST['order_status'] ?? '');
  $trackingNumber = sanitize($_POST['tracking_number'] ?? '');
  $note = sanitize($_POST['note'] ?? '');

  if (!empty($newStatus) && $newStatus !== $order['order_status']) {
    $stmt = $mysqli->prepare('UPDATE orders SET order_status = ?, tracking_number = ?, updated_at = NOW() WHERE id = ?');
    $stmt->bind_param('ssi', $newStatus, $trackingNumber, $id);
    $stmt->execute();

    $adminId = $_SESSION['admin_id'];
    $stmt = $mysqli->prepare('INSERT INTO order_status_history (order_id, status, note, created_by) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('issi', $id, $newStatus, $note, $adminId);
    $stmt->execute();

    $success = 'Order status updated successfully.';
    $order['order_status'] = $newStatus;
    $order['tracking_number'] = $trackingNumber;
  }
}

$pageTitle = 'Order ' . $order['order_number'] . ' — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Order <?= sanitize($order['order_number']) ?></h1>
      <p style="color: var(--color-text-secondary);">Placed on <?= date('F d, Y \\a\\t h:i A', strtotime($order['created_at'])) ?></p>
    </div>
    <a href="/admin/orders/" class="btn btn-secondary">&larr; Back to Orders</a>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom: var(--space-6);"><?= sanitize($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6);"><?= sanitize($success) ?></div>
  <?php endif; ?>

  <div class="admin-grid">
    <div class="admin-card">
      <div class="admin-card-header"><h2>Customer Details</h2></div>
      <div style="padding: var(--space-6);">
        <p><strong>Name:</strong> <?= sanitize($order['customer_name']) ?></p>
        <p><strong>Email:</strong> <?= sanitize($order['customer_email']) ?></p>
        <p><strong>Phone:</strong> <?= sanitize($order['customer_phone']) ?></p>
      </div>
    </div>

    <div class="admin-card">
      <div class="admin-card-header"><h2>Order Summary</h2></div>
      <div style="padding: var(--space-6);">
        <p><strong>Payment:</strong> <?= ucfirst($order['payment_method']) ?></p>
        <p><strong>Payment Status:</strong> <span class="status-badge status-<?= $order['payment_status'] ?>"><?= ucfirst($order['payment_status']) ?></span></p>
        <p><strong>Subtotal:</strong> <?= formatPrice($order['subtotal']) ?></p>
        <p><strong>Discount:</strong> <?= formatPrice($order['discount_amount']) ?></p>
        <p><strong>Shipping:</strong> <?= formatPrice($order['shipping_amount']) ?></p>
        <p><strong>Grand Total:</strong> <?= formatPrice($order['grand_total']) ?></p>
      </div>
    </div>
  </div>

  <div class="admin-card" style="margin-top: var(--space-6);">
    <div class="admin-card-header"><h2>Order Items</h2></div>
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>Product</th><th>SKU</th><th>Size</th><th>Qty</th><th>Price</th><th>Total</th></tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td style="display: flex; align-items: center; gap: 12px;">
                <img src="<?= $item['image'] ?? '' ?>" alt="" style="width: 40px; height: 52px; object-fit: cover; border-radius: var(--radius-sm); background: var(--color-bg-elevated);">
                <?= sanitize($item['product_name']) ?>
              </td>
              <td><?= sanitize($item['product_sku'] ?? 'N/A') ?></td>
              <td><?= sanitize($item['size'] ?? 'N/A') ?></td>
              <td><?= (int)$item['quantity'] ?></td>
              <td><?= formatPrice($item['unit_price']) ?></td>
              <td><?= formatPrice($item['total_price']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="admin-card" style="margin-top: var(--space-6);">
    <div class="admin-card-header"><h2>Update Status</h2></div>
    <form method="POST" style="padding: var(--space-6);">
      <div class="form-grid">
        <div class="form-group">
          <label>Order Status</label>
          <select name="order_status">
            <option value="pending" <?= $order['order_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $order['order_status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            <option value="returned" <?= $order['order_status'] === 'returned' ? 'selected' : '' ?>>Returned</option>
            <option value="refunded" <?= $order['order_status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
          </select>
        </div>
        <div class="form-group">
          <label>Tracking Number</label>
          <input type="text" name="tracking_number" value="<?= sanitize($order['tracking_number'] ?? '') ?>">
        </div>
        <div class="form-group full-width">
          <label>Note</label>
          <textarea name="note" rows="2" placeholder="Optional note for this status update..."></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Order</button>
      </div>
    </form>
  </div>

  <?php if (!empty($history)): ?>
    <div class="admin-card" style="margin-top: var(--space-6);">
      <div class="admin-card-header"><h2>Status History</h2></div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead><tr><th>Status</th><th>Note</th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach ($history as $h): ?>
              <tr>
                <td><span class="status-badge status-<?= $h['status'] ?>"><?= ucfirst(str_replace('_', ' ', $h['status'])) ?></span></td>
                <td><?= sanitize($h['note'] ?? '—') ?></td>
                <td><?= date('M d, Y h:i A', strtotime($h['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
