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
  $res = $mysqli->query('SELECT COUNT(*) as c FROM products WHERE is_active = 1');
  $stats['products'] = $res ? (int)$res->fetch_assoc()['c'] : 0;

  $res = $mysqli->query("SELECT gender, COUNT(*) as c FROM products WHERE is_active = 1 GROUP BY gender");
  if ($res) {
    while ($r = $res->fetch_assoc()) {
      if ($r['gender'] === 'women') $stats['women_prods'] = (int)$r['c'];
      elseif ($r['gender'] === 'men') $stats['men_prods'] = (int)$r['c'];
      elseif ($r['gender'] === 'kids') $stats['kids_prods'] = (int)$r['c'];
    }
  }

  $res = $mysqli->query('SELECT COUNT(*) as c FROM orders');
  $stats['orders'] = $res ? (int)$res->fetch_assoc()['c'] : 0;

  $res = $mysqli->query('SELECT COUNT(*) as c FROM customers WHERE is_active = 1');
  $stats['customers'] = $res ? (int)$res->fetch_assoc()['c'] : 0;

  $res = $mysqli->query('SELECT SUM(grand_total) as total FROM orders WHERE payment_status = "paid" AND order_status != "cancelled"');
  $stats['revenue'] = $res ? (float)($res->fetch_assoc()['total'] ?? 0) : 0;

  $res = $mysqli->query('SELECT COUNT(*) as c FROM orders WHERE order_status = "pending"');
  $stats['pending_orders'] = $res ? (int)$res->fetch_assoc()['c'] : 0;

  $res = $mysqli->query('SELECT COUNT(*) as c FROM enquiries WHERE status = "new"');
  $stats['new_enquiries'] = $res ? (int)$res->fetch_assoc()['c'] : 0;

  $recentOrders = $mysqli->query('SELECT o.*, c.first_name, c.last_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.created_at DESC LIMIT 5')->fetch_all(MYSQLI_ASSOC);

  $lowStock = $mysqli->query('SELECT p.id, p.name, p.image, ps.size, ps.stock FROM product_sizes ps JOIN products p ON ps.product_id = p.id WHERE ps.stock <= 3 ORDER BY ps.stock ASC LIMIT 5')->fetch_all(MYSQLI_ASSOC);
} else {
  $recentOrders = [];
  $lowStock = [];
}

