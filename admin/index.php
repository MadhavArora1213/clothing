<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();
$admin = getAdmin();

$stats = [];

// Total products
$result = $mysqli->query('SELECT COUNT(*) as count FROM products WHERE is_active = 1');
$stats['products'] = $result->fetch_assoc()['count'];

// Total orders
$result = $mysqli->query('SELECT COUNT(*) as count FROM orders');
$stats['orders'] = $result->fetch_assoc()['count'];

// Total customers
$result = $mysqli->query('SELECT COUNT(*) as count FROM customers WHERE is_active = 1');
$stats['customers'] = $result->fetch_assoc()['count'];

// Total revenue
$result = $mysqli->query('SELECT SUM(grand_total) as total FROM orders WHERE payment_status = "paid" AND order_status != "cancelled"');
$stats['revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Pending orders
$result = $mysqli->query('SELECT COUNT(*) as count FROM orders WHERE order_status = "pending"');
$stats['pending_orders'] = $result->fetch_assoc()['count'];

// Total enquiries
$result = $mysqli->query('SELECT COUNT(*) as count FROM enquiries WHERE status = "new"');
$stats['new_enquiries'] = $result->fetch_assoc()['count'];

// Recent orders
$recentOrders = $mysqli->query('SELECT o.*, c.first_name, c.last_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC LIMIT 5')->fetch_all(MYSQLI_ASSOC);

// Top selling products
$topProducts = $mysqli->query('SELECT p.name, p.image, SUM(oi.quantity) as total_sold FROM order_items oi JOIN products p ON oi.product_id = p.id GROUP BY p.id ORDER BY total_sold DESC LIMIT 5')->fetch_all(MYSQLI_ASSOC);

// Sales by status
$orderStatuses = [];
$result = $mysqli->query('SELECT order_status, COUNT(*) as count FROM orders GROUP BY order_status');
while ($row = $result->fetch_assoc()) {
  $orderStatuses[] = $row;
}

$pageTitle = 'Dashboard — ATELIER Admin';
$bodyClass = 'admin-page';
include dirname(__DIR__) . '/admin/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header">
    <h1>Dashboard</h1>
    <p>Welcome back, <?= sanitize($admin['name']) ?>.</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon" style="background: #EFF6FF; color: #2563EB;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= number_format($stats['products']) ?></span>
        <span class="stat-label">Total Products</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background: #F0FDF4; color: #16A34A;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= number_format($stats['customers']) ?></span>
        <span class="stat-label">Customers</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background: #FEF3C7; color: #D97706;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= number_format($stats['orders']) ?></span>
        <span class="stat-label">Total Orders</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background: #FDF2F8; color: #DB2777;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= formatPrice($stats['revenue']) ?></span>
        <span class="stat-label">Revenue</span>
      </div>
    </div>
  </div>

  <div class="admin-grid">
    <div class="admin-card">
      <div class="admin-card-header">
        <h2>Recent Orders</h2>
        <a href="/admin/orders/" class="btn btn-secondary" style="padding: var(--space-2) var(--space-4); font-size: var(--text-caption);">View All</a>
      </div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order</th>
              <th>Customer</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recentOrders)): ?>
              <tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--color-text-tertiary);">No orders yet.</td></tr>
            <?php else: ?>
              <?php foreach ($recentOrders as $order): ?>
                <tr>
                  <td><a href="/admin/orders/view.php?id=<?= $order['id'] ?>"><?= sanitize($order['order_number']) ?></a></td>
                  <td><?= sanitize($order['customer_name']) ?></td>
                  <td><?= formatPrice($order['grand_total']) ?></td>
                  <td><span class="status-badge status-<?= $order['order_status'] ?>"><?= ucfirst(str_replace('_', ' ', $order['order_status'])) ?></span></td>
                  <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="admin-card">
      <div class="admin-card-header">
        <h2>Top Selling Products</h2>
        <a href="/admin/products/" class="btn btn-secondary" style="padding: var(--space-2) var(--space-4); font-size: var(--text-caption);">View All</a>
      </div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr><th>Product</th><th>Sold</th></tr>
          </thead>
          <tbody>
            <?php if (empty($topProducts)): ?>
              <tr><td colspan="2" style="text-align: center; padding: 40px; color: var(--color-text-tertiary);">No sales data yet.</td></tr>
            <?php else: ?>
              <?php foreach ($topProducts as $product): ?>
                <tr>
                  <td><?= sanitize($product['name']) ?></td>
                  <td><?= number_format($product['total_sold']) ?> units</td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <?php if ($stats['pending_orders'] > 0 || $stats['new_enquiries'] > 0): ?>
    <div class="alerts-row">
      <?php if ($stats['pending_orders'] > 0): ?>
        <a href="/admin/orders/" class="alert-card" style="background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E;">
          <strong><?= number_format($stats['pending_orders']) ?></strong> pending orders need attention.
        </a>
      <?php endif; ?>
      <?php if ($stats['new_enquiries'] > 0): ?>
        <a href="/admin/enquiries/" class="alert-card" style="background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF;">
          <strong><?= number_format($stats['new_enquiries']) ?></strong> new enquiries to review.
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/admin/includes/footer.php'; ?>
