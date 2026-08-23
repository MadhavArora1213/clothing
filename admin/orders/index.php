<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$statusFilter = sanitize($_GET['status'] ?? '');
$where = '1=1';
if ($statusFilter) {
  $where .= " AND order_status = '$statusFilter'";
}

$orders = $mysqli->query("SELECT o.*, c.first_name, c.last_name, c.email FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE $where ORDER BY o.created_at DESC")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Orders — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <h1>Orders</h1>
    <div class="admin-actions">
      <select class="filter-select" onchange="window.location.href='?status=' + this.value;">
        <option value="">All Statuses</option>
        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="confirmed" <?= $statusFilter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
        <option value="processing" <?= $statusFilter === 'processing' ? 'selected' : '' ?>>Processing</option>
        <option value="shipped" <?= $statusFilter === 'shipped' ? 'selected' : '' ?>>Shipped</option>
        <option value="delivered" <?= $statusFilter === 'delivered' ? 'selected' : '' ?>>Delivered</option>
        <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
      </select>
    </div>
  </div>

  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr><td colspan="7" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">No orders found.</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><a href="/admin/orders/view.php?id=<?= $order['id'] ?>"><?= sanitize($order['order_number']) ?></a></td>
                <td><?= sanitize($order['customer_name']) ?><br><small style="color: var(--color-text-tertiary);"><?= sanitize($order['customer_email']) ?></small></td>
                <td><?= formatPrice($order['grand_total']) ?></td>
                <td><span class="status-badge status-<?= $order['payment_status'] ?>"><?= ucfirst($order['payment_status']) ?></span></td>
                <td><span class="status-badge status-<?= $order['order_status'] ?>"><?= ucfirst(str_replace('_', ' ', $order['order_status'])) ?></span></td>
                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                <td>
                  <a href="/admin/orders/view.php?id=<?= $order['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
