<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$statusFilter = sanitize($_GET['status'] ?? '');
$search = sanitize($_GET['search'] ?? '');

$where = ['1=1'];
$params = [];
$types = '';

if (!empty($statusFilter)) {
  $where[] = 'o.order_status = ?';
  $params[] = $statusFilter;
  $types .= 's';
}

if (!empty($search)) {
  $where[] = '(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ? OR o.customer_phone LIKE ?)';
  $searchTerm = "%$search%";
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $types .= 'ssss';
}

$whereClause = implode(' AND ', $where);

$query = "SELECT o.*, c.first_name, c.last_name, 
          (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as total_items 
          FROM orders o 
          LEFT JOIN customers c ON o.customer_id = c.id 
          WHERE $whereClause 
          ORDER BY o.created_at DESC";

if (!empty($params)) {
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
  $res = $mysqli->query($query);
  $orders = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

// Counts for quick tabs
$counts = [
  'all' => 0, 'pending' => 0, 'confirmed' => 0, 'processing' => 0, 'shipped' => 0, 'delivered' => 0, 'cancelled' => 0
];
$cRes = $mysqli->query("SELECT order_status, COUNT(*) as cnt FROM orders GROUP BY order_status");
if ($cRes) {
  while ($row = $cRes->fetch_assoc()) {
    $counts[$row['order_status']] = (int)$row['cnt'];
    $counts['all'] += (int)$row['cnt'];
  }
}

$pageTitle = 'Orders Management — urban outfit Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Orders Management</h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Track customer apparel purchases, fulfillment pipeline, shipping tracking, and order receipts.
      </p>
    </div>
  </div>

  <!-- Status Quick Filter Tabs -->
  <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: var(--space-6);">
    <a href="?status=" class="btn <?= empty($statusFilter) ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
      All Orders (<?= $counts['all'] ?>)
    </a>
    <a href="?status=pending" class="btn <?= $statusFilter === 'pending' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
      Pending (<?= $counts['pending'] ?>)
    </a>
    <a href="?status=confirmed" class="btn <?= $statusFilter === 'confirmed' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
      Confirmed (<?= $counts['confirmed'] ?>)
    </a>
    <a href="?status=processing" class="btn <?= $statusFilter === 'processing' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
      Processing (<?= $counts['processing'] ?>)
    </a>
    <a href="?status=shipped" class="btn <?= $statusFilter === 'shipped' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
      Shipped (<?= $counts['shipped'] ?>)
    </a>
    <a href="?status=delivered" class="btn <?= $statusFilter === 'delivered' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
      Delivered (<?= $counts['delivered'] ?>)
    </a>
    <a href="?status=cancelled" class="btn <?= $statusFilter === 'cancelled' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
      Cancelled (<?= $counts['cancelled'] ?>)
    </a>
  </div>

  <!-- Search & Filter Card -->
  <div class="admin-card" style="margin-bottom: var(--space-6); padding: var(--space-4) var(--space-6);">
    <form method="GET" action="" style="display: flex; gap: 12px; align-items: center;">
      <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
      <div class="search-box" style="flex: 1; max-width: 380px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="text" name="search" placeholder="Search order number, customer, phone..." value="<?= sanitize($search) ?>">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Search</button>
      <?php if ($search): ?>
        <a href="?status=<?= urlencode($statusFilter) ?>" class="btn btn-secondary btn-sm" style="color: #ef4444;">Clear</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Orders Table -->
  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer &amp; Contact</th>
            <th>Items</th>
            <th>Grand Total</th>
            <th>Payment</th>
            <th>Order Status</th>
            <th>Date Placed</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr>
              <td colspan="8" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">
                No orders found under this view.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
              <?php
                $statusColor = match($order['order_status']) {
                  'pending' => 'background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;',
                  'confirmed' => 'background: #E0E7FF; color: #3730A3; border: 1px solid #C7D2FE;',
                  'processing' => 'background: #DBEAFE; color: #1E40AF; border: 1px solid #BFDBFE;',
                  'shipped' => 'background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD;',
                  'delivered' => 'background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;',
                  'cancelled', 'returned' => 'background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;',
                  default => 'background: #F3F4F6; color: #374151;'
                };
              ?>
              <tr>
                <td>
                  <a href="<?= adminUrl('orders/view.php?id=' . $order['id']) ?>" style="font-weight: 700; color: #0284c7; text-decoration: underline;">
                    <?= sanitize($order['order_number']) ?>
                  </a>
                  <?php if (!empty($order['tracking_number'])): ?>
                    <div style="font-size: 11px; color: var(--color-text-tertiary);">Track: <?= sanitize($order['tracking_number']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-weight: 600;"><?= sanitize($order['customer_name']) ?></div>
                  <div style="font-size: 12px; color: var(--color-text-secondary);"><?= sanitize($order['customer_phone']) ?></div>
                  <small style="color: var(--color-text-tertiary); font-size: 11px;"><?= sanitize($order['customer_email']) ?></small>
                </td>
                <td>
                  <span style="font-size: 12px; font-weight: 600; background: #f1f5f9; padding: 2px 8px; border-radius: 12px;">
                    <?= (int)$order['total_items'] ?> <?= (int)$order['total_items'] === 1 ? 'item' : 'items' ?>
                  </span>
                </td>
                <td>
                  <div style="font-weight: 700; font-size: 14px;"><?= formatPrice($order['grand_total']) ?></div>
                  <?php if ($order['discount_amount'] > 0): ?>
                    <small style="color: #16a34a; font-size: 11px;">Saved <?= formatPrice($order['discount_amount']) ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-weight: 500; font-size: 12px; text-transform: uppercase;"><?= sanitize($order['payment_method']) ?></div>
                  <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 1px 6px; border-radius: 4px; <?= $order['payment_status'] === 'paid' ? 'background: #DCFCE7; color: #166534;' : 'background: #FEF3C7; color: #92400E;' ?>">
                    <?= ucfirst($order['payment_status']) ?>
                  </span>
                </td>
                <td>
                  <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 6px; display: inline-block; <?= $statusColor ?>">
                    <?= ucfirst($order['order_status']) ?>
                  </span>
                </td>
                <td style="font-size: 12px; color: #64748b;">
                  <?= date('M d, Y', strtotime($order['created_at'])) ?><br>
                  <small><?= date('h:i A', strtotime($order['created_at'])) ?></small>
                </td>
                <td style="text-align: right;">
                  <a href="<?= adminUrl('orders/view.php?id=' . $order['id']) ?>" class="btn btn-secondary btn-sm">
                    Manage &rarr;
                  </a>
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
