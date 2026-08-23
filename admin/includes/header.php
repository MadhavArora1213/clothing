<?php
$pageTitle = $pageTitle ?? 'AURA & CO. Admin';
require_once dirname(__DIR__, 2) . '/config/database.php';
$admin = getAdmin();
if (!$admin && basename($_SERVER['PHP_SELF']) !== 'login.php') {
  redirect(adminUrl('login.php'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= sanitize($pageTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= siteUrl('css/style.css?v=' . time()) ?>">
  <link rel="stylesheet" href="<?= siteUrl('css/admin.css?v=' . time()) ?>">
</head>
<body class="<?= $bodyClass ?? '' ?>">
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
      <a href="<?= adminUrl('index.php') ?>" class="sidebar-logo">A</a>
      <span class="sidebar-brand">AURA &amp; CO.<br><small>Admin Fashion Studio</small></span>
    </div>
    <nav class="sidebar-nav">
      <a href="<?= adminUrl('index.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' && !str_contains($_SERVER['PHP_SELF'], 'products') && !str_contains($_SERVER['PHP_SELF'], 'categories') && !str_contains($_SERVER['PHP_SELF'], 'orders') && !str_contains($_SERVER['PHP_SELF'], 'customers') && !str_contains($_SERVER['PHP_SELF'], 'enquiries') && !str_contains($_SERVER['PHP_SELF'], 'coupons') && !str_contains($_SERVER['PHP_SELF'], 'reviews') && !str_contains($_SERVER['PHP_SELF'], 'settings') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
        Dashboard
      </a>
      <a href="<?= adminUrl('products/') ?>" class="<?= str_contains($_SERVER['PHP_SELF'], '/products') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
        Products
      </a>
      <a href="<?= adminUrl('categories/') ?>" class="<?= str_contains($_SERVER['PHP_SELF'], '/categories') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
        Categories
      </a>
      <a href="<?= adminUrl('orders/') ?>" class="<?= str_contains($_SERVER['PHP_SELF'], '/orders') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
        Orders
        <?php
          if ($mysqli) {
            $pendingRes = $mysqli->query('SELECT COUNT(*) as c FROM orders WHERE order_status = "pending"');
            $pending = $pendingRes ? $pendingRes->fetch_assoc() : ['c' => 0];
            if (!empty($pending['c']) && $pending['c'] > 0): ?>
              <span class="sidebar-badge"><?= number_format($pending['c']) ?></span>
          <?php endif; } ?>
      </a>
      <a href="<?= adminUrl('customers/') ?>" class="<?= str_contains($_SERVER['PHP_SELF'], '/customers') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Customers
      </a>
      <a href="<?= adminUrl('enquiries/') ?>" class="<?= str_contains($_SERVER['PHP_SELF'], '/enquiries') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Enquiries
        <?php
          if ($mysqli) {
            $newEnqRes = $mysqli->query('SELECT COUNT(*) as c FROM enquiries WHERE status = "new"');
            $newEnq = $newEnqRes ? $newEnqRes->fetch_assoc() : ['c' => 0];
            if (!empty($newEnq['c']) && $newEnq['c'] > 0): ?>
              <span class="sidebar-badge"><?= number_format($newEnq['c']) ?></span>
          <?php endif; } ?>
      </a>
      <a href="<?= adminUrl('coupons/') ?>" class="<?= str_contains($_SERVER['PHP_SELF'], '/coupons') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 5H3a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z"/><path d="M21 12H3"/></svg>
        Coupons
      </a>
      <a href="<?= adminUrl('reviews/') ?>" class="<?= str_contains($_SERVER['PHP_SELF'], '/reviews') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        Reviews
      </a>
    </nav>
    <div class="sidebar-footer">
      <a href="<?= adminUrl('settings/') ?>" class="<?= str_contains($_SERVER['PHP_SELF'], '/settings') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
        Settings
      </a>
      <a href="<?= adminUrl('logout.php') ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
        Logout
      </a>
    </div>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="icon-btn" id="sidebarToggle" aria-label="Toggle sidebar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
      </button>
      <div class="admin-topbar-right">
        <a href="<?= siteUrl('index.php') ?>" target="_blank" class="btn btn-secondary" style="padding: var(--space-2) var(--space-4); font-size: var(--text-caption);">View Store &rarr;</a>
        <div class="admin-user">
          <div class="admin-avatar"><?= $admin ? strtoupper(substr($admin['name'], 0, 1)) : 'A' ?></div>
          <span><?= $admin ? sanitize($admin['name']) : 'Admin' ?></span>
        </div>
      </div>
    </header>
