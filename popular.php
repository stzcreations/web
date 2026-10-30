<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Popular Projects';
$projects = db()->query("SELECT p.*, c.name as category_name FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE p.status='published' ORDER BY p.download_count DESC LIMIT 30")->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
  <h2 class="section-title mt-4">Popular <span>Downloads</span></h2>
  <div class="row">
    <?php foreach ($projects as $p): include __DIR__ . '/includes/project_card.php'; endforeach; ?>
    <?php if (empty($projects)): ?><p class="text-muted">No projects yet.</p><?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
