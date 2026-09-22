<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
requireAdminAuth();

$error = '';
$success = '';

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $error = 'Invalid request. Please try again.';
  } else {
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
          $error = 'Update failed. Please try again.';
          error_log('Category update failed: ' . $mysqli->error);
        }
      } else {
        $stmt = $mysqli->prepare('INSERT INTO categories (name, slug, department, description, image, parent_id, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssiii', $name, $slug, $department, $description, $image, $parent_id, $sort_order, $is_active);
        if ($stmt->execute()) {
          redirect(adminUrl('categories/?msg=Category+created+successfully'));
        } else {
          $error = 'Creation failed. Please try again.';
          error_log('Category creation failed: ' . $mysqli->error);
        }
      }
    }
  }
}

// Fetch all categories with parent names & product counts
$categories = $mysqli->query("
  SELECT c.*, p.name as parent_name, 
    (SELECT COUNT(*) FROM products prod WHERE prod.category_id = c.id OR prod.subcategory_id = c.id) as product_count
  FROM categories c 
  LEFT JOIN categories p ON c.parent_id = p.id 
  ORDER BY c.parent_id ASC, c.sort_order ASC, c.name ASC
")->fetch_all(MYSQLI_ASSOC);

$parentCategories = array_values(array_filter($categories, fn($c) => $c['parent_id'] == 0));
$subCategories = array_values(array_filter($categories, fn($c) => $c['parent_id'] > 0));

// Selected parent for right panel
$selectedParentId = (int)($_GET['parent_id'] ?? 0);
if ($selectedParentId <= 0 && !empty($parentCategories)) {
  $selectedParentId = (int)$parentCategories[0]['id'];
}
$selectedParent = null;
foreach ($parentCategories as $pc) {
  if ((int)$pc['id'] === $selectedParentId) {
    $selectedParent = $pc;
    break;
  }
}
$subsForSelected = array_values(array_filter($subCategories, fn($c) => (int)$c['parent_id'] === $selectedParentId));

$pageTitle = 'Categories Management — urban outfit Admin';
include dirname(__DIR__) . '/includes/header.php';

function deptBadge($dept) {
  $style = match ($dept) {
    'women' => 'background: #FCE7F3; color: #9D174D;',
    'men' => 'background: #E0E7FF; color: #3730A3;',
    'kids' => 'background: #FEF3C7; color: #92400E;',
    default => 'background: #F3F4F6; color: #374151;',
  };
  return '<span style="font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 4px; ' . $style . '">' . ucfirst($dept) . '</span>';
}
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h1>Categories &amp; Subcategories</h1>
      <p style="color: var(--color-text-secondary); margin-top: 4px;">
        Left: parent collections — Right: their subcategories. Click a parent on the left to manage its children.
      </p>
    </div>
    <button class="btn btn-primary" onclick="openAddForm()">+ Add New Category</button>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom: var(--space-6); background: #FEF2F2; color: #991B1B; border: 1px solid #F87171; padding: 12px 16px; border-radius: 8px;">
      <?= esc($error) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success" style="margin-bottom: var(--space-6); background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; padding: 12px 16px; border-radius: 8px;">
      <?= esc($_GET['msg']) ?>
    </div>
  <?php endif; ?>

  <!-- Add / Edit Modal Card -->
  <div class="admin-card" id="categoryForm" style="display: none; margin-bottom: var(--space-6); border: 2px solid var(--color-accent-primary);">
    <div class="admin-card-header" style="background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
      <h2 id="formTitle" style="font-size: 18px; margin: 0;">Add Category</h2>
      <button type="button" class="btn btn-secondary btn-sm" onclick="closeForm()">✕ Close</button>
    </div>
    <form method="POST" action="" enctype="multipart/form-data" style="padding: var(--space-6);">
      <?= getCSRFInput() ?>
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
                <?= esc($pCat['name']) ?> (<?= ucfirst($pCat['department']) ?>)
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

  <!-- LEFT + RIGHT STRUCTURED LAYOUT -->
  <div style="display: grid; grid-template-columns: 340px 1fr; gap: var(--space-4); align-items: start;">

    <!-- LEFT: Parent Categories -->
    <div class="admin-card" style="overflow: hidden;">
      <div style="padding: var(--space-4) var(--space-5); border-bottom: 1px solid var(--color-bg-elevated); display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size: 15px; font-weight: 700; margin: 0;">Parent Categories</h2>
        <span style="font-size: 12px; font-weight: 600; background: #f1f5f9; padding: 2px 10px; border-radius: 12px; color: #475569;">
          <?= count($parentCategories) ?>
        </span>
      </div>

      <?php if (empty($parentCategories)): ?>
        <div style="padding: 32px 20px; text-align: center; color: var(--color-text-tertiary); font-size: 13px;">
          No parent categories yet.
        </div>
      <?php else: ?>
        <div style="display: flex; flex-direction: column;">
          <?php foreach ($parentCategories as $pc):
            $isActive = ((int)$pc['id'] === $selectedParentId);
            $childCount = count(array_filter($subCategories, fn($c) => (int)$c['parent_id'] === (int)$pc['id']));
          ?>
            <a href="<?= adminUrl('categories/?parent_id=' . $pc['id']) ?>"
               style="display: flex; align-items: center; gap: 12px; padding: 14px 16px; text-decoration: none; color: inherit; border-left: 4px solid <?= $isActive ? '#0284c7' : 'transparent' ?>; background: <?= $isActive ? '#F0F9FF' : 'transparent' ?>; border-bottom: 1px solid var(--color-bg-elevated); transition: background 0.15s;">
              <?php if (!empty($pc['image'])): ?>
                <img src="<?= htmlspecialchars($pc['image']) ?>" alt="" style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
              <?php else: ?>
                <div style="width: 44px; height: 44px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; color: #64748b; flex-shrink: 0;">
                  <?= strtoupper(substr($pc['name'], 0, 1)) ?>
                </div>
              <?php endif; ?>

              <div style="flex: 1; min-width: 0;">
                <div style="font-weight: 700; font-size: 14px; color: <?= $isActive ? '#0369A1' : '#0f172a' ?>; margin-bottom: 3px;">
                  <?= esc($pc['name']) ?>
                </div>
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                  <?= deptBadge($pc['department']) ?>
                  <span style="font-size: 11px; color: var(--color-text-tertiary);">
                    <?= $childCount ?> sub · <?= (int)$pc['product_count'] ?> products
                  </span>
                </div>
              </div>

              <div style="display: flex; flex-direction: column; gap: 4px; flex-shrink: 0;">
                <button type="button" class="btn btn-secondary btn-sm" style="padding: 3px 10px; font-size: 11px;"
                        onclick="event.preventDefault(); event.stopPropagation(); editCategory(<?= htmlspecialchars(json_encode($pc), ENT_QUOTES) ?>)">
                  Edit
                </button>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div style="padding: 12px 16px; border-top: 1px solid var(--color-bg-elevated);">
        <button class="btn btn-primary btn-sm" style="width: 100%;" onclick="openAddForm('0')">+ Add Parent Category</button>
      </div>
    </div>

    <!-- RIGHT: Subcategories of selected parent -->
    <div class="admin-card" style="overflow: hidden;">
      <div style="padding: var(--space-4) var(--space-5); border-bottom: 1px solid var(--color-bg-elevated); display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
        <div>
          <h2 style="font-size: 15px; font-weight: 700; margin: 0;">
            Subcategories
            <?php if ($selectedParent): ?>
              <span style="color: #0284c7;">of <?= esc($selectedParent['name']) ?></span>
            <?php endif; ?>
          </h2>
          <?php if ($selectedParent): ?>
            <p style="font-size: 12px; color: var(--color-text-tertiary); margin: 3px 0 0;">
              <?= deptBadge($selectedParent['department']) ?>
              &nbsp; <?= (int)$selectedParent['product_count'] ?> products in parent
            </p>
          <?php endif; ?>
        </div>
        <div style="display: flex; gap: 8px;">
          <button class="btn btn-secondary btn-sm" onclick="openAddForm('<?= $selectedParentId ?>')">+ Add Subcategory</button>
          <?php if ($selectedParent): ?>
            <button class="btn btn-secondary btn-sm" onclick="editCategory(<?= htmlspecialchars(json_encode($selectedParent), ENT_QUOTES) ?>)">Edit Parent</button>
          <?php endif; ?>
        </div>
      </div>

      <?php if (!$selectedParent): ?>
        <div style="padding: 48px 24px; text-align: center; color: var(--color-text-tertiary); font-size: 14px;">
          No parent category selected. Create a parent on the left first.
        </div>
      <?php elseif (empty($subsForSelected)): ?>
        <div style="padding: 48px 24px; text-align: center; color: var(--color-text-tertiary); font-size: 14px;">
          No subcategories under <strong><?= esc($selectedParent['name']) ?></strong> yet.
          <div style="margin-top: 12px;">
            <button class="btn btn-primary btn-sm" onclick="openAddForm('<?= $selectedParentId ?>')">+ Add First Subcategory</button>
          </div>
        </div>
      <?php else: ?>
        <div class="table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Subcategory</th>
                <th>Slug</th>
                <th>Products</th>
                <th>Sort</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($subsForSelected as $sub): ?>
                <tr>
                  <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                      <?php if (!empty($sub['image'])): ?>
                        <img src="<?= htmlspecialchars($sub['image']) ?>" alt="" style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px;">
                      <?php else: ?>
                        <div style="width: 36px; height: 36px; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; color: #64748b;">
                          <?= strtoupper(substr($sub['name'], 0, 1)) ?>
                        </div>
                      <?php endif; ?>
                      <span style="font-weight: 600; font-size: 13px;"><?= esc($sub['name']) ?></span>
                    </div>
                  </td>
                  <td style="font-family: monospace; font-size: 12px; color: #475569;"><?= esc($sub['slug']) ?></td>
                  <td>
                    <span style="font-size: 12px; font-weight: 600; background: #f1f5f9; padding: 2px 8px; border-radius: 12px;">
                      <?= number_format($sub['product_count']) ?> items
                    </span>
                  </td>
                  <td><?= $sub['sort_order'] ?></td>
                  <td>
                    <span class="status-badge <?= $sub['is_active'] ? 'status-active' : 'status-inactive' ?>">
                      <?= $sub['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                      <button type="button" class="btn btn-secondary btn-sm" onclick='editCategory(<?= htmlspecialchars(json_encode($sub), ENT_QUOTES) ?>)'>
                        Edit
                      </button>
                      <form method="POST" action="<?= adminUrl('categories/delete.php?id=' . $sub['id']) ?>" style="display: inline;" onsubmit="return confirm('Delete subcategory \'<?= addslashes(esc($sub['name'])) ?>\'?')">
                        <?= getCSRFInput() ?>
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
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

function openAddForm(parentId) {
  document.getElementById('categoryForm').style.display = 'block';
  document.getElementById('formTitle').textContent = 'Add New Category / Subcategory';
  document.getElementById('submitBtn').textContent = 'Save Category';
  document.getElementById('editId').value = '';
  document.getElementById('catName').value = '';
  document.getElementById('catSlug').value = '';
  document.getElementById('catDept').value = 'women';
  document.getElementById('catParent').value = parentId !== undefined ? String(parentId) : '0';
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
