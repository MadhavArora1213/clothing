<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
$stmt = $mysqli->prepare('SELECT * FROM enquiries WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$enquiry = $stmt->get_result()->fetch_assoc();

if (!$enquiry) redirect('/admin/enquiries/');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $status = sanitize($_POST['status'] ?? $enquiry['status']);
  $stmt = $mysqli->prepare('UPDATE enquiries SET status = ?, updated_at = NOW() WHERE id = ?');
  $stmt->bind_param('si', $status, $id);
  $stmt->execute();
  redirect('/admin/enquiries/view.php?id=' . $id);
}

$pageTitle = 'Enquiry #' . $id . ' — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <h1>Enquiry #<?= $id ?></h1>
    <a href="/admin/enquiries/" class="btn btn-secondary">&larr; Back to Enquiries</a>
  </div>

  <div class="admin-card" style="margin-bottom: var(--space-6);">
    <div class="admin-card-header"><h2>Enquiry Details</h2></div>
    <div style="padding: var(--space-6);">
      <p><strong>Name:</strong> <?= sanitize($enquiry['name']) ?></p>
      <p><strong>Email:</strong> <?= sanitize($enquiry['email']) ?></p>
      <p><strong>Phone:</strong> <?= sanitize($enquiry['phone'] ?? 'N/A') ?></p>
      <p><strong>Subject:</strong> <?= sanitize($enquiry['subject']) ?></p>
      <p><strong>Status:</strong> <span class="status-badge status-<?= $enquiry['status'] ?>"><?= ucfirst(str_replace('_', ' ', $enquiry['status'])) ?></span></p>
      <p><strong>Received:</strong> <?= date('F d, Y h:i A', strtotime($enquiry['created_at'])) ?></p>
      <div style="margin-top: var(--space-4); padding: var(--space-4); background: var(--color-bg-primary); border-radius: var(--radius-md);">
        <strong>Message:</strong>
        <p style="margin-top: var(--space-2); white-space: pre-wrap;"><?= sanitize($enquiry['message']) ?></p>
      </div>
    </div>
  </div>

  <div class="admin-card">
    <div class="admin-card-header"><h2>Update Status</h2></div>
    <form method="POST" style="padding: var(--space-6);">
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="new" <?= $enquiry['status'] === 'new' ? 'selected' : '' ?>>New</option>
          <option value="in_progress" <?= $enquiry['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
          <option value="resolved" <?= $enquiry['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
        </select>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Status</button>
      </div>
    </form>
  </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
