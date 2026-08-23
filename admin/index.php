<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();
$admin = getAdmin();

$stats = [
  'products' => 0,
  'women_prods' => 0,
  'men_prods' => 0,
  'kids_prods' => 0,
  'customers' => 0,
  'orders' => 0,
  'revenue' => 0,
  'pending_orders' => 0,
  'new_enquiries' => 0,
];

if ($mysqli) {
  // Total products
  $res = $mysqli->query('SELECT COUNT(*) as c FROM products WHERE is_active = 1');
  $stats['products'] = $res ? (int)$res->fetch_assoc()['c'] : 0;

  // Department counts
  $res = $mysqli->query("SELECT gender, COUNT(*) as c FROM products WHERE is_active = 1 GROUP BY gender");
  if ($res) {
    while ($r = $res->fetch_assoc()) {
      if ($r['gender'] === 'women') $stats['women_prods'] = (int)$r['c'];
      elseif ($r['gender'] === 'men') $stats['men_prods'] = (int)$r['c'];
      elseif ($r['gender'] === 'kids') $stats['kids_prods'] = (int)$r['c'];
    }
  }

  // Total orders
  $res = $mysqli->query('SELECT COUNT(*) as c FROM orders');
  $stats['orders'] = $res ? (int)$res->fetch_assoc()['c'] : 0;

  // Total customers
  $res = $mysqli->query('SELECT COUNT(*) as c FROM customers WHERE is_active = 1');
  $stats['customers'] = $res ? (int)$res->fetch_assoc()['c'] : 0;

  // Total revenue
  $res = $mysqli->query('SELECT SUM(grand_total) as total FROM orders WHERE payment_status = "paid" AND order_status != "cancelled"');
  $stats['revenue'] = $res ? (float)($res->fetch_assoc()['total'] ?? 0) : 0;

  // Pending orders
  $res = $mysqli->query('SELECT COUNT(*) as c FROM orders WHERE order_status = "pending"');
  $stats['pending_orders'] = $res ? (int)$res->fetch_assoc()['c'] : 0;

  // New enquiries
  $res = $mysqli->query('SELECT COUNT(*) as c FROM enquiries WHERE status = "new"');
  $stats['new_enquiries'] = $res ? (int)$res->fetch_assoc()['c'] : 0;

  // Recent orders
  $recentOrders = $mysqli->query('SELECT o.*, c.first_name, c.last_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC LIMIT 5')->fetch_all(MYSQLI_ASSOC);

  // Low stock products
  $lowStock = $mysqli->query('SELECT p.id, p.name, p.image, ps.size, ps.stock FROM product_sizes ps JOIN products p ON ps.product_id = p.id WHERE ps.stock <= 3 ORDER BY ps.stock ASC LIMIT 5')->fetch_all(MYSQLI_ASSOC);
} else {
  $recentOrders = [];
  $lowStock = [];
}

