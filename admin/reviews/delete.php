<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0 || !$mysqli) {
  redirect(adminUrl('reviews/?msg=Invalid+review+ID'));
}

$stmt = $mysqli->prepare('DELETE FROM reviews WHERE id = ?');
if (!$stmt) {
  redirect(adminUrl('reviews/?msg=Database+error+occurred'));
}
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  redirect(adminUrl('reviews/?msg=Review+deleted+successfully'));
} else {
  redirect(adminUrl('reviews/?msg=Review+not+found'));
}
