<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Categories';
$cats = db()->query("SELECT c.*, (SELECT COUNT(*) FROM projects p WHERE p.category_id=c.id AND p.status='published') as cnt FROM categories c WHERE c.status='active' ORDER BY c.name")->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
  <h2 class="section-title mt-4">Categories</h2>
  <div class="row">
    <?php foreach ($cats as $c): ?>
    <div class="col-md-3 col-sm-6 mb-4">
      <a href="<?php echo base_url('category/' . $c['slug']); ?>" class="card-dark d-block p-4 text-center h-100">
        <i data-lucide="folder-open" style="width:32px;height:32px;color:#2563eb;"></i>
        <h5 class="text-white mt-2"><?php echo e($c['name']); ?></h5>
        <p class="text-muted small mb-0"><?php echo (int)$c['cnt']; ?> projects</p>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