$pageTitle = 'Fashion Admin Dashboard — AURA & CO.';
$bodyClass = 'admin-page';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <div>
        <h1>Admin Control Dashboard</h1>
        <p>Welcome back, <?= $admin ? sanitize($admin['name']) : 'Administrator' ?>. Here is the latest fashion store overview.</p>
      </div>
      <div style="display: flex; gap: 8px;">
        <a href="<?= adminUrl('products/add.php') ?>" class="btn btn-primary">+ Add New Product</a>
      </div>
    </div>
  </div>

  <!-- Primary Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon" style="background: #EFF6FF; color: #2563EB;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= number_format($stats['products']) ?></span>
        <span class="stat-label">Total Catalog Products</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: #FEF3C7; color: #D97706;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= number_format($stats['orders']) ?></span>
        <span class="stat-label">Total Orders</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: #F0FDF4; color: #16A34A;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= formatPrice($stats['revenue']) ?></span>
        <span class="stat-label">Paid Revenue</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: #FDF2F8; color: #DB2777;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= number_format($stats['customers']) ?></span>
        <span class="stat-label">Registered Customers</span>
      </div>
    </div>
  </div>

  <!-- Department Quick Breakdown (Kids, Men, Women) -->
  <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4); margin-bottom: var(--space-8);">
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #DB2777;">
      <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #DB2777;">Women Wear</span>
      <div style="font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 4px;"><?= $stats['women_prods'] ?> <span style="font-size: 13px; font-weight: 400; color: #64748b;">Products</span></div>
      <small style="color: #64748b;">Suits, Salwars, Kurtis, Sarees, Lehengas</small>
    </div>

    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #3B82F6;">
      <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #3B82F6;">Men (Gents) Wear</span>
      <div style="font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 4px;"><?= $stats['men_prods'] ?> <span style="font-size: 13px; font-weight: 400; color: #64748b;">Products</span></div>
      <small style="color: #64748b;">Kurta Sets, Shirts, T-Shirts, Trousers</small>
    </div>

    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #F59E0B;">
      <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #F59E0B;">Kids Wear</span>
      <div style="font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 4px;"><?= $stats['kids_prods'] ?> <span style="font-size: 13px; font-weight: 400; color: #64748b;">Products</span></div>
      <small style="color: #64748b;">Boys Ethnic, Girls Frocks &amp; Casuals</small>
    </div>
  </div>

  <!-- Priority Alerts Row -->
  <?php if ($stats['pending_orders'] > 0 || $stats['new_enquiries'] > 0): ?>
    <div style="display: flex; gap: var(--space-4); margin-bottom: var(--space-8); flex-wrap: wrap;">
      <?php if ($stats['pending_orders'] > 0): ?>
        <a href="<?= adminUrl('orders/?status=pending') ?>" style="flex: 1; min-width: 260px; background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; padding: 14px 18px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between;">
          <span><strong><?= number_format($stats['pending_orders']) ?></strong> pending orders waiting for confirmation.</span>
          <span style="font-weight: 700;">Review &rarr;</span>
        </a>
      <?php endif; ?>
      <?php if ($stats['new_enquiries'] > 0): ?>
        <a href="<?= adminUrl('enquiries/') ?>" style="flex: 1; min-width: 260px; background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; padding: 14px 18px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between;">
          <span><strong><?= number_format($stats['new_enquiries']) ?></strong> new customer contact enquiries received.</span>
          <span style="font-weight: 700;">View &rarr;</span>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- Recent Orders & Inventory Alerts -->
  <div class="admin-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-6);">
    <div class="admin-card">
      <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 16px; margin: 0;">Recent Customer Orders</h2>
        <a href="<?= adminUrl('orders/') ?>" class="btn btn-secondary btn-sm">View All Orders</a>
      </div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order #</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recentOrders)): ?>
              <tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--color-text-tertiary);">No orders placed yet.</td></tr>
            <?php else: ?>
              <?php foreach ($recentOrders as $ord): ?>
                <tr>
                  <td><a href="<?= adminUrl('orders/view.php?id=' . $ord['id']) ?>" style="font-weight: 700; color: #0284c7;"><?= sanitize($ord['order_number']) ?></a></td>
                  <td><?= sanitize($ord['customer_name']) ?></td>
                  <td style="font-weight: 600;"><?= formatPrice($ord['grand_total']) ?></td>
                  <td><span class="status-badge status-<?= $ord['order_status'] ?>"><?= ucfirst($ord['order_status']) ?></span></td>
                  <td style="font-size: 12px; color: #64748b;"><?= date('M d, Y', strtotime($ord['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Low Stock Items Warning -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 style="font-size: 16px; margin: 0; display: flex; align-items: center; gap: 6px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          Low Stock Alerts
        </h2>
      </div>
      <div style="padding: var(--space-4) var(--space-6);">
        <?php if (empty($lowStock)): ?>
          <p style="color: var(--color-text-tertiary); font-size: 13px; text-align: center; padding: 20px 0;">All items have sufficient inventory.</p>
        <?php else: ?>
          <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php foreach ($lowStock as $ls): ?>
              <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;">
                <div>
                  <a href="<?= adminUrl('products/edit.php?id=' . $ls['id']) ?>" style="font-weight: 600; font-size: 13px; color: #0f172a;">
                    <?= sanitize($ls['name']) ?>
                  </a>
                  <div style="font-size: 11px; color: #64748b;">Size: <strong><?= sanitize($ls['size']) ?></strong></div>
                </div>
                <span style="font-size: 11px; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 2px 6px; border-radius: 4px;">
                  <?= $ls['stock'] ?> left
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
