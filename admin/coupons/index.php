<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$coupons = $mysqli->query('SELECT * FROM coupons ORDER BY created_at DESC')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Coupons — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <h1>Coupons & Discounts</h1>
    <button class="btn btn-primary" onclick="document.getElementById('couponForm').style.display='block';">+ Add Coupon</button>
  </div>

  <div class="admin-card" id="couponForm" style="display: none; margin-bottom: var(--space-6);">
    <div class="admin-card-header"><h2 id="couponFormTitle">Add Coupon</h2></div>
    <form method="POST" action="">
      <input type="hidden" name="edit_id" id="couponEditId">
      <div class="admin-form-page" style="box-shadow: none; padding: var(--space-6) 0;">
        <div class="form-grid">
          <div class="form-group">
            <label>Coupon Code <span class="required">*</span></label>
            <input type="text" name="code" id="couponCode" required>
          </div>
          <div class="form-group">
            <label>Type <span class="required">*</span></label>
            <select name="type" id="couponType">
              <option value="percentage">Percentage</option>
              <option value="fixed">Fixed Amount</option>
            </select>
          </div>
          <div class="form-group">
            <label>Discount Value <span class="required">*</span></label>
            <input type="number" step="0.01" name="discount_value" id="couponValue" required>
          </div>
          <div class="form-group">
            <label>Minimum Order Amount</label>
            <input type="number" step="0.01" name="minimum_order_amount" id="couponMin" value="0">
          </div>
          <div class="form-group">
            <label>Max Discount Amount</label>
            <input type="number" step="0.01" name="maximum_discount_amount" id="couponMax">
          </div>
          <div class="form-group">
            <label>Usage Limit</label>
            <input type="number" name="usage_limit" id="couponLimit">
          </div>
          <div class="form-group">
            <label>Per User Limit</label>
            <input type="number" name="per_user_limit" id="couponPerUser" value="1">
          </div>
          <div class="form-group">
            <label>Starts At</label>
            <input type="datetime-local" name="starts_at" id="couponStarts">
          </div>
          <div class="form-group">
            <label>Expires At</label>
            <input type="datetime-local" name="expires_at" id="couponExpires">
          </div>
          <div class="form-group">
            <label><input type="checkbox" name="is_active" id="couponActive" checked> Active</label>
          </div>
        </div>
        <div class="form-actions" style="border: none; margin: 0; padding: 0;">
          <button type="button" class="btn btn-secondary" onclick="document.getElementById('couponForm').style.display='none';">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Coupon</button>
        </div>
      </div>
    </form>
  </div>

  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Code</th>
            <th>Type</th>
            <th>Value</th>
            <th>Min Order</th>
            <th>Used / Limit</th>
            <th>Expires</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($coupons)): ?>
            <tr><td colspan="8" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">No coupons yet.</td></tr>
          <?php else: ?>
            <?php foreach ($coupons as $coupon): ?>
              <tr>
                <td style="font-weight: 600;"><?= sanitize($coupon['code']) ?></td>
                <td><?= ucfirst($coupon['type']) ?></td>
                <td><?= $coupon['type'] === 'percentage' ? $coupon['discount_value'] . '%' : formatPrice($coupon['discount_value']) ?></td>
                <td><?= formatPrice($coupon['minimum_order_amount']) ?></td>
                <td><?= number_format($coupon['usage_count']) ?> / <?= $coupon['usage_limit'] ?: '∞' ?></td>
                <td><?= $coupon['expires_at'] ? date('M d, Y', strtotime($coupon['expires_at'])) : 'Never' ?></td>
                <td><span class="status-badge <?= $coupon['is_active'] ? 'status-active' : 'status-inactive' ?>"><?= $coupon['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <button class="btn btn-secondary btn-sm" onclick="editCoupon(<?= $coupon['id'] ?>, '<?= sanitize($coupon['code']) ?>', '<?= $coupon['type'] ?>', <?= $coupon['discount_value'] ?>, <?= $coupon['minimum_order_amount'] ?>, <?= $coupon['maximum_discount_amount'] ?? 0 ?>, <?= $coupon['usage_limit'] ?? 0 ?>, <?= $coupon['per_user_limit'] ?>, '<?= $coupon['starts_at'] ?? '' ?>', '<?= $coupon['expires_at'] ?? '' ?>', <?= $coupon['is_active'] ?>)">Edit</button>
                    <a href="/admin/coupons/delete.php?id=<?= $coupon['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this coupon?')">Delete</a>
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

<script>
function editCoupon(id, code, type, value, min, max, limit, perUser, starts, expires, active) {
  document.getElementById('couponForm').style.display = 'block';
  document.getElementById('couponFormTitle').textContent = 'Edit Coupon';
  document.getElementById('couponEditId').value = id;
  document.getElementById('couponCode').value = code;
  document.getElementById('couponType').value = type;
  document.getElementById('couponValue').value = value;
  document.getElementById('couponMin').value = min;
  document.getElementById('couponMax').value = max;
  document.getElementById('couponLimit').value = limit;
  document.getElementById('couponPerUser').value = perUser;
  document.getElementById('couponStarts').value = starts;
  document.getElementById('couponExpires').value = expires;
  document.getElementById('couponActive').checked = active == 1;
  document.getElementById('couponForm').scrollIntoView({ behavior: 'smooth' });
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
