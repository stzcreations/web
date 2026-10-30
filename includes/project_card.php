<?php
// expects $p (project row) possibly with category_name
$catName = $p['category_name'] ?? 'Uncategorized';
?>
<div class="col-md-4 col-sm-6 mb-4">
  <div class="cb">
    <?php $thumb = $p['thumbnail'] ? base_url('uploads/thumbnails/' . $p['thumbnail']) : base_url('assets/images/placeholder.svg'); ?>
    <img src="<?php echo e($thumb); ?>" alt="<?php echo e($p['title']); ?>" class="cb-thumb mb-2" loading="lazy">
    <div class="meta">
      <span><?php echo e($catName); ?></span>
      <span class="ft"><?php echo e($p['file_type']); ?></span>
    </div>
    <h3><a href="<?php echo base_url('project/' . $p['slug']); ?>"><?php echo e($p['title']); ?></a></h3>
    <p><?php echo e(mb_substr($p['short_description'] ?? '', 0, 120)); ?></p>
    <div class="meta sm">
      <span>v<?php echo e($p['version']); ?></span>
      <span>Updated <?php echo e(date('M d, Y', strtotime($p['updated_at']))); ?></span>
      <span><svg class="ic" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11m0 0l-4-4m4 4l4-4M5 20h14"></path></svg><?php echo (int)$p['download_count']; ?></span>
    </div>
    <a class="btn ghost" href="<?php echo base_url('project/' . $p['slug']); ?>">View details</a>
  </div>
</div>