$pageTitle = 'Dashboard — urban outfit';
$bodyClass = 'admin-page';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header">
    <div>
      <div class="page-header-overline">Overview</div>
      <h1 class="page-header-title">Welcome back, <?= $admin ? sanitize($admin['name']) : 'Administrator' ?></h1>
      <p class="page-header-subtitle">Here's what's happening with your store today.</p>
    </div>
    <div>
      <a href="<?= adminUrl('products/add.php') ?>" class="btn btn-primary btn-lg">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Product
      </a>
    </div>
  </div>

  <!-- Primary Stats Grid -->
  <div class="stats-grid">
    <a href="<?= adminUrl('products/') ?>" class="stat-card stat-card--products fade-up" style="animation-delay: 0.05s;">
      <div class="stat-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= number_format($stats['products']) ?></span>
        <span class="stat-label">Total Products</span>
      </div>
      <div class="stat-card-glow"></div>
    </a>

    <a href="<?= adminUrl('orders/') ?>" class="stat-card stat-card--orders fade-up" style="animation-delay: 0.1s;">
      <div class="stat-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= number_format($stats['orders']) ?></span>
        <span class="stat-label">Total Orders</span>
      </div>
      <div class="stat-card-glow"></div>
    </a>

    <a href="<?= adminUrl('orders/?status=paid') ?>" class="stat-card stat-card--revenue fade-up" style="animation-delay: 0.15s;">
      <div class="stat-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= formatPrice($stats['revenue']) ?></span>
        <span class="stat-label">Revenue</span>
      </div>
      <div class="stat-card-glow"></div>
    </a>

    <a href="<?= adminUrl('customers/') ?>" class="stat-card stat-card--customers fade-up" style="animation-delay: 0.2s;">
      <div class="stat-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= number_format($stats['customers']) ?></span>
        <span class="stat-label">Customers</span>
      </div>
      <div class="stat-card-glow"></div>
    </a>
  </div>

  <!-- Department Breakdown -->
  <div class="section-header fade-up" style="animation-delay: 0.25s; margin-bottom: var(--space-4);">
    <h2 class="section-title">Department Breakdown</h2>
    <p class="section-subtitle">Product distribution across categories</p>
  </div>

  <div class="dept-grid">
    <div class="dept-card dept-card--women fade-up" style="animation-delay: 0.3s;">
      <div class="dept-card-header">
        <div class="dept-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.4a1.6 1.6 0 0 0-1.26-.54h-13a1.6 1.6 0 0 0-1.26.54L2 10v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V10Z"/><path d="M12 5v14"/><path d="M8 10v4"/><path d="M16 10v4"/></svg>
        </div>
        <span class="dept-badge">Women</span>
      </div>
      <div class="dept-count"><?= number_format($stats['women_prods']) ?></div>
      <div class="dept-label">Products</div>
      <div class="dept-meta">Suits, Salwars, Kurtis, Sarees, Lehengas</div>
      <div class="dept-progress"><span style="width: <?= max(8, min(100, ($stats['women_prods'] / max(1, $stats['products'])) * 100)) ?>%;"></span></div>
    </div>

    <div class="dept-card dept-card--men fade-up" style="animation-delay: 0.35s;">
      <div class="dept-card-header">
        <div class="dept-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.4a1.6 1.6 0 0 0-1.26-.54h-13a1.6 1.6 0 0 0-1.26.54L2 10v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V10Z"/><path d="M12 5v14"/><path d="M8 10v4"/><path d="M16 10v4"/></svg>
        </div>
        <span class="dept-badge">Men</span>
      </div>
      <div class="dept-count"><?= number_format($stats['men_prods']) ?></div>
      <div class="dept-label">Products</div>
      <div class="dept-meta">Kurta Sets, Shirts, T-Shirts, Trousers</div>
      <div class="dept-progress"><span style="width: <?= max(8, min(100, ($stats['men_prods'] / max(1, $stats['products'])) * 100)) ?>%;"></span></div>
    </div>

    <div class="dept-card dept-card--kids fade-up" style="animation-delay: 0.4s;">
      <div class="dept-card-header">
        <div class="dept-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <span class="dept-badge">Kids</span>
      </div>
      <div class="dept-count"><?= number_format($stats['kids_prods']) ?></div>
      <div class="dept-label">Products</div>
      <div class="dept-meta">Boys Ethnic, Girls Frocks & Casuals</div>
      <div class="dept-progress"><span style="width: <?= max(8, min(100, ($stats['kids_prods'] / max(1, $stats['products'])) * 100)) ?>%;"></span></div>
    </div>
  </div>

  <!-- Priority Alerts -->
  <?php if ($stats['pending_orders'] > 0 || $stats['new_enquiries'] > 0): ?>
    <div class="alerts-row fade-up" style="animation-delay: 0.45s; margin: var(--space-8) 0;">
      <?php if ($stats['pending_orders'] > 0): ?>
        <a href="<?= adminUrl('orders/?status=pending') ?>" class="alert-card alert-card--warning pulse-subtle">
          <div class="alert-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div class="alert-body">
            <strong><?= number_format($stats['pending_orders']) ?> Pending Orders</strong>
            <span>Waiting for confirmation</span>
          </div>
          <div class="alert-arrow">&rarr;</div>
        </a>
      <?php endif; ?>
      <?php if ($stats['new_enquiries'] > 0): ?>
        <a href="<?= adminUrl('enquiries/') ?>" class="alert-card alert-card--info pulse-subtle">
          <div class="alert-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </div>
          <div class="alert-body">
            <strong><?= number_format($stats['new_enquiries']) ?> New Enquiries</strong>
            <span>Customer messages</span>
          </div>
          <div class="alert-arrow">&rarr;</div>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- Recent Orders & Inventory -->
  <div class="admin-grid">
    <div class="admin-card fade-up" style="animation-delay: 0.5s;">
      <div class="admin-card-header">
        <h2>Recent Customer Orders</h2>
        <a href="<?= adminUrl('orders/') ?>" class="btn btn-secondary btn-sm">View All</a>
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
              <tr><td colspan="5" class="table-empty">No orders placed yet.</td></tr>
            <?php else: ?>
              <?php foreach ($recentOrders as $ord): ?>
                <tr class="table-row-hover">
                  <td><a href="<?= adminUrl('orders/view.php?id=' . $ord['id']) ?>" class="order-link"><?= sanitize($ord['order_number']) ?></a></td>
                  <td><?= sanitize($ord['customer_name']) ?></td>
                  <td class="table-amount"><?= formatPrice($ord['grand_total']) ?></td>
                  <td><span class="status-badge status-<?= $ord['order_status'] ?>"><?= ucfirst($ord['order_status']) ?></span></td>
                  <td class="table-date"><?= date('M d, Y', strtotime($ord['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="admin-card fade-up" style="animation-delay: 0.55s;">
      <div class="admin-card-header">
        <h2>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          Low Stock Alerts
        </h2>
      </div>
      <div class="low-stock-list">
        <?php if (empty($lowStock)): ?>
          <div class="low-stock-empty">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <p>All items have sufficient inventory.</p>
          </div>
        <?php else: ?>
          <?php foreach ($lowStock as $ls): ?>
            <a href="<?= adminUrl('products/edit.php?id=' . $ls['id']) ?>" class="low-stock-item">
              <div class="low-stock-info">
                <div class="low-stock-name"><?= sanitize($ls['name']) ?></div>
                <div class="low-stock-meta">Size: <?= sanitize($ls['size']) ?></div>
              </div>
              <span class="low-stock-badge"><?= $ls['stock'] ?> left</span>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
