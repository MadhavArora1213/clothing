<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$statusFilter = sanitize($_GET['status'] ?? '');
$where = '1=1';
if ($statusFilter) {
  $where .= " AND is_approved = " . ($statusFilter === 'approved' ? '1' : '0');
}

$reviews = $mysqli->query("SELECT r.*, p.name as product_name FROM reviews r JOIN products p ON r.product_id = p.id WHERE $where ORDER BY r.created_at DESC")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Reviews — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <h1>Reviews & Ratings</h1>
    <div class="admin-actions">
      <select class="filter-select" onchange="window.location.href='?status=' + this.value;">
        <option value="">All Reviews</option>
        <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
      </select>
    </div>
  </div>

  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Product</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reviews)): ?>
            <tr><td colspan="7" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">No reviews found.</td></tr>
          <?php else: ?>
            <?php foreach ($reviews as $review): ?>
              <tr>
                <td style="font-weight: 600;"><?= sanitize($review['customer_name']) ?></td>
                <td><?= sanitize($review['product_name']) ?></td>
                <td><?= str_repeat('★', $review['rating']) ?><?= str_repeat('☆', 5 - $review['rating']) ?></td>
                <td><?= sanitize(mb_substr($review['comment'] ?? '', 0, 60)) ?><?= mb_strlen($review['comment'] ?? '') > 60 ? '...' : '' ?></td>
                <td><span class="status-badge <?= $review['is_approved'] ? 'status-active' : 'status-inactive' ?>"><?= $review['is_approved'] ? 'Approved' : 'Pending' ?></span></td>
                <td><?= date('M d, Y', strtotime($review['created_at'])) ?></td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <?php if (!$review['is_approved']): ?>
                      <a href="/admin/reviews/approve.php?id=<?= $review['id'] ?>" class="btn btn-secondary btn-sm">Approve</a>
                    <?php else: ?>
                      <a href="/admin/reviews/reject.php?id=<?= $review['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                    <?php endif; ?>
                    <a href="/admin/reviews/delete.php?id=<?= $review['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this review?')">Delete</a>
                  </div>
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
