<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect(adminUrl('enquiries/'));

$stmt = $mysqli->prepare('SELECT * FROM enquiries WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$enquiry = $stmt->get_result()->fetch_assoc();

if (!$enquiry) redirect(adminUrl('enquiries/'));

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $status = sanitize($_POST['status'] ?? $enquiry['status']);
  $reply = sanitize($_POST['reply'] ?? '');
  
  $stmt = $mysqli->prepare('UPDATE enquiries SET status = ?, reply = ?, updated_at = NOW() WHERE id = ?');
  $stmt->bind_param('ssi', $status, $reply, $id);
  $stmt->execute();

  $success = 'Enquiry status and resolution notes updated.';
  
  $enquiry['status'] = $status;
  $enquiry['reply'] = $reply;
}

$pageTitle = 'Enquiry #' . $id . ' — AURA & CO. Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Enquiry #<?= $id ?>: <?= sanitize($enquiry['subject']) ?></h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Received on <?= date('F d, Y \a\t h:i A', strtotime($enquiry['created_at'])) ?>
      </p>
    </div>
    <a href="<?= adminUrl('enquiries/') ?>" class="btn btn-secondary">&larr; Back to Enquiries</a>
  </div>

  <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6); background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; padding: 12px 16px; border-radius: 8px; font-weight: 500;">
      <?= sanitize($success) ?>
    </div>
  <?php endif; ?>

  <div class="admin-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-6);">
    <div class="admin-card" style="padding: var(--space-6);">
      <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Customer Message</h2>
      
      <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
        <p style="font-size: 14px; line-height: 1.6; color: #0f172a; white-space: pre-wrap; margin: 0;"><?= sanitize($enquiry['message']) ?></p>
      </div>

      <div style="font-size: 13px; color: var(--color-text-secondary); line-height: 1.6;">
        <p><strong>Sender Name:</strong> <?= sanitize($enquiry['name']) ?></p>
        <p><strong>Email Address:</strong> <a href="mailto:<?= sanitize($enquiry['email']) ?>"><?= sanitize($enquiry['email']) ?></a></p>
        <p><strong>Phone Number:</strong> <?= sanitize($enquiry['phone'] ?? 'Not provided') ?></p>
      </div>
    </div>

    <div class="admin-card" style="padding: var(--space-6);">
      <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Resolution &amp; Reply</h2>
      <form method="POST" action="">
        <div class="form-group" style="margin-bottom: 14px;">
          <label>Status</label>
          <select name="status" style="font-weight: 600;">
            <option value="new" <?= $enquiry['status'] === 'new' ? 'selected' : '' ?>>New / Unopened</option>
            <option value="in_progress" <?= $enquiry['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress / Contacted</option>
            <option value="resolved" <?= $enquiry['status'] === 'resolved' ? 'selected' : '' ?>>Resolved / Closed</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
          <label>Admin Reply / Resolution Notes</label>
          <textarea name="reply" rows="4" placeholder="Notes about resolution or copy of email reply sent to customer..."><?= sanitize($enquiry['reply'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">
          Save Status &amp; Notes
        </button>
      </form>
    </div>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
