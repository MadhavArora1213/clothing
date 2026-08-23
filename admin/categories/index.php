<?php
require_once dirname(__DIR__) . '/config/database.php';
requireAdminAuth();

$categories = $mysqli->query('SELECT * FROM categories ORDER BY parent_id, sort_order, name')->fetch_all(MYSQLI_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $slug = sanitize($_POST['slug'] ?? '');
  $description = sanitize($_POST['description'] ?? '');
  $image = sanitize($_POST['image'] ?? '');
  $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
  $sort_order = (int)($_POST['sort_order'] ?? 0);
  $is_active = isset($_POST['is_active']) ? 1 : 0;

  if (empty($name) || empty($slug)) {
    $error = 'Name and slug are required.';
  } else {
    if (!empty($_POST['edit_id'])) {
      $stmt = $mysqli->prepare('UPDATE categories SET name=?, slug=?, description=?, image=?, parent_id=?, sort_order=?, is_active=? WHERE id=?');
      $stmt->bind_param('ssssiiii', $name, $slug, $description, $image, $parent_id, $sort_order, $is_active, $_POST['edit_id']);
      $stmt->execute();
    } else {
      $stmt = $mysqli->prepare('INSERT INTO categories (name, slug, description, image, parent_id, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('ssssiii', $name, $slug, $description, $image, $parent_id, $sort_order, $is_active);
      $stmt->execute();
    }
    redirect('/admin/categories/');
  }
}

$pageTitle = 'Categories — ATELIER Admin';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <h1>Categories</h1>
    <button class="btn btn-primary" onclick="document.getElementById('categoryForm').style.display='block';">+ Add Category</button>
  </div>

  <div class="admin-card" id="categoryForm" style="display: none; margin-bottom: var(--space-6);">
    <div class="admin-card-header">
      <h2 id="formTitle">Add Category</h2>
    </div>
    <form method="POST" action="">
      <input type="hidden" name="edit_id" id="editId">
      <div class="admin-form-page" style="box-shadow: none; padding: var(--space-6) 0;">
        <div class="form-grid">
          <div class="form-group">
            <label>Name <span class="required">*</span></label>
            <input type="text" name="name" id="catName" required>
          </div>
          <div class="form-group">
            <label>Slug <span class="required">*</span></label>
            <input type="text" name="slug" id="catSlug" required>
          </div>
          <div class="form-group full-width">
            <label>Description</label>
            <textarea name="description" id="catDesc" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Image URL</label>
            <input type="text" name="image" id="catImage">
          </div>
          <div class="form-group">
            <label>Parent Category</label>
            <select name="parent_id" id="catParent">
              <option value="0">None (Top Level)</option>
              <?php foreach ($categories as $cat): if ($cat['parent_id'] == 0): ?>
                <option value="<?= $cat['id'] ?>"><?= sanitize($cat['name']) ?></option>
              <?php endif; endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Sort Order</label>
            <input type="number" name="sort_order" id="catSort" value="0">
          </div>
          <div class="form-group">
            <label><input type="checkbox" name="is_active" id="catActive" checked> Active</label>
          </div>
        </div>
        <div class="form-actions" style="border: none; margin: 0; padding: 0;">
          <button type="button" class="btn btn-secondary" onclick="document.getElementById('categoryForm').style.display='none';">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Category</button>
        </div>
      </div>
    </form>
  </div>

  <div class="admin-card">
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Parent</th>
            <th>Sort</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($categories)): ?>
            <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--color-text-tertiary);">No categories yet.</td></tr>
          <?php else: ?>
            <?php foreach ($categories as $cat): ?>
              <tr>
                <td style="font-weight: 600;"><?= sanitize($cat['name']) ?></td>
                <td><?= sanitize($cat['slug']) ?></td>
                <td><?= $cat['parent_id'] ? 'Sub-category' : '—' ?></td>
                <td><?= $cat['sort_order'] ?></td>
                <td><span class="status-badge <?= $cat['is_active'] ? 'status-active' : 'status-inactive' ?>"><?= $cat['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <button class="btn btn-secondary btn-sm" onclick="editCategory(<?= $cat['id'] ?>, '<?= sanitize($cat['name']) ?>', '<?= sanitize($cat['slug']) ?>', '<?= sanitize($cat['description'] ?? '') ?>', '<?= sanitize($cat['image'] ?? '') ?>', <?= $cat['parent_id'] ?>, <?= $cat['sort_order'] ?>, <?= $cat['is_active'] ?>)">Edit</button>
                    <a href="/admin/categories/delete.php?id=<?= $cat['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this category?')">Delete</a>
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
function editCategory(id, name, slug, desc, image, parent, sort, active) {
  document.getElementById('categoryForm').style.display = 'block';
  document.getElementById('formTitle').textContent = 'Edit Category';
  document.getElementById('editId').value = id;
  document.getElementById('catName').value = name;
  document.getElementById('catSlug').value = slug;
  document.getElementById('catDesc').value = desc;
  document.getElementById('catImage').value = image;
  document.getElementById('catParent').value = parent;
  document.getElementById('catSort').value = sort;
  document.getElementById('catActive').checked = active == 1;
  document.getElementById('categoryForm').scrollIntoView({ behavior: 'smooth' });
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
