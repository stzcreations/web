<?php
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc><?php echo e(SITE_URL); ?>/</loc></url>
  <url><loc><?php echo e(SITE_URL); ?>/browse</loc></url>
  <url><loc><?php echo e(SITE_URL); ?>/categories</loc></url>
  <?php
  $projects = db()->query("SELECT slug, updated_at FROM projects WHERE status='published'")->fetchAll();
  foreach ($projects as $p): ?>
  <url><loc><?php echo e(SITE_URL); ?>/project/<?php echo e($p['slug']); ?></loc><lastmod><?php echo date('Y-m-d', strtotime($p['updated_at'])); ?></lastmod></url>
  <?php endforeach; ?>
  <?php
  $cats = db()->query("SELECT slug FROM categories WHERE status='active'")->fetchAll();
  foreach ($cats as $c): ?>
  <url><loc><?php echo e(SITE_URL); ?>/category/<?php echo e($c['slug']); ?></loc></url>
  <?php endforeach; ?>
</urlset>
