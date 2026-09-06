<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0 || !$mysqli) {
  redirect(adminUrl('reviews/?msg=Invalid+review+ID'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    redirect(adminUrl('reviews/?msg=Invalid+request'));
  }
  $stmt = $mysqli->prepare('UPDATE reviews SET is_approved = 0 WHERE id = ?');
  if ($stmt) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
      redirect(adminUrl('reviews/?msg=Review+rejected+successfully'));
    } else {
      redirect(adminUrl('reviews/?msg=Review+not+found+or+already+rejected'));
    }
  }
}
redirect(adminUrl('reviews/?msg=Invalid+request'));
