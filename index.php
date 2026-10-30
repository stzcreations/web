<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$pageTitle = setting('seo_title', 'STZ Creations');
// Latest projects
$latest = db()->query("SELECT p.*, c.name as category_name FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE p.status='published' ORDER BY p.created_at DESC LIMIT 6")->fetchAll();
// Popular
$popular = db()->query("SELECT p.*, c.name as category_name FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE p.status='published' ORDER BY p.download_count DESC LIMIT 6")->fetchAll();
// Recently updated
$updated = db()->query("SELECT p.*, c.name as category_name FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE p.status='published' ORDER BY p.updated_at DESC LIMIT 6")->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<!-- Hero -->
<section class="hero">
  <div class="container text-center">
    <h1>Ready-to-use web projects, free to download</h1>
    <p class="mt-3"><?php echo e(setting('site_description')); ?></p>
    <form class="search-box d-flex mx-auto mt-4" style="max-width:640px;" action="<?php echo base_url('search'); ?>" method="get">
      <input class="form-control" type="search" name="q" placeholder="Search projects by name or description...">
      <button class="btn btn-glow ms-2" type="submit">Search</button>
    </form>
    <div class="mt-3">
      <a href="<?php echo base_url('browse'); ?>" class="btn btn-glow">Browse All Projects</a>
    </div>
  </div>
</section>

<div class="container">
  <?php echo render_ad(get_ads('homepage')); ?>

  <h2 class="section-title mt-4">Latest <span>Projects</span></h2>
  <div class="row">
    <?php foreach ($latest as $p): include __DIR__ . '/includes/project_card.php'; endforeach; ?>
    <?php if (empty($latest)): ?><p class="text-muted">No projects yet.</p><?php endif; ?>
  </div>

  <h2 class="section-title mt-4">Popular <span>Downloads</span></h2>
  <div class="row">
    <?php foreach ($popular as $p): include __DIR__ . '/includes/project_card.php'; endforeach; ?>
  </div>

  <h2 class="section-title mt-4">Recently <span>Updated</span></h2>
  <div class="row">
    <?php foreach ($updated as $p): include __DIR__ . '/includes/project_card.php'; endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
