<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $code = strtoupper(sanitize($_POST['code'] ?? ''));
  $type = sanitize($_POST['type'] ?? 'percentage');
  $discount_value = (float)($_POST['discount_value'] ?? 0);
  $minimum_order_amount = (float)($_POST['minimum_order_amount'] ?? 0);
  $maximum_discount_amount = !empty($_POST['maximum_discount_amount']) ? (float)$_POST['maximum_discount_amount'] : null;
  $usage_limit = !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null;
  $is_active = isset($_POST['is_active']) ? 1 : 0;
  $edit_id = !empty($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

  if (empty($code) || $discount_value <= 0) {
    $error = 'Valid coupon code and discount value are required.';
  } else {
    if ($edit_id > 0) {
      $stmt = $mysqli->prepare('UPDATE coupons SET code=?, type=?, discount_value=?, minimum_order_amount=?, maximum_discount_amount=?, usage_limit=?, is_active=? WHERE id=?');
      $stmt->bind_param('ssdddiis', $code, $type, $discount_value, $minimum_order_amount, $maximum_discount_amount, $usage_limit, $is_active, $edit_id);
      $stmt->execute();
    } else {
      $stmt = $mysqli->prepare('INSERT INTO coupons (code, type, discount_value, minimum_order_amount, maximum_discount_amount, usage_limit, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('ssdddis', $code, $type, $discount_value, $minimum_order_amount, $maximum_discount_amount, $usage_limit, $is_active);
      $stmt->execute();
    }
    redirect(adminUrl('coupons/?msg=Coupon+saved+successfully'));
  }
}

$coupons = $mysqli->query('SELECT * FROM coupons ORDER BY created_at DESC')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Coupons Management — urban outfit Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Discount Coupons &amp; Offers</h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Create promo codes, percentage discounts, and order threshold incentives.
      </p>
    </div>
    <button class="btn btn-primary" onclick="openCouponForm()">+ Add New Coupon</button>
  </div>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6); background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; padding: 12px 16px; border-radius: 8px;">
      <?= sanitize($_GET['msg']) ?>
    </div>
  <?php endif; ?>

  <div class="admin-card" id="couponForm" style="display: none; margin-bottom: var(--space-6); border: 2px solid var(--color-accent-primary);">
    <div class="admin-card-header" style="background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
      <h2 id="couponFormTitle" style="font-size: 18px; margin: 0;">Add Coupon</h2>
      <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('couponForm').style.display='none';">✕ Close</button>
    </div>
    <form method="POST" action="" style="padding: var(--space-6);">
      <input type="hidden" name="edit_id" id="couponEditId">
      <div class="form-grid">
        <div class="form-group">
          <label>Coupon Code <span class="required" style="color: #ef4444;">*</span></label>
          <input type="text" name="code" id="couponCode" required placeholder="e.g. FESTIVE20" style="text-transform: uppercase;">
        </div>

        <div class="form-group">
          <label>Discount Type <span class="required" style="color: #ef4444;">*</span></label>
          <select name="type" id="couponType">
            <option value="percentage">Percentage Discount (%)</option>
            <option value="fixed">Fixed Amount Off (₹)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Discount Value <span class="required" style="color: #ef4444;">*</span></label>
          <input type="number" step="0.01" name="discount_value" id="couponValue" required placeholder="e.g. 20 (for 20%) or 500 (for ₹500)">
        </div>

        <div class="form-group">
          <label>Minimum Order Amount (₹)</label>
          <input type="number" step="0.01" name="minimum_order_amount" id="couponMin" value="0">
        </div>

        <div class="form-group">
          <label>Maximum Cap / Discount Limit (₹)</label>
          <input type="number" step="0.01" name="maximum_discount_amount" id="couponMax" placeholder="Optional cap for percentage">
        </div>

        <div class="form-group">
          <label>Usage Limit (Total times)</label>
          <input type="number" name="usage_limit" id="couponLimit" placeholder="Blank for unlimited">
        </div>

        <div class="form-group" style="display: flex; align-items: center; padding-top: 24px;">
          <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
            <input type="checkbox" name="is_active" id="couponActive" checked style="width: 18px; height: 18px;">
            <span>Active &amp; Redeemable</span>
          </label>
        </div>
      </div>

      <div class="form-actions" style="margin-top: var(--space-4); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('couponForm').style.display='none';">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Coupon</button>
      </div>
    </form>
  </div>

  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Coupon Code</th>
            <th>Discount Type</th>
            <th>Value</th>
            <th>Min Order Amount</th>
            <th>Usage</th>
            <th>Status</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($coupons)): ?>
            <tr><td colspan="7" style="text-align: center; padding: 60px; color: var(--color-text-tertiary);">No promo coupons configured yet.</td></tr>
          <?php else: ?>
            <?php foreach ($coupons as $coupon): ?>
              <tr>
                <td>
                  <strong style="font-family: monospace; font-size: 14px; background: #f1f5f9; padding: 3px 8px; border-radius: 4px; border: 1px dashed #94a3b8;">
                    <?= sanitize($coupon['code']) ?>
                  </strong>
                </td>
                <td><?= ucfirst($coupon['type']) ?></td>
                <td style="font-weight: 700; color: #16a34a;">
                  <?= $coupon['type'] === 'percentage' ? $coupon['discount_value'] . '% OFF' : formatPrice($coupon['discount_value']) . ' OFF' ?>
                </td>
                <td><?= formatPrice($coupon['minimum_order_amount']) ?></td>
                <td><?= number_format($coupon['usage_count']) ?> / <?= $coupon['usage_limit'] ? $coupon['usage_limit'] : 'Unlimited' ?></td>
                <td>
                  <span class="status-badge <?= $coupon['is_active'] ? 'status-active' : 'status-inactive' ?>">
                    <?= $coupon['is_active'] ? 'Active' : 'Inactive' ?>
                  </span>
                </td>
                <td style="text-align: right;">
                  <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="editCoupon(<?= htmlspecialchars(json_encode($coupon)) ?>)">Edit</button>
                    <a href="<?= adminUrl('coupons/delete.php?id=' . $coupon['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this coupon?')">Delete</a>
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
function openCouponForm() {
  document.getElementById('couponForm').style.display = 'block';
  document.getElementById('couponFormTitle').textContent = 'Add New Coupon';
  document.getElementById('couponEditId').value = '';
  document.getElementById('couponCode').value = '';
  document.getElementById('couponType').value = 'percentage';
  document.getElementById('couponValue').value = '';
  document.getElementById('couponMin').value = '0';
  document.getElementById('couponMax').value = '';
  document.getElementById('couponLimit').value = '';
  document.getElementById('couponActive').checked = true;
  document.getElementById('couponForm').scrollIntoView({ behavior: 'smooth' });
}

function editCoupon(c) {
  document.getElementById('couponForm').style.display = 'block';
  document.getElementById('couponFormTitle').textContent = 'Edit Coupon: ' + c.code;
  document.getElementById('couponEditId').value = c.id;
  document.getElementById('couponCode').value = c.code;
  document.getElementById('couponType').value = c.type;
  document.getElementById('couponValue').value = c.discount_value;
  document.getElementById('couponMin').value = c.minimum_order_amount;
  document.getElementById('couponMax').value = c.maximum_discount_amount || '';
  document.getElementById('couponLimit').value = c.usage_limit || '';
  document.getElementById('couponActive').checked = c.is_active == 1;
  document.getElementById('couponForm').scrollIntoView({ behavior: 'smooth' });
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
