<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$settings = $mysqli->query('SELECT * FROM settings ORDER BY group_name, `key`')->fetch_all(MYSQLI_ASSOC);
$settingsByGroup = [];
foreach ($settings as $setting) {
  $settingsByGroup[$setting['group_name']][] = $setting;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['settings']) && is_array($_POST['settings'])) {
    foreach ($_POST['settings'] as $key => $value) {
      $stmt = $mysqli->prepare('UPDATE settings SET value = ? WHERE `key` = ?');
      $stmt->bind_param('ss', $value, $key);
      $stmt->execute();
    }
  }

  // Handle Admin Password Change
  if (!empty($_POST['new_password'])) {
    $currentPass = $_POST['current_password'] ?? '';
    $newPass = $_POST['new_password'];
    $confirmPass = $_POST['confirm_password'] ?? '';

    if ($newPass !== $confirmPass) {
      $error = 'New passwords do not match.';
    } elseif (strlen($newPass) < 6) {
      $error = 'New password must be at least 6 characters.';
    } else {
      $admin = getAdmin();
      $hash = password_hash($newPass, PASSWORD_DEFAULT);
      $pStmt = $mysqli->prepare('UPDATE admins SET password = ? WHERE id = ?');
      $pStmt->bind_param('si', $hash, $_SESSION['admin_id']);
      $pStmt->execute();
      $success = 'Password changed and store settings updated successfully.';
    }
  } else {
    $success = 'Store configuration settings updated successfully.';
  }

  // Reload settings
  $settings = $mysqli->query('SELECT * FROM settings ORDER BY group_name, `key`')->fetch_all(MYSQLI_ASSOC);
  $settingsByGroup = [];
  foreach ($settings as $setting) {
    $settingsByGroup[$setting['group_name']][] = $setting;
  }
}

$pageTitle = 'Store Settings — urban outfit Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Store Configuration &amp; Settings</h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Manage business contact, brand name, currency symbol, and admin credentials.
      </p>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom: var(--space-6); background: #FEF2F2; color: #991B1B; border: 1px solid #F87171; padding: 12px 16px; border-radius: 8px;">
      <?= sanitize($error) ?>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6); background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; padding: 12px 16px; border-radius: 8px;">
      <?= sanitize($success) ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="admin-card" style="margin-bottom: var(--space-6); padding: var(--space-6);">
      <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; border-bottom: 1px solid var(--color-bg-elevated); padding-bottom: 8px;">
        Store Brand &amp; Contact Info
      </h2>
      <div class="form-grid">
        <?php foreach ($settings as $setting): ?>
          <div class="form-group">
            <label><?= ucwords(str_replace('_', ' ', $setting['key'])) ?></label>
            <input type="text" name="settings[<?= $setting['key'] ?>]" value="<?= sanitize($setting['value']) ?>">
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Admin Account Security -->
    <div class="admin-card" style="margin-bottom: var(--space-6); padding: var(--space-6);">
      <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; border-bottom: 1px solid var(--color-bg-elevated); padding-bottom: 8px;">
        Administrator Password &amp; Security
      </h2>
      <div class="form-grid">
        <div class="form-group">
          <label>New Password (Optional)</label>
          <input type="password" name="new_password" placeholder="Leave blank to keep unchanged">
        </div>
        <div class="form-group">
          <label>Confirm New Password</label>
          <input type="password" name="confirm_password" placeholder="Repeat new password">
        </div>
      </div>
    </div>

    <div class="form-actions" style="display: flex; justify-content: flex-end;">
      <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Save All Settings</button>
    </div>
  </form>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
