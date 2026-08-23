<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
  $mysqli->query("DELETE FROM coupons WHERE id = $id");
}
redirect('/admin/coupons/');
