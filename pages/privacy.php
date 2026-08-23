<?php
$pageTitle = 'Privacy Policy — ATELIER';
$pageDescription = 'Learn how we collect, use, and protect your personal information.';
include dirname(__DIR__) . '/includes/header.php';
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div class="section-header">
      <h1 class="section-title">Privacy Policy</h1>
    </div>
    <div class="admin-form-page">
      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin-bottom: var(--space-4);">Information We Collect</h2>
      <p style="color: var(--color-text-secondary); margin-bottom: var(--space-6);">We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us. This includes your name, email address, phone number, and shipping address.</p>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-4);">How We Use Your Information</h2>
      <p style="color: var(--color-text-secondary); margin-bottom: var(--space-6);">We use the information we collect to process your orders, communicate with you about your purchases, improve our services, and send you promotional offers (with your consent).</p>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-4);">Data Security</h2>
      <p style="color: var(--color-text-secondary); margin-bottom: var(--space-6);">We implement appropriate security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction.</p>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-4);">Contact Us</h2>
      <p style="color: var(--color-text-secondary);">If you have any questions about this Privacy Policy, please contact us at <?= sanitize(getSetting('site_email', 'hello@atelier.com')) ?>.</p>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
