<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $position = $_POST['position'] ?? 'homepage';
    $status = $_POST['status'] ?? 'enabled';
    if ($title === '') $errors[] = 'Title required';
    $imgName = null;
    if (!empty($_FILES['image']['name'])) {
        [$ok, $res] = upload_file($_FILES['image'], UPLOAD_DIR . '/ads', ['jpg','jpeg','png','gif','webp'], ['image/jpeg','image/png','image/gif','image/webp']);
        if (!$ok) $errors[] = 'Image: ' . $res;
        else $imgName = $res;
    }
    if (empty($errors)) {
        $stmt = db()->prepare("INSERT INTO advertisements (title,image_path,url,code,position,status) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$title,$imgName,$url,$code,$position,$status]);
        redirect('admin/advertisements');
    }
}
?>
<h2 class="text-white mb-3">Add Advertisement</h2>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach($errors as $er) echo e($er).'<br>'; ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="card-dark p-4" style="max-width:700px;">
  <?php echo csrf_field(); ?>
  <div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" required value="<?php echo e($_POST['title'] ?? ''); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">URL</label>
    <input type="url" name="url" class="form-control" value="<?php echo e($_POST['url'] ?? ''); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Ad Code (Adsterra/Monetag script, optional - overrides image)</label>
    <textarea name="code" class="form-control" rows="4" placeholder="Paste ad script here"><?php echo e($_POST['code'] ?? ''); ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Image</label>
    <input type="file" name="image" class="form-control" accept="image/*">
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Position</label>
      <select name="position" class="form-select">
        <option value="header">Header</option>
        <option value="homepage">Homepage</option>
        <option value="sidebar">Sidebar</option>
        <option value="footer">Footer</option>
        <option value="socialbar">Social Bar</option>
      </select>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="enabled">Enabled</option>
        <option value="disabled">Disabled</option>
      </select>
    </div>
  </div>
  <button class="btn btn-glow" type="submit">Save</button>
  <a href="<?php echo base_url('admin/advertisements'); ?>" class="btn btn-outline-glow">Cancel</a>
</form>
