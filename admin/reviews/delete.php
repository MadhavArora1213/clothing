<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
  $stmt = $mysqli->prepare('DELETE FROM reviews WHERE id = ?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
}
redirect('/admin/reviews/');
