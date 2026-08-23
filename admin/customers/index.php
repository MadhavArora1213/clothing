<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$search = sanitize($_GET['search'] ?? '');
$where = '1=1';
if ($search) {
  $where .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%')";
}

$customers = $mysqli->query("SELECT * FROM customers WHERE $where ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Customers — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <h1>Customers</h1>
    <div class="search-box">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      <input type="text" placeholder="Search customers..." id="searchInput" value="<?= sanitize($search) ?>">
    </div>
  </div>

  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Orders</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($customers)): ?>
            <tr><td colspan="6" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">No customers found.</td></tr>
          <?php else: ?>
            <?php foreach ($customers as $customer): ?>
              <?php
                $orderCount = $mysqli->query("SELECT COUNT(*) as c FROM orders WHERE customer_id = {$customer['id']}")->fetch_assoc()['c'] ?? 0;
              ?>
              <tr>
                <td style="font-weight: 600;"><?= sanitize($customer['first_name'] . ' ' . $customer['last_name']) ?></td>
                <td><?= sanitize($customer['email']) ?></td>
                <td><?= sanitize($customer['phone'] ?? 'N/A') ?></td>
                <td><span class="status-badge <?= $customer['is_active'] ? 'status-active' : 'status-inactive' ?>"><?= $customer['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td><?= number_format($orderCount) ?></td>
                <td><?= date('M d, Y', strtotime($customer['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
