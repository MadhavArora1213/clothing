<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$statusFilter = sanitize($_GET['status'] ?? '');
$where = ['1=1'];
$params = [];
$types = '';

if ($statusFilter) {
  $where[] = 'status = ?';
  $params[] = $statusFilter;
  $types .= 's';
}

$whereClause = implode(' AND ', $where);
$query = "SELECT * FROM enquiries WHERE $whereClause ORDER BY created_at DESC";

if (!empty($params)) {
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $enquiries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
  $res = $mysqli->query($query);
  $enquiries = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

$pageTitle = 'Customer Enquiries — urban outfit Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Customer Contact Inquiries (<?= count($enquiries) ?>)</h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Review and respond to customer questions regarding sizing, custom stitching, orders and shipping.
      </p>
    </div>
    <div class="admin-actions">
      <select class="filter-select" onchange="window.location.href='?status=' + this.value;">
        <option value="">All Inquiries</option>
        <option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>New / Unopened</option>
        <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
        <option value="resolved" <?= $statusFilter === 'resolved' ? 'selected' : '' ?>>Resolved</option>
      </select>
    </div>
  </div>

  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Contact</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Date Received</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($enquiries)): ?>
            <tr><td colspan="6" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">No enquiries found.</td></tr>
          <?php else: ?>
            <?php foreach ($enquiries as $enquiry): ?>
              <?php
                $statusClass = match($enquiry['status']) {
                  'new' => 'background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE;',
                  'in_progress' => 'background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;',
                  'resolved' => 'background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;',
                  default => 'background: #F3F4F6; color: #374151;'
                };
              ?>
              <tr>
                <td style="font-weight: 600;"><?= sanitize($enquiry['name']) ?></td>
                <td>
                  <div><a href="mailto:<?= sanitize($enquiry['email']) ?>"><?= sanitize($enquiry['email']) ?></a></div>
                  <small style="color: var(--color-text-tertiary);"><?= sanitize($enquiry['phone'] ?? 'No phone') ?></small>
                </td>
                <td style="font-weight: 500;"><?= sanitize($enquiry['subject']) ?></td>
                <td>
                  <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 6px; <?= $statusClass ?>">
                    <?= ucfirst(str_replace('_', ' ', $enquiry['status'])) ?>
                  </span>
                </td>
                <td style="font-size: 12px; color: #64748b;"><?= date('M d, Y h:i A', strtotime($enquiry['created_at'])) ?></td>
                <td style="text-align: right;">
                  <a href="<?= adminUrl('enquiries/view.php?id=' . $enquiry['id']) ?>" class="btn btn-secondary btn-sm">
                    View &amp; Reply &rarr;
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
