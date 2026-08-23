<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$statusFilter = sanitize($_GET['status'] ?? '');
$where = '1=1';
if ($statusFilter) {
  $where .= " AND status = '$statusFilter'";
}

$enquiries = $mysqli->query("SELECT * FROM enquiries WHERE $where ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Enquiries — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <h1>Enquiries</h1>
    <div class="admin-actions">
      <select class="filter-select" onchange="window.location.href='?status=' + this.value;">
        <option value="">All Statuses</option>
        <option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>New</option>
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
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($enquiries)): ?>
            <tr><td colspan="7" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">No enquiries found.</td></tr>
          <?php else: ?>
            <?php foreach ($enquiries as $enquiry): ?>
              <tr>
                <td style="font-weight: 600;"><?= sanitize($enquiry['name']) ?></td>
                <td><?= sanitize($enquiry['email']) ?></td>
                <td><?= sanitize($enquiry['phone'] ?? 'N/A') ?></td>
                <td><?= sanitize($enquiry['subject']) ?></td>
                <td><span class="status-badge status-<?= $enquiry['status'] ?>"><?= ucfirst(str_replace('_', ' ', $enquiry['status'])) ?></span></td>
                <td><?= date('M d, Y', strtotime($enquiry['created_at'])) ?></td>
                <td>
                  <a href="/admin/enquiries/view.php?id=<?= $enquiry['id'] ?>" class="btn btn-secondary btn-sm">View</a>
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
