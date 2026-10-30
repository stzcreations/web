<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare("SELECT * FROM categories WHERE slug=? AND status='active'");
$stmt->execute([$slug]);
$cat = $stmt->fetch();
if (!$cat) { http_response_code(404); echo "Category not found"; require __DIR__.'/includes/footer.php'; exit; }
$pageTitle = $cat['name'];
$projects = db()->prepare("SELECT p.*, c.name as category_name FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE p.status='published' AND p.category_id=? ORDER BY p.created_at DESC");
$projects->execute([$cat['id']]);
$projects = $projects->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
  <h2 class="section-title mt-4"><?php echo e($cat['name']); ?></h2>
  <p class="text-muted"><?php echo e($cat['description']); ?></p>
  <div class="row">
    <?php foreach ($projects as $p): include __DIR__ . '/includes/project_card.php'; endforeach; ?>
    <?php if (empty($projects)): ?><p class="text-muted">No projects in this category.</p><?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
