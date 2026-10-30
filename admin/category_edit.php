<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM categories WHERE id=?");
$stmt->execute([$id]);
$cat = $stmt->fetch();
if (!$cat) { echo "Not found"; return; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'active';
    if ($name === '') $errors[] = 'Name required';
    if ($slug === '') $slug = slugify($name);
    if (empty($errors)) {
        try {
            $stmt = db()->prepare("UPDATE categories SET name=?,slug=?,description=?,status=? WHERE id=?");
            $stmt->execute([$name,$slug,$desc,$status,$id]);
            redirect('admin/categories');
        } catch (Exception $e) { $errors[] = 'Slug already exists'; }
    }
}
?>
<h2 class="text-white mb-3">Edit Category</h2>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach($errors as $er) echo e($er).'<br>'; ?></div><?php endif; ?>
<form method="post" class="card-dark p-4" style="max-width:600px;">
  <?php echo csrf_field(); ?>
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" required value="<?php echo e($_POST['name'] ?? $cat['name']); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Slug</label>
    <input type="text" name="slug" class="form-control" value="<?php echo e($_POST['slug'] ?? $cat['slug']); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="3"><?php echo e($_POST['description'] ?? $cat['description']); ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select">
      <option value="active" <?php if($cat['status']==='active') echo 'selected'; ?>>Active</option>
      <option value="inactive" <?php if($cat['status']==='inactive') echo 'selected'; ?>>Inactive</option>
    </select>
  </div>
  <button class="btn btn-glow" type="submit">Update</button>
  <a href="<?php echo base_url('admin/categories'); ?>" class="btn btn-outline-glow">Cancel</a>
</form>
