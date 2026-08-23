<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$error = '';
$success = '';

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $slug = sanitize($_POST['slug'] ?? '');
  $department = sanitize($_POST['department'] ?? 'all');
  $description = sanitize($_POST['description'] ?? '');
  $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
  $sort_order = (int)($_POST['sort_order'] ?? 0);
  $is_active = isset($_POST['is_active']) ? 1 : 0;
  $edit_id = !empty($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

  // Handle Image Upload or URL
  $image = sanitize($_POST['image'] ?? '');
  if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
    $uploaded = handleImageUpload($_FILES['image_file'], 'categories');
    if ($uploaded) {
      $image = $uploaded;
    }
  }

  if (empty($name)) {
    $error = 'Category name is required.';
  } else {
    if (empty($slug)) {
      $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }

    if ($edit_id > 0) {
      $stmt = $mysqli->prepare('UPDATE categories SET name=?, slug=?, department=?, description=?, image=?, parent_id=?, sort_order=?, is_active=? WHERE id=?');
      $stmt->bind_param('sssssiiii', $name, $slug, $department, $description, $image, $parent_id, $sort_order, $is_active, $edit_id);
      if ($stmt->execute()) {
        redirect(adminUrl('categories/?msg=Category+updated+successfully'));
      } else {
        $error = 'Update failed: ' . $mysqli->error;
      }
    } else {
      $stmt = $mysqli->prepare('INSERT INTO categories (name, slug, department, description, image, parent_id, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('sssssiii', $name, $slug, $department, $description, $image, $parent_id, $sort_order, $is_active);
      if ($stmt->execute()) {
        redirect(adminUrl('categories/?msg=Category+created+successfully'));
      } else {
        $error = 'Creation failed: ' . $mysqli->error;
      }
    }
  }
}

