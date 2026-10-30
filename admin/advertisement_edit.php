<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM advertisements WHERE id=?");
$stmt->execute([$id]);
$ad = $stmt->fetch();
if (!$ad) { echo "Not found"; return; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $position = $_POST['position'] ?? $ad['position'];
    $status = $_POST['status'] ?? $ad['status'];
    if ($title === '') $errors[] = 'Title required';
    $imgName = $ad['image_path'];
    if (!empty($_FILES['image']['name'])) {
        [$ok, $res] = upload_file($_FILES['image'], UPLOAD_DIR . '/ads', ['jpg','jpeg','png','gif','webp'], ['image/jpeg','image/png','image/gif','image/webp']);
        if (!$ok) $errors[] = 'Image: ' . $res;
        else $imgName = $res;
    }
    if (empty($errors)) {
        $stmt = db()->prepare("UPDATE advertisements SET title=?,image_path=?,url=?,code=?,position=?,status=? WHERE id=?");
        $stmt->execute([$title,$imgName,$url,$code,$position,$status,$id]);
        redirect('admin/advertisements');
    }
}
?>
<h2 class="text-white mb-3">Edit Advertisement</h2>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach($errors as $er) echo e($er).'<br>'; ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="card-dark p-4" style="max-width:700px;">
  <?php echo csrf_field(); ?>
  <div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" required value="<?php echo e($_POST['title'] ?? $ad['title']); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">URL</label>
    <input type="url" name="url" class="form-control" value="<?php echo e($_POST['url'] ?? $ad['url']); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Ad Code (Adsterra/Monetag script, optional)</label>
    <textarea name="code" class="form-control" rows="4"><?php echo e($_POST['code'] ?? $ad['code']); ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Image</label>
    <input type="file" name="image" class="form-control" accept="image/*">
    <?php if ($ad['image_path']): ?><img src="<?php echo base_url('uploads/ads/'.$ad['image_path']); ?>" style="max-height:80px;margin-top:8px;"> <?php endif; ?>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Position</label>
      <select name="position" class="form-select">
        <?php foreach (['header','homepage','sidebar','footer','socialbar'] as $pos): ?>
        <option value="<?php echo $pos; ?>" <?php if($ad['position']===$pos) echo 'selected'; ?>><?php echo ucfirst($pos); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="enabled" <?php if($ad['status']==='enabled') echo 'selected'; ?>>Enabled</option>
        <option value="disabled" <?php if($ad['status']==='disabled') echo 'selected'; ?>>Disabled</option>
      </select>
    </div>
  </div>
  <button class="btn btn-glow" type="submit">Update</button>
  <a href="<?php echo base_url('admin/advertisements'); ?>" class="btn btn-outline-glow">Cancel</a>
</form>
