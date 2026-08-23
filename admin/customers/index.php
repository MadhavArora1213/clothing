<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

// Handle status toggle
if (isset($_GET['toggle_status_id'])) {
  $toggleId = (int)$_GET['toggle_status_id'];
  $mysqli->query("UPDATE customers SET is_active = IF(is_active = 1, 0, 1) WHERE id = $toggleId");
  redirect(adminUrl('customers/?msg=Customer+status+updated'));
}

$search = sanitize($_GET['search'] ?? '');
$where = ['1=1'];
$params = [];
$types = '';

if (!empty($search)) {
  $where[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
  $searchTerm = "%$search%";
  $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
  $types = 'ssss';
}

$whereClause = implode(' AND ', $where);
$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM orders WHERE customer_id = c.id) as order_count,
          (SELECT IFNULL(SUM(grand_total), 0) FROM orders WHERE customer_id = c.id AND payment_status = 'paid') as total_spend 
          FROM customers c 
          WHERE $whereClause 
          ORDER BY c.created_at DESC";

if (!empty($params)) {
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $customers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
  $res = $mysqli->query($query);
  $customers = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

$pageTitle = 'Customers Management — AURA & CO. Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Registered Customers (<?= count($customers) ?>)</h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        View customer profiles, order history, lifetime value, and account access.
      </p>
    </div>
    <div class="search-box">
      <form method="GET" action="" style="display: flex; gap: 8px;">
        <div style="position: relative;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" name="search" placeholder="Search customer, email, phone..." value="<?= sanitize($search) ?>" style="padding-left: 36px; width: 280px;">
        </div>
        <button type="submit" class="btn btn-secondary btn-sm">Search</button>
        <?php if ($search): ?>
          <a href="<?= adminUrl('customers/') ?>" class="btn btn-secondary btn-sm" style="color: #ef4444;">Clear</a>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6); background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; padding: 12px 16px; border-radius: 8px;">
      <?= sanitize($_GET['msg']) ?>
    </div>
  <?php endif; ?>

  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Orders</th>
            <th>Total Spend</th>
            <th>Status</th>
            <th>Joined Date</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($customers)): ?>
            <tr><td colspan="8" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">No customers registered yet.</td></tr>
          <?php else: ?>
            <?php foreach ($customers as $cust): ?>
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="admin-avatar" style="width: 32px; height: 32px; font-size: 12px;">
                      <?= strtoupper(substr($cust['first_name'], 0, 1)) ?>
                    </div>
                    <strong><?= sanitize($cust['first_name'] . ' ' . $cust['last_name']) ?></strong>
                  </div>
                </td>
                <td><a href="mailto:<?= sanitize($cust['email']) ?>"><?= sanitize($cust['email']) ?></a></td>
                <td><?= sanitize($cust['phone'] ?? '—') ?></td>
                <td>
                  <span style="font-size: 12px; font-weight: 600; background: #f1f5f9; padding: 2px 8px; border-radius: 12px;">
                    <?= (int)$cust['order_count'] ?> orders
                  </span>
                </td>
                <td style="font-weight: 600; color: #0f172a;"><?= formatPrice($cust['total_spend']) ?></td>
                <td>
                  <span class="status-badge <?= $cust['is_active'] ? 'status-active' : 'status-inactive' ?>">
                    <?= $cust['is_active'] ? 'Active' : 'Disabled' ?>
                  </span>
                </td>
                <td style="font-size: 12px; color: #64748b;"><?= date('M d, Y', strtotime($cust['created_at'])) ?></td>
                <td style="text-align: right;">
                  <a href="<?= adminUrl('customers/?toggle_status_id=' . $cust['id']) ?>" class="btn btn-secondary btn-sm" onclick="return confirm('Change account status for this customer?')">
                    <?= $cust['is_active'] ? 'Disable' : 'Enable' ?>
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
