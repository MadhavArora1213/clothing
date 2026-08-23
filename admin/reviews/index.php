<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$statusFilter = sanitize($_GET['status'] ?? '');
$where = '1=1';
if ($statusFilter) {
  $where .= " AND is_approved = " . ($statusFilter === 'approved' ? '1' : '0');
}

$reviews = $mysqli->query("SELECT r.*, p.name as product_name FROM reviews r JOIN products p ON r.product_id = p.id WHERE $where ORDER BY r.created_at DESC")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Reviews & Ratings — AURA & CO. Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Product Customer Reviews</h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Moderate buyer reviews, ratings, and customer feedback.
      </p>
    </div>
    <div class="admin-actions">
      <select class="filter-select" onchange="window.location.href='?status=' + this.value;">
        <option value="">All Reviews</option>
        <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending Review</option>
      </select>
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
            <th>Customer</th>
            <th>Product</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Date</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reviews)): ?>
            <tr><td colspan="7" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">No product reviews found.</td></tr>
          <?php else: ?>
            <?php foreach ($reviews as $review): ?>
              <tr>
                <td style="font-weight: 600;"><?= sanitize($review['customer_name']) ?></td>
                <td><strong><?= sanitize($review['product_name']) ?></strong></td>
                <td style="color: #f59e0b; font-size: 14px; letter-spacing: 2px;">
                  <?= str_repeat('★', $review['rating']) ?><?= str_repeat('☆', 5 - $review['rating']) ?>
                </td>
                <td style="max-width: 300px; font-size: 13px;">
                  <?= sanitize($review['comment'] ?? '') ?>
                </td>
                <td>
                  <span class="status-badge <?= $review['is_approved'] ? 'status-active' : 'status-inactive' ?>">
                    <?= $review['is_approved'] ? 'Approved' : 'Pending' ?>
                  </span>
                </td>
                <td style="font-size: 12px; color: #64748b;"><?= date('M d, Y', strtotime($review['created_at'])) ?></td>
                <td style="text-align: right;">
                  <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <?php if (!$review['is_approved']): ?>
                      <a href="<?= adminUrl('reviews/approve.php?id=' . $review['id']) ?>" class="btn btn-secondary btn-sm">Approve</a>
                    <?php else: ?>
                      <a href="<?= adminUrl('reviews/reject.php?id=' . $review['id']) ?>" class="btn btn-secondary btn-sm" style="color: #ea580c;">Reject</a>
                    <?php endif; ?>
                    <a href="<?= adminUrl('reviews/delete.php?id=' . $review['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this review?')">Delete</a>
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
