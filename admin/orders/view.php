<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  redirect(adminUrl('orders/'));
}

$stmt = $mysqli->prepare('SELECT o.*, c.first_name, c.last_name, c.email as cust_email, c.phone as cust_phone 
                          FROM orders o 
                          LEFT JOIN customers c ON o.customer_id = c.id 
                          WHERE o.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
  redirect(adminUrl('orders/'));
}

// Fetch Items with product images
$items = $mysqli->query("
  SELECT oi.*, p.image as fallback_img, p.slug as prod_slug 
  FROM order_items oi 
  LEFT JOIN products p ON oi.product_id = p.id 
  WHERE oi.order_id = $id
")->fetch_all(MYSQLI_ASSOC);

// Fetch Status History
$history = $mysqli->query("
  SELECT h.*, a.name as admin_name 
  FROM order_status_history h 
  LEFT JOIN admins a ON h.created_by = a.id 
  WHERE h.order_id = $id 
  ORDER BY h.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$error = '';
$success = '';

// Handle Status & Tracking Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newStatus = sanitize($_POST['order_status'] ?? '');
  $newPaymentStatus = sanitize($_POST['payment_status'] ?? '');
  $trackingNumber = sanitize($_POST['tracking_number'] ?? '');
  $note = sanitize($_POST['note'] ?? '');

  if (!empty($newStatus)) {
    $upStmt = $mysqli->prepare('UPDATE orders SET order_status = ?, payment_status = ?, tracking_number = ?, updated_at = NOW() WHERE id = ?');
    $upStmt->bind_param('sssi', $newStatus, $newPaymentStatus, $trackingNumber, $id);
    $upStmt->execute();

    // Add status history record if status changed or note provided
    if ($newStatus !== $order['order_status'] || !empty($note)) {
      $adminId = $_SESSION['admin_id'] ?? null;
      $histStmt = $mysqli->prepare('INSERT INTO order_status_history (order_id, status, note, created_by) VALUES (?, ?, ?, ?)');
      $histStmt->bind_param('issi', $id, $newStatus, $note, $adminId);
      $histStmt->execute();
    }

    $success = 'Order status and details updated successfully.';
    
    // Refresh order data
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    $history = $mysqli->query("
      SELECT h.*, a.name as admin_name 
      FROM order_status_history h 
      LEFT JOIN admins a ON h.created_by = a.id 
      WHERE h.order_id = $id 
      ORDER BY h.created_at DESC
    ")->fetch_all(MYSQLI_ASSOC);
  }
}

// Decode addresses
$shippingAddr = json_decode($order['shipping_address'] ?? '{}', true) ?: [];
$billingAddr = json_decode($order['billing_address'] ?? '{}', true) ?: [];

$pageTitle = 'Order ' . $order['order_number'] . ' — AURA & CO. Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <div style="display: flex; align-items: center; gap: 12px;">
        <h1>Order: <?= sanitize($order['order_number']) ?></h1>
        <span class="status-badge status-<?= $order['order_status'] ?>" style="font-size: 13px; text-transform: uppercase;">
          <?= ucfirst($order['order_status']) ?>
        </span>
      </div>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Placed on <?= date('F d, Y \a\t h:i A', strtotime($order['created_at'])) ?>
      </p>
    </div>
    <div style="display: flex; gap: 8px;">
      <button type="button" class="btn btn-secondary" onclick="window.print()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Print Invoice
      </button>
      <a href="<?= adminUrl('orders/') ?>" class="btn btn-secondary">&larr; Back to Orders</a>
    </div>
  </div>

  <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6); background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; padding: 12px 16px; border-radius: 8px; font-weight: 500;">
      <?= sanitize($success) ?>
    </div>
  <?php endif; ?>

  <div class="admin-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-6);">
    <!-- Left Column: Items & Customer Details -->
    <div>
      <!-- Order Items Card -->
      <div class="admin-card" style="margin-bottom: var(--space-6);">
        <div class="admin-card-header" style="padding: var(--space-4) var(--space-6); border-bottom: 1px solid var(--color-bg-elevated);">
          <h2 style="font-size: 16px; font-weight: 600; margin: 0;">Ordered Apparel Items (<?= count($items) ?>)</h2>
        </div>
        <div class="table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Color &amp; Swatch</th>
                <th>Size</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th style="text-align: right;">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                      <?php if (!empty($item['fallback_img'])): ?>
                        <img src="<?= htmlspecialchars($item['fallback_img']) ?>" alt="" style="width: 44px; height: 56px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                      <?php endif; ?>
                      <div>
                        <div style="font-weight: 600; font-size: 13px;"><?= sanitize($item['product_name']) ?></div>
                        <small style="color: var(--color-text-tertiary); font-family: monospace; font-size: 11px;">SKU: <?= sanitize($item['product_sku'] ?? 'N/A') ?></small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 6px;">
                      <?php if (!empty($item['color_code'])): ?>
                        <span class="color-swatch-square" style="background-color: <?= htmlspecialchars($item['color_code']) ?>; width: 18px; height: 18px;"></span>
                      <?php endif; ?>
                      <span style="font-size: 13px; font-weight: 500;"><?= sanitize($item['color_name'] ?? 'Standard') ?></span>
                    </div>
                  </td>
                  <td>
                    <span style="font-weight: 600; font-size: 12px; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">
                      <?= sanitize($item['size'] ?? 'Free Size') ?>
                    </span>
                  </td>
                  <td style="font-weight: 600;"><?= (int)$item['quantity'] ?></td>
                  <td><?= formatPrice($item['unit_price']) ?></td>
                  <td style="text-align: right; font-weight: 700;"><?= formatPrice($item['total_price']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Summary Calculation Table -->
        <div style="padding: var(--space-6); background: #fafaf9; border-top: 1px solid var(--color-bg-elevated); display: flex; justify-content: flex-end;">
          <div style="width: 280px; font-size: 14px; display: flex; flex-direction: column; gap: 8px;">
            <div style="display: flex; justify-content: space-between; color: var(--color-text-secondary);">
              <span>Subtotal:</span>
              <span><?= formatPrice($order['subtotal']) ?></span>
            </div>
            <?php if ($order['discount_amount'] > 0): ?>
              <div style="display: flex; justify-content: space-between; color: #16a34a;">
                <span>Discount <?= !empty($order['coupon_code']) ? '(' . sanitize($order['coupon_code']) . ')' : '' ?>:</span>
                <span>-<?= formatPrice($order['discount_amount']) ?></span>
              </div>
            <?php endif; ?>
            <div style="display: flex; justify-content: space-between; color: var(--color-text-secondary);">
              <span>Shipping:</span>
              <span><?= $order['shipping_amount'] > 0 ? formatPrice($order['shipping_amount']) : 'FREE' ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 16px; border-top: 2px solid #e2e8f0; padding-top: 8px; color: #0f172a;">
              <span>Grand Total:</span>
              <span><?= formatPrice($order['grand_total']) ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Shipping & Customer Info Grid -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-6);">
        <!-- Shipping Address Card -->
        <div class="admin-card" style="padding: var(--space-6);">
          <h3 style="font-size: 15px; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Shipping Address
          </h3>
          <p style="font-size: 13px; line-height: 1.6; color: var(--color-text-primary);">
            <strong><?= sanitize($shippingAddr['full_name'] ?? $order['customer_name']) ?></strong><br>
            <?= sanitize($shippingAddr['address_line1'] ?? 'N/A') ?><br>
            <?php if (!empty($shippingAddr['address_line2'])): ?>
              <?= sanitize($shippingAddr['address_line2']) ?><br>
            <?php endif; ?>
            <?= sanitize($shippingAddr['city'] ?? '') ?>, <?= sanitize($shippingAddr['state'] ?? '') ?> - <?= sanitize($shippingAddr['postal_code'] ?? '') ?><br>
            <?= sanitize($shippingAddr['country'] ?? 'India') ?><br>
            <strong>Phone:</strong> <?= sanitize($shippingAddr['phone'] ?? $order['customer_phone']) ?>
          </p>
        </div>

        <!-- Customer Profile Card -->
        <div class="admin-card" style="padding: var(--space-6);">
          <h3 style="font-size: 15px; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Customer Contact
          </h3>
          <p style="font-size: 13px; line-height: 1.6;">
            <strong>Name:</strong> <?= sanitize($order['customer_name']) ?><br>
            <strong>Email:</strong> <a href="mailto:<?= sanitize($order['customer_email']) ?>"><?= sanitize($order['customer_email']) ?></a><br>
            <strong>Phone:</strong> <a href="tel:<?= sanitize($order['customer_phone']) ?>"><?= sanitize($order['customer_phone']) ?></a><br>
            <strong>Payment Method:</strong> <?= strtoupper($order['payment_method']) ?><br>
            <strong>Payment Status:</strong> <span class="status-badge status-<?= $order['payment_status'] ?>"><?= ucfirst($order['payment_status']) ?></span>
          </p>
        </div>
      </div>
    </div>

    <!-- Right Column: Status Pipeline & Timeline -->
    <div>
      <!-- Update Status Card -->
      <div class="admin-card" style="padding: var(--space-6); margin-bottom: var(--space-6);">
        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Update Order Status</h3>
        <form method="POST" action="">
          <div class="form-group" style="margin-bottom: 14px;">
            <label>Order Pipeline Status</label>
            <select name="order_status" style="font-weight: 600;">
              <option value="pending" <?= $order['order_status'] === 'pending' ? 'selected' : '' ?>>Pending Review</option>
              <option value="confirmed" <?= $order['order_status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
              <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>Processing / Packing</option>
              <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>Shipped / In Transit</option>
              <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
              <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
              <option value="returned" <?= $order['order_status'] === 'returned' ? 'selected' : '' ?>>Returned</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 14px;">
            <label>Payment Status</label>
            <select name="payment_status">
              <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending (COD / Unpaid)</option>
              <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid (Received)</option>
              <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
              <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 14px;">
            <label>Tracking Number / Courier AWB</label>
            <input type="text" name="tracking_number" placeholder="e.g. BLUEDART-8934278" value="<?= sanitize($order['tracking_number'] ?? '') ?>">
          </div>

          <div class="form-group" style="margin-bottom: 16px;">
            <label>Admin Note / Remark</label>
            <textarea name="note" rows="2" placeholder="e.g. Packed in warehouse, handed over to courier"></textarea>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%;">
            Update Order Status
          </button>
        </form>
      </div>

      <!-- Status History Timeline Card -->
      <div class="admin-card" style="padding: var(--space-6);">
        <h3 style="font-size: 15px; font-weight: 600; margin-bottom: 14px;">Order History Timeline</h3>
        <?php if (empty($history)): ?>
          <p style="color: var(--color-text-tertiary); font-size: 13px;">No history records recorded yet.</p>
        <?php else: ?>
          <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($history as $h): ?>
              <div style="border-left: 2px solid #0284c7; padding-left: 12px; margin-bottom: 8px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                  <span class="status-badge status-<?= $h['status'] ?>" style="font-size: 10px; text-transform: uppercase;">
                    <?= ucfirst($h['status']) ?>
                  </span>
                  <small style="color: var(--color-text-tertiary); font-size: 11px;">
                    <?= date('M d, h:i A', strtotime($h['created_at'])) ?>
                  </small>
                </div>
                <?php if (!empty($h['note'])): ?>
                  <p style="font-size: 12px; color: var(--color-text-secondary); margin-top: 4px;"><?= sanitize($h['note']) ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
