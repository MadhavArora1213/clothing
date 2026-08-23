<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
  $stmt = $mysqli->prepare('UPDATE reviews SET is_approved = 1 WHERE id = ?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
}

redirect(adminUrl('reviews/?msg=Review+approved'));
