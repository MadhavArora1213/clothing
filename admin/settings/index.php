<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$settings = $mysqli->query('SELECT * FROM settings ORDER BY group_name, `key`')->fetch_all(MYSQLI_ASSOC);
$settingsByGroup = [];
foreach ($settings as $setting) {
  $settingsByGroup[$setting['group_name']][] = $setting;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($_POST['settings'] as $key => $value) {
    $stmt = $mysqli->prepare('UPDATE settings SET value = ? WHERE `key` = ?');
    $stmt->bind_param('ss', $value, $key);
    $stmt->execute();
  }
  $success = 'Settings updated successfully.';
}

$pageTitle = 'Settings — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="page-header">
    <h1>Settings</h1>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom: var(--space-6);"><?= sanitize($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6);"><?= sanitize($success) ?></div>
  <?php endif; ?>

  <form method="POST">
    <?php foreach ($settingsByGroup as $group => $groupSettings): ?>
      <div class="admin-card" style="margin-bottom: var(--space-6);">
        <div class="admin-card-header"><h2><?= ucfirst($group) ?></h2></div>
        <div style="padding: var(--space-6);">
          <?php foreach ($groupSettings as $setting): ?>
            <div class="form-group">
              <label><?= ucwords(str_replace('_', ' ', $setting['key'])) ?></label>
              <input type="text" name="settings[<?= $setting['key'] ?>]" value="<?= sanitize($setting['value']) ?>">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save Settings</button>
    </div>
  </form>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
