<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0 || !$mysqli) {
  redirect(adminUrl('reviews/?msg=Invalid+review+ID'));
}

$stmt = $mysqli->prepare('UPDATE reviews SET is_approved = 1 WHERE id = ?');
if (!$stmt) {
  redirect(adminUrl('reviews/?msg=Database+error+occurred'));
}
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  redirect(adminUrl('reviews/?msg=Review+approved+successfully'));
} else {
  redirect(adminUrl('reviews/?msg=Review+not+found+or+already+approved'));
}
