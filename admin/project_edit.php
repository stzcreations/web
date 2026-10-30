<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM projects WHERE id=?");
$stmt->execute([$id]);
$project = $stmt->fetch();
if (!$project) { echo "Project not found"; return; }
$cats = db()->query("SELECT * FROM categories WHERE status='active' ORDER BY name")->fetchAll();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title'] ?? '');
    $short = trim($_POST['short_description'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $version = trim($_POST['version'] ?? '1.0');
    $fileType = trim($_POST['file_type'] ?? 'ZIP');
    $categoryId = (int)($_POST['category_id'] ?? 0) ?: null;
    $status = $_POST['status'] ?? 'draft';
    $featured = isset($_POST['featured']) ? 1 : 0;
    $seoTitle = trim($_POST['seo_title'] ?? '');
    $metaDesc = trim($_POST['meta_description'] ?? '');
    if ($title === '') $errors[] = 'Title required';
    if (empty($errors)) {
        $slug = $project['slug'];
        if ($title !== $project['title']) {
            $slug = slugify($title);
            $base = $slug; $i=1;
            while (true) {
                $st = db()->prepare("SELECT COUNT(*) FROM projects WHERE slug=? AND id<>?");
                $st->execute([$slug, $id]);
                if ($st->fetchColumn() == 0) break;
                $slug = $base . '-' . $i++;
            }
        }
        $thumbName = $project['thumbnail'];
        if (!empty($_FILES['thumbnail']['name'])) {
            [$ok, $res] = upload_file($_FILES['thumbnail'], UPLOAD_DIR . '/thumbnails', ['jpg','jpeg','png','gif','webp'], ['image/jpeg','image/png','image/gif','image/webp']);
            if (!$ok) $errors[] = 'Thumbnail: ' . $res;
            else $thumbName = $res;
        }
        $fileName = $project['file_path'];
        $fileSize = $project['file_size'];
        if (!empty($_FILES['project_file']['name'])) {
            [$ok, $res] = upload_file($_FILES['project_file'], STORAGE_DIR . '/projects', ['zip','php','html','rar','7z','tar','gz'], []);
            if (!$ok) $errors[] = 'Project file: ' . $res;
            else { $fileName = $res; $fileSize = format_size($_FILES['project_file']['size']); }
        }
        if (empty($errors)) {
            $stmt = db()->prepare("UPDATE projects SET category_id=?,title=?,slug=?,short_description=?,description=?,features=?,version=?,file_type=?,file_size=?,file_path=?,thumbnail=?,seo_title=?,meta_description=?,status=?,featured=? WHERE id=?");
            $stmt->execute([$categoryId,$title,$slug,$short,$desc,$features,$version,$fileType,$fileSize,$fileName,$thumbName,$seoTitle,$metaDesc,$status,$featured,$id]);
            // new screenshots
            if (!empty($_FILES['screenshots']['name'][0])) {
                $files = $_FILES['screenshots'];
                $count = count($files['name']);
                for ($i=0; $i<$count; $i++) {
                    if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
                    $f = ['name'=>$files['name'][$i],'type'=>$files['type'][$i],'tmp_name'=>$files['tmp_name'][$i],'error'=>$files['error'][$i],'size'=>$files['size'][$i]];
                    [$ok, $res] = upload_file($f, UPLOAD_DIR . '/screenshots', ['jpg','jpeg','png','gif','webp'], ['image/jpeg','image/png','image/gif','image/webp']);
                    if ($ok) {
                        $ins = db()->prepare("INSERT INTO project_screenshots (project_id, image_path, sort_order) VALUES (?,?,?)");
                        $ins->execute([$id, $res, $i]);
                    }
                }
            }
            redirect('admin/projects');
        }
    }
}
// screenshots
$ssStmt = db()->prepare("SELECT * FROM project_screenshots WHERE project_id=?");
$ssStmt->execute([$id]);
$screenshots = $ssStmt->fetchAll();
?>
<h2 class="text-white mb-3">Edit Project</h2>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach($errors as $er) echo e($er).'<br>'; ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="card-dark p-4">
  <?php echo csrf_field(); ?>
  <div class="row">
    <div class="col-md-8 mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" required value="<?php echo e($_POST['title'] ?? $project['title']); ?>">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Category</label>
      <select name="category_id" class="form-select">
        <option value="">-- None --</option>
        <?php foreach ($cats as $c): ?>
        <option value="<?php echo (int)$c['id']; ?>" <?php if (($project['category_id'])==$c['id']) echo 'selected'; ?>><?php echo e($c['name']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Short Description</label>
    <input type="text" name="short_description" class="form-control" value="<?php echo e($_POST['short_description'] ?? $project['short_description']); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="5"><?php echo e($_POST['description'] ?? $project['description']); ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Features</label>
    <textarea name="features" class="form-control" rows="4"><?php echo e($_POST['features'] ?? $project['features']); ?></textarea>
  </div>
  <div class="row">
    <div class="col-md-3 mb-3">
      <label class="form-label">Version</label>
      <input type="text" name="version" class="form-control" value="<?php echo e($_POST['version'] ?? $project['version']); ?>">
    </div>
    <div class="col-md-3 mb-3">
      <label class="form-label">File Type</label>
      <input type="text" name="file_type" class="form-control" value="<?php echo e($_POST['file_type'] ?? $project['file_type']); ?>">
    </div>
    <div class="col-md-3 mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="draft" <?php if($project['status']==='draft') echo 'selected'; ?>>Draft</option>
        <option value="published" <?php if($project['status']==='published') echo 'selected'; ?>>Published</option>
      </select>
    </div>
    <div class="col-md-3 mb-3 d-flex align-items-end">
      <div class="form-check">
        <input type="checkbox" name="featured" class="form-check-input" id="feat" <?php if($project['featured']) echo 'checked'; ?>>
        <label class="form-check-label" for="feat">Featured</label>
      </div>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Thumbnail (leave empty to keep)</label>
    <input type="file" name="thumbnail" class="form-control" accept="image/*">
    <?php if ($project['thumbnail']): ?><img src="<?php echo base_url('uploads/thumbnails/'.$project['thumbnail']); ?>" style="max-height:80px;margin-top:8px;"> <?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label">Screenshots (add more)</label>
    <input type="file" name="screenshots[]" class="form-control" multiple accept="image/*">
    <div class="mt-2 d-flex gap-2 flex-wrap">
      <?php foreach ($screenshots as $s): ?>
      <img src="<?php echo base_url('uploads/screenshots/'.$s['image_path']); ?>" style="max-height:60px;">
      <?php endforeach; ?>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Project File (leave empty to keep)</label>
    <input type="file" name="project_file" class="form-control">
    <?php if ($project['file_path']): ?><p class="text-muted small mt-1">Current: <?php echo e($project['file_path']); ?></p><?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label">SEO Title</label>
    <input type="text" name="seo_title" class="form-control" value="<?php echo e($_POST['seo_title'] ?? $project['seo_title']); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Meta Description</label>
    <textarea name="meta_description" class="form-control" rows="2"><?php echo e($_POST['meta_description'] ?? $project['meta_description']); ?></textarea>
  </div>
  <button class="btn btn-glow" type="submit">Update Project</button>
  <a href="<?php echo base_url('admin/projects'); ?>" class="btn btn-outline-glow">Cancel</a>
</form>
<?php
// delete screenshot handling not included
?>
