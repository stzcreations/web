<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$q = trim($_GET['q'] ?? '');
$pageTitle = 'Search: ' . $q;
$projects = [];
if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = db()->prepare("SELECT p.*, c.name as category_name FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE p.status='published' AND (p.title LIKE ? OR p.short_description LIKE ? OR p.description LIKE ?) ORDER BY p.created_at DESC");
    $stmt->execute([$like, $like, $like]);
    $projects = $stmt->fetchAll();
}
require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
  <h2 class="section-title mt-4">Search Results</h2>
  <p class="text-muted">Query: "<?php echo e($q); ?>" — <?php echo count($projects); ?> results</p>
  <div class="row">
    <?php foreach ($projects as $p): include __DIR__ . '/includes/project_card.php'; endforeach; ?>
    <?php if (empty($projects)): ?><p class="text-muted">No projects found.</p><?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
