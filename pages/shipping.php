<?php
$pageTitle = 'Shipping Policy — ATELIER';
$pageDescription = 'Learn about our shipping policies and delivery timelines.';
include dirname(__DIR__) . '/includes/header.php';
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div class="section-header">
      <h1 class="section-title">Shipping Policy</h1>
    </div>
    <div class="admin-form-page">
      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin-bottom: var(--space-4);">Delivery Timelines</h2>
      <p style="color: var(--color-text-secondary); margin-bottom: var(--space-6);">We strive to deliver your orders as quickly as possible. Here are our standard delivery timelines:</p>
      <ul style="color: var(--color-text-secondary); margin-bottom: var(--space-6); padding-left: var(--space-6);">
        <li style="margin-bottom: var(--space-2);"><strong>Standard Shipping:</strong> 4-6 business days</li>
        <li style="margin-bottom: var(--space-2);"><strong>Express Shipping:</strong> 2-3 business days</li>
        <li><strong>Free Shipping:</strong> Available on orders above <?= formatPrice(getSetting('shipping_free_min', 1999)) ?></li>
      </ul>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-4);">Shipping Charges</h2>
      <ul style="color: var(--color-text-secondary); margin-bottom: var(--space-6); padding-left: var(--space-6);">
        <li style="margin-bottom: var(--space-2);"><strong>Standard Shipping:</strong> <?= formatPrice(getSetting('shipping_standard', 149)) ?></li>
        <li><strong>Express Shipping:</strong> <?= formatPrice(getSetting('shipping_express', 299)) ?></li>
      </ul>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-4);">Order Tracking</h2>
      <p style="color: var(--color-text-secondary);">Once your order is shipped, you will receive a tracking number via email and SMS. You can use this tracking number to track your order on our website or the courier's website.</p>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
