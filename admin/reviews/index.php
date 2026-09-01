<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$statusFilter = sanitize($_GET['status'] ?? '');
$where = '1=1';
if ($statusFilter === 'approved') {
  $where .= " AND r.is_approved = 1";
} elseif ($statusFilter === 'pending') {
  $where .= " AND r.is_approved = 0";
}

$reviews = $mysqli->query("
  SELECT r.*, p.name as product_name, p.price as product_price
  FROM reviews r
  LEFT JOIN products p ON r.product_id = p.id
  WHERE $where
  ORDER BY r.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

// Fetch review images
if (!empty($reviews)) {
  $revIds = array_column($reviews, 'id');
  $placeholders = implode(',', array_fill(0, count($revIds), '?'));
  $imgStmt = $mysqli->prepare("SELECT review_id, image_url FROM review_images WHERE review_id IN ($placeholders) ORDER BY review_id, sort_order");
  if ($imgStmt) {
    $imgStmt->bind_param(str_repeat('i', count($revIds)), ...$revIds);
    $imgStmt->execute();
    $imgRows = $imgStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $reviewImages = [];
    foreach ($imgRows as $ir) {
      $reviewImages[$ir['review_id']][] = $ir['image_url'];
    }
    foreach ($reviews as &$rev) {
      $rev['images'] = $reviewImages[$rev['id']] ?? [];
    }
    unset($rev);
  }
}

// Stats
$totalCount = count($reviews);
$approvedCount = 0;
$pendingCount = 0;
$sumRating = 0;
foreach ($reviews as $r) {
  if ($r['is_approved']) $approvedCount++;
  else $pendingCount++;
  $sumRating += (int)$r['rating'];
}
$avgRating = $totalCount > 0 ? round($sumRating / $totalCount, 1) : 0;

$pageTitle = 'Reviews & Ratings — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<style>
  .rev-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }
  .rev-stat-card {
    background: #fff;
    border: 1px solid #E8E2D8;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
  }
  .rev-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .rev-stat-icon svg { width: 22px; height: 22px; }
  .rev-stat-info .num {
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
    line-height: 1;
  }
  .rev-stat-info .lbl {
    font-size: 12px;
    color: #9A8E7E;
    margin-top: 2px;
  }
  .rev-stars-cell {
    color: #D4AF37;
    font-size: 15px;
    letter-spacing: 1px;
  }
  .rev-comment {
    max-width: 300px;
    font-size: 13px;
    color: #5C5347;
    line-height: 1.5;
  }
  .rev-title { font-weight: 600; font-size: 13px; color: #1a1a1a; margin-bottom: 2px; }
</style>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Reviews & Ratings</h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Moderate customer reviews and manage product ratings.
      </p>
    </div>
    <div class="admin-actions">
      <select class="filter-select" onchange="window.location.href='?status=' + this.value;">
        <option value="">All Reviews (<?= $totalCount ?>)</option>
        <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved (<?= $approvedCount ?>)</option>
        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending (<?= $pendingCount ?>)</option>
      </select>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="rev-stats">
    <div class="rev-stat-card">
      <div class="rev-stat-icon" style="background: rgba(212,175,55,0.1);">
        <svg fill="none" stroke="#D4AF37" stroke-width="2" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
      </div>
      <div class="rev-stat-info">
        <div class="num"><?= $avgRating ?></div>
        <div class="lbl">Avg Rating</div>
      </div>
    </div>
    <div class="rev-stat-card">
      <div class="rev-stat-icon" style="background: rgba(59,130,246,0.1);">
        <svg fill="none" stroke="#3B82F6" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </div>
      <div class="rev-stat-info">
        <div class="num"><?= $totalCount ?></div>
        <div class="lbl">Total Reviews</div>
      </div>
    </div>
    <div class="rev-stat-card">
      <div class="rev-stat-icon" style="background: rgba(34,197,94,0.1);">
        <svg fill="none" stroke="#22C55E" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div class="rev-stat-info">
        <div class="num"><?= $approvedCount ?></div>
        <div class="lbl">Approved</div>
      </div>
    </div>
    <div class="rev-stat-card">
      <div class="rev-stat-icon" style="background: rgba(245,158,11,0.1);">
        <svg fill="none" stroke="#F59E0B" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div class="rev-stat-info">
        <div class="num"><?= $pendingCount ?></div>
        <div class="lbl">Pending</div>
      </div>
    </div>
  </div>

  <?php if (!empty($_GET['msg'])): ?>
    <?php
      $msg = sanitize($_GET['msg']);
      $isError = (strpos($msg, 'error') !== false || strpos($msg, 'not+found') !== false || strpos($msg, 'Invalid') !== false || strpos($msg, 'Database') !== false);
      $bgColor = $isError ? '#FEF2F2' : '#F0FDF4';
      $textColor = $isError ? '#991B1B' : '#166534';
      $borderColor = $isError ? '#FECACA' : '#BBF7D0';
    ?>
    <div style="margin-bottom: 20px; background: <?= $bgColor ?>; color: <?= $textColor ?>; border: 1px solid <?= $borderColor ?>; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="<?= $textColor ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <?= $isError ? '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>' : '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>' ?>
      </svg>
      <?= $msg ?>
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
            <th>Review</th>
            <th>Photos</th>
            <th>Status</th>
            <th>Date</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reviews)): ?>
            <tr>
              <td colspan="8" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">
                <?php if ($statusFilter): ?>
                  No <?= $statusFilter ?> reviews found.
                <?php else: ?>
                  No product reviews yet.
                <?php endif; ?>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($reviews as $review): ?>
              <tr>
                <td>
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #D4AF37, #B8960B); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;">
                      <?= strtoupper(substr($review['customer_name'], 0, 1)) ?>
                    </div>
                    <div>
                      <div style="font-weight: 600; font-size: 13px;"><?= sanitize($review['customer_name']) ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <div style="font-weight: 600; font-size: 13px;"><?= sanitize($review['product_name'] ?? 'N/A') ?></div>
                  <?php if (!empty($review['product_price'])): ?>
                    <div style="font-size: 12px; color: #9A8E7E;">₹<?= number_format($review['product_price']) ?></div>
                  <?php endif; ?>
                </td>
                <td class="rev-stars-cell">
                  <?= str_repeat('★', $review['rating']) ?><?= str_repeat('☆', 5 - $review['rating']) ?>
                </td>
                <td>
                  <?php if (!empty($review['title'])): ?>
                    <div class="rev-title"><?= sanitize($review['title']) ?></div>
                  <?php endif; ?>
                  <div class="rev-comment"><?= sanitize($review['comment'] ?? '') ?></div>
                </td>
                <td>
                  <?php if (!empty($review['images'])): ?>
                    <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                      <?php foreach ($review['images'] as $img): ?>
                        <a href="<?= htmlspecialchars($img) ?>" target="_blank" style="display: block; width: 48px; height: 48px; border-radius: 6px; overflow: hidden; border: 1px solid #E5E7EB;">
                          <img src="<?= htmlspecialchars($img) ?>" alt="Review photo" style="width: 100%; height: 100%; object-fit: cover;">
                        </a>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <span style="color: #9A8E7E; font-size: 12px;">No photos</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="status-badge <?= $review['is_approved'] ? 'status-active' : 'status-inactive' ?>">
                    <?= $review['is_approved'] ? 'Approved' : 'Pending' ?>
                  </span>
                </td>
                <td style="font-size: 12px; color: #64748b; white-space: nowrap;">
                  <?= date('M d, Y', strtotime($review['created_at'])) ?>
                  <div style="color: #9A8E7E;"><?= date('g:i A', strtotime($review['created_at'])) ?></div>
                </td>
                <td style="text-align: right;">
                  <div style="display: flex; gap: 6px; justify-content: flex-end;">
                    <?php if (!$review['is_approved']): ?>
                      <a href="<?= adminUrl('reviews/approve.php?id=' . $review['id']) ?>" class="btn btn-secondary btn-sm" style="color: #166534; border-color: #BBF7D0;">Approve</a>
                    <?php else: ?>
                      <a href="<?= adminUrl('reviews/reject.php?id=' . $review['id']) ?>" class="btn btn-secondary btn-sm" style="color: #EA580C; border-color: #FED7AA;">Reject</a>
                    <?php endif; ?>
                    <a href="<?= adminUrl('reviews/delete.php?id=' . $review['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this review permanently?')">Delete</a>
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
