<?php
$pageTitle = 'Returns & Refunds — ATELIER';
$pageDescription = 'Learn about our return and refund policies.';
include dirname(__DIR__) . '/includes/header.php';
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div class="section-header">
      <h1 class="section-title">Returns & Refunds</h1>
    </div>
    <div class="admin-form-page">
      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin-bottom: var(--space-4);">Return Policy</h2>
      <p style="color: var(--color-text-secondary); margin-bottom: var(--space-6);">We want you to be completely satisfied with your purchase. If you are not happy with your order, we accept returns within <?= (int)getSetting('return_days', 30) ?> days of delivery.</p>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-4);">Conditions for Return</h2>
      <ul style="color: var(--color-text-secondary); margin-bottom: var(--space-6); padding-left: var(--space-6);">
        <li style="margin-bottom: var(--space-2);">Items must be unworn, unwashed, and with original tags attached</li>
        <li style="margin-bottom: var(--space-2);">Original packaging must be intact</li>
        <li style="margin-bottom: var(--space-2);">Sale items are non-returnable unless defective</li>
        <li>Underwear and swimwear cannot be returned for hygiene reasons</li>
      </ul>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-4);">Refund Process</h2>
      <p style="color: var(--color-text-secondary); margin-bottom: var(--space-6);">Once we receive your return, we will inspect the item and process your refund within 5-7 business days. The refund will be issued to the original payment method.</p>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-4);">How to Initiate a Return</h2>
      <p style="color: var(--color-text-secondary);">To initiate a return, please contact our customer support team at <?= sanitize(getSetting('site_email', 'hello@atelier.com')) ?> or call us at <?= sanitize(getSetting('site_phone', '+91 98765 43210')) ?>.</p>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
