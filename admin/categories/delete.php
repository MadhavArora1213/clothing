<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
  if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    redirect(adminUrl('categories/?msg=Invalid+request'));
  }
  $stmt = $mysqli->prepare('DELETE FROM categories WHERE id = ?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
}

redirect(adminUrl('categories/?msg=Category+deleted+successfully'));