// Fetch all categories with parent names
$categories = $mysqli->query("
  SELECT c.*, p.name as parent_name, 
    (SELECT COUNT(*) FROM products prod WHERE prod.category_id = c.id OR prod.subcategory_id = c.id) as product_count
  FROM categories c 
  LEFT JOIN categories p ON c.parent_id = p.id 
  ORDER BY c.parent_id ASC, c.sort_order ASC, c.name ASC
")->fetch_all(MYSQLI_ASSOC);

$parentCategories = array_filter($categories, fn($c) => $c['parent_id'] == 0);

$pageTitle = 'Categories Management — AURA & CO. Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Categories &amp; Subcategories</h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Manage parent collections (Women, Men, Kids, Accessories) and their subcategories (Suits, Sarees, Shirts, etc.).
      </p>
    </div>
    <button class="btn btn-primary" onclick="openAddForm()">+ Add New Category</button>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom: var(--space-6); background: #FEF2F2; color: #991B1B; border: 1px solid #F87171; padding: 12px 16px; border-radius: 8px;">
      <?= sanitize($error) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6); background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; padding: 12px 16px; border-radius: 8px;">
      <?= sanitize($_GET['msg']) ?>
    </div>
  <?php endif; ?>

  <!-- Add / Edit Modal Card -->
  <div class="admin-card" id="categoryForm" style="display: none; margin-bottom: var(--space-6); border: 2px solid var(--color-accent-primary);">
    <div class="admin-card-header" style="background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
      <h2 id="formTitle" style="font-size: 18px; margin: 0;">Add Category</h2>
      <button type="button" class="btn btn-secondary btn-sm" onclick="closeForm()">✕ Close</button>
    </div>
    <form method="POST" action="" enctype="multipart/form-data" style="padding: var(--space-6);">
      <input type="hidden" name="edit_id" id="editId" value="">
      <div class="form-grid">
        <div class="form-group">
          <label>Category Name <span class="required" style="color: #ef4444;">*</span></label>
          <input type="text" name="name" id="catName" required placeholder="e.g. Suits & Salwars" oninput="autoSlugCategory(this.value)">
        </div>

        <div class="form-group">
          <label>URL Slug <span class="required" style="color: #ef4444;">*</span></label>
          <input type="text" name="slug" id="catSlug" required placeholder="e.g. suits-salwars">
        </div>

        <div class="form-group">
          <label>Department / Audience</label>
          <select name="department" id="catDept">
            <option value="women">Women</option>
            <option value="men">Men</option>
            <option value="kids">Kids</option>
            <option value="all">All / Universal</option>
          </select>
        </div>

        <div class="form-group">
          <label>Parent Category</label>
          <select name="parent_id" id="catParent">
            <option value="0">None (Top Level Category)</option>
            <?php foreach ($parentCategories as $pCat): ?>
              <option value="<?= $pCat['id'] ?>">
                <?= sanitize($pCat['name']) ?> (<?= ucfirst($pCat['department']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <small style="color: var(--color-text-tertiary); font-size: 11px;">Select Parent to make this a Subcategory</small>
        </div>

        <div class="form-group full-width">
          <label>Description</label>
          <textarea name="description" id="catDesc" rows="2" placeholder="Brief description for category banner..."></textarea>
        </div>

        <div class="form-group">
          <label>Category Banner / Image URL</label>
          <input type="text" name="image" id="catImage" placeholder="https://...">
        </div>

        <div class="form-group">
          <label>Or Upload Image File</label>
          <input type="file" name="image_file" accept="image/*">
        </div>

        <div class="form-group">
          <label>Sort Order</label>
          <input type="number" name="sort_order" id="catSort" value="0">
        </div>

        <div class="form-group" style="display: flex; align-items: center; padding-top: 24px;">
          <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
            <input type="checkbox" name="is_active" id="catActive" value="1" checked style="width: 18px; height: 18px;">
            <span>Active &amp; Visible in Store Menu</span>
          </label>
        </div>
      </div>

      <div class="form-actions" style="margin-top: var(--space-4); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn btn-secondary" onclick="closeForm()">Cancel</button>
        <button type="submit" class="btn btn-primary" id="submitBtn">Save Category</button>
      </div>
    </form>
  </div>

  <!-- Categories Table -->
  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Category Name</th>
            <th>Slug</th>
            <th>Department</th>
            <th>Type / Parent</th>
            <th>Products Count</th>
            <th>Sort Order</th>
            <th>Status</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($categories)): ?>
            <tr>
              <td colspan="8" style="text-align: center; padding: 40px; color: var(--color-text-tertiary);">
                No categories found. Click "+ Add New Category" above to create one.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($categories as $cat): ?>
              <?php
                $isSub = $cat['parent_id'] > 0;
                $deptBadge = match($cat['department']) {
                  'women' => 'background: #FCE7F3; color: #9D174D;',
                  'men' => 'background: #E0E7FF; color: #3730A3;',
                  'kids' => 'background: #FEF3C7; color: #92400E;',
                  default => 'background: #F3F4F6; color: #374151;'
                };
              ?>
              <tr style="<?= !$isSub ? 'background: #fafaf9; font-weight: 600;' : '' ?>">
                <td>
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <?php if ($isSub): ?>
                      <span style="color: var(--color-text-tertiary); font-family: monospace; font-size: 16px; margin-left: 12px;">↳</span>
                    <?php endif; ?>
                    <?php if (!empty($cat['image'])): ?>
                      <img src="<?= htmlspecialchars($cat['image']) ?>" alt="" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px;">
                    <?php endif; ?>
                    <span><?= sanitize($cat['name']) ?></span>
                  </div>
                </td>
                <td style="font-family: monospace; font-size: 12px; color: #475569;">
                  <?= sanitize($cat['slug']) ?>
                </td>
                <td>
                  <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 4px; <?= $deptBadge ?>">
                    <?= ucfirst($cat['department']) ?>
                  </span>
                </td>
                <td>
                  <?php if ($isSub): ?>
                    <span style="color: var(--color-text-secondary); font-size: 12px;">Subcategory of <strong><?= sanitize($cat['parent_name'] ?? 'Parent') ?></strong></span>
                  <?php else: ?>
                    <span style="color: #0284c7; font-weight: 600; font-size: 12px;">★ Top Level (Parent)</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span style="font-size: 12px; font-weight: 600; background: #f1f5f9; padding: 2px 8px; border-radius: 12px;">
                    <?= number_format($cat['product_count']) ?> items
                  </span>
                </td>
                <td><?= $cat['sort_order'] ?></td>
                <td>
                  <span class="status-badge <?= $cat['is_active'] ? 'status-active' : 'status-inactive' ?>">
                    <?= $cat['is_active'] ? 'Active' : 'Inactive' ?>
                  </span>
                </td>
                <td style="text-align: right;">
                  <div style="display: flex; gap: 6px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)">
                      Edit
                    </button>
                    <a href="<?= adminUrl('categories/delete.php?id=' . $cat['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete category \'<?= addslashes(sanitize($cat['name'])) ?>\'?')">
                      Delete
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function autoSlugCategory(name) {
  const slug = name.toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, '')
    .replace(/[\s_-]+/g, '-')
    .replace(/^-+|-+$/g, '');
  document.getElementById('catSlug').value = slug;
}

function openAddForm() {
  document.getElementById('categoryForm').style.display = 'block';
  document.getElementById('formTitle').textContent = 'Add New Category / Subcategory';
  document.getElementById('submitBtn').textContent = 'Save Category';
  document.getElementById('editId').value = '';
  document.getElementById('catName').value = '';
  document.getElementById('catSlug').value = '';
  document.getElementById('catDept').value = 'women';
  document.getElementById('catParent').value = '0';
  document.getElementById('catDesc').value = '';
  document.getElementById('catImage').value = '';
  document.getElementById('catSort').value = '0';
  document.getElementById('catActive').checked = true;
  document.getElementById('categoryForm').scrollIntoView({ behavior: 'smooth' });
}

function editCategory(cat) {
  document.getElementById('categoryForm').style.display = 'block';
  document.getElementById('formTitle').textContent = 'Edit Category: ' + cat.name;
  document.getElementById('submitBtn').textContent = 'Update Category';
  document.getElementById('editId').value = cat.id;
  document.getElementById('catName').value = cat.name;
  document.getElementById('catSlug').value = cat.slug;
  document.getElementById('catDept').value = cat.department || 'women';
  document.getElementById('catParent').value = cat.parent_id || '0';
  document.getElementById('catDesc').value = cat.description || '';
  document.getElementById('catImage').value = cat.image || '';
  document.getElementById('catSort').value = cat.sort_order || '0';
  document.getElementById('catActive').checked = cat.is_active == 1;
  document.getElementById('categoryForm').scrollIntoView({ behavior: 'smooth' });
}

function closeForm() {
  document.getElementById('categoryForm').style.display = 'none';
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
