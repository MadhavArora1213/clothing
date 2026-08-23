<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
  $stmt = $mysqli->prepare('UPDATE reviews SET is_approved = 0 WHERE id = ?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
}
redirect('/admin/reviews/');
