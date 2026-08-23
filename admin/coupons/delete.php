<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
  $stmt = $mysqli->prepare('DELETE FROM coupons WHERE id = ?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
}

redirect(adminUrl('coupons/?msg=Coupon+deleted'));
