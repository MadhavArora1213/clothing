<?php
$pageTitle = 'Contact Us — ATELIER';
$pageDescription = 'Get in touch with us. We would love to hear from you.';
include dirname(__DIR__) . '/includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $email = sanitize($_POST['email'] ?? '');
  $phone = sanitize($_POST['phone'] ?? '');
  $subject = sanitize($_POST['subject'] ?? '');
  $message = sanitize($_POST['message'] ?? '');

  if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    $error = 'Please fill in all required fields.';
  } else {
    $stmt = $mysqli->prepare('INSERT INTO enquiries (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $name, $email, $phone, $subject, $message);
    $stmt->execute();
    $success = 'Thank you for contacting us. We will get back to you within 24 hours.';
  }
}
?>

<main style="padding-top: calc(var(--header-height) + var(--space-16)); padding-bottom: var(--space-16);">
  <div class="container">
    <div class="section-header">
      <h1 class="section-title">Contact Us</h1>
      <p class="section-subtitle">Have a question? We would love to hear from you.</p>
    </div>

    <div class="admin-grid">
      <div class="admin-card">
        <div style="padding: var(--space-8);">
          <h3 style="font-family: var(--font-display); font-size: var(--text-h3); margin-bottom: var(--space-4);">Get in Touch</h3>
          <p style="color: var(--color-text-secondary); margin-bottom: var(--space-6);">Fill out the form and our team will get back to you within 24 hours.</p>

          <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-4);"><?= sanitize($error) ?></div>
          <?php endif; ?>
          <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom: var(--space-4);"><?= sanitize($success) ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="form-grid">
              <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="name" required value="<?= sanitize($_POST['name'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Email Address <span class="required">*</span></label>
                <input type="email" name="email" required value="<?= sanitize($_POST['email'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= sanitize($_POST['phone'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Subject <span class="required">*</span></label>
                <input type="text" name="subject" required value="<?= sanitize($_POST['subject'] ?? '') ?>">
              </div>
              <div class="form-group full-width">
                <label>Message <span class="required">*</span></label>
                <textarea name="message" rows="5" required><?= sanitize($_POST['message'] ?? '') ?></textarea>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>
        </div>
      </div>

      <div>
        <div class="admin-card" style="margin-bottom: var(--space-6);">
          <div style="padding: var(--space-8);">
            <h3 style="font-family: var(--font-display); font-size: var(--text-h3); margin-bottom: var(--space-4);">Contact Information</h3>
            <div style="color: var(--color-text-secondary);">
              <p style="margin-bottom: var(--space-3);"><strong>Email:</strong> <?= sanitize(getSetting('site_email', 'hello@atelier.com')) ?></p>
              <p style="margin-bottom: var(--space-3);"><strong>Phone:</strong> <?= sanitize(getSetting('site_phone', '+91 98765 43210')) ?></p>
              <p><strong>Address:</strong> <?= sanitize(getSetting('site_address', 'Mumbai, India')) ?></p>
            </div>
          </div>
        </div>

        <div class="admin-card">
          <div style="padding: var(--space-8);">
            <h3 style="font-family: var(--font-display); font-size: var(--text-h3); margin-bottom: var(--space-4);">Business Hours</h3>
            <p style="color: var(--color-text-secondary);">Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
