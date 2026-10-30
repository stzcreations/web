<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE p.slug=?");
$stmt->execute([$slug]);
$p = $stmt->fetch();
if (!$p) { http_response_code(404); echo "Project not found"; require __DIR__.'/includes/footer.php'; exit; }
$pageTitle = $p['seo_title'] ?: $p['title'];
$pageDesc = $p['meta_description'] ?: $p['short_description'];
$ogImage = $p['thumbnail'] ? base_url('uploads/thumbnails/' . $p['thumbnail']) : '';

$ssStmt = db()->prepare("SELECT * FROM project_screenshots WHERE project_id=? ORDER BY sort_order, id");
$ssStmt->execute([$p['id']]);
$screenshots = $ssStmt->fetchAll();

$relStmt = db()->prepare("SELECT p.*, c.name as category_name FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE p.status='published' AND p.id<>? AND (p.category_id=? ) ORDER BY p.created_at DESC LIMIT 4");
$relStmt->execute([$p['id'], $p['category_id']]);
$related = $relStmt->fetchAll();

$hasFile = !empty($p['file_path']);
$countdown = (int)setting('download_countdown', '10');
$adUrl = setting('download_ad_url', 'https://www.profitableratecpmnetwork.com/h7mm3034rf?key=71a92769225b798cc017c15982d72277');
$go = isset($_GET['go']);

require_once __DIR__ . '/includes/header.php';

if ($go) {
  // Download interstitial on same page
  ?>
  <div class="container mt-5" style="max-width:720px;">
    <div class="card-dark p-4 text-center">
      <h2 class="text-white mb-2"><?php echo e($p['title']); ?></h2>
      <p class="text-muted mb-4">Your download will start after the countdown.</p>
      <div class="mb-3">
        <div style="font-size:3rem;font-weight:700;background:linear-gradient(90deg,#2563eb,#1d4ed8);-webkit-background-clip:text;background-clip:text;color:transparent;" id="countdown"><?php echo (int)$countdown; ?></div>
        <small class="text-muted">seconds remaining</small>
      </div>
      <a href="<?php echo e($adUrl); ?>" target="_blank" rel="noopener" class="btn btn-outline-glow mb-3">Open Sponsored Ad</a>
      <?php if ($hasFile): ?>
      <div class="mb-3">
        <a id="downloadBtn" href="<?php echo base_url('download/' . $p['id'] . '?get=1'); ?>" class="btn btn-glow w-100 disabled" aria-disabled="true" style="pointer-events:none;opacity:.6;">Download Now</a>
      </div>
      <?php else: ?>
      <div id="notAvail" class="alert alert-warning d-none">File not available yet. Please check back later.</div>
      <?php endif; ?>
      <a href="<?php echo base_url('project/' . $p['slug']); ?>" class="btn btn-outline-glow mt-2">Back to Project</a>
    </div>
  </div>
  <script>
  (function(){
    var hasFile = <?php echo $hasFile ? 'true' : 'false'; ?>;
    var seconds = <?php echo (int)$countdown; ?>;
    var cd = document.getElementById('countdown');
    var btn = document.getElementById('downloadBtn');
    var notAvail = document.getElementById('notAvail');
    if (seconds <= 0) {
      if (hasFile && btn) { btn.classList.remove('disabled'); btn.style.pointerEvents=''; btn.style.opacity=''; btn.removeAttribute('aria-disabled'); window.location.href = btn.href; }
      else if (notAvail) notAvail.classList.remove('d-none');
      return;
    }
    var timer = setInterval(function(){
      seconds--;
      if (cd) cd.textContent = seconds;
      if (seconds <= 0) {
        clearInterval(timer);
        if (hasFile && btn) { btn.classList.remove('disabled'); btn.style.pointerEvents=''; btn.style.opacity=''; btn.removeAttribute('aria-disabled'); window.location.href = btn.href; }
        else if (notAvail) notAvail.classList.remove('d-none');
      }
    }, 1000);
  })();
  </script>
  <?php
  require_once __DIR__ . '/includes/footer.php';
  exit;
}
?>
<div class="container mt-4">
  <div class="row">
    <div class="col-lg-8">
      <h1 style="color:#000;font-family:'DM Sans','Nirmala UI','Vrinda','Kalpurush',sans-serif;"><?php echo e($p['title']); ?></h1>
      <p class="text-muted"><?php echo e($p['short_description']); ?></p>
      <img class="main mb-3 img-fluid" src="<?php echo e($p['thumbnail'] ? base_url('uploads/thumbnails/'.$p['thumbnail']) : base_url('assets/images/placeholder.svg')); ?>" alt="<?php echo e($p['title']); ?>">
      <?php if ($screenshots): ?>
      <div class="row gallery mb-3">
        <?php foreach ($screenshots as $s): ?>
        <div class="col-4 mb-2"><img src="<?php echo base_url('uploads/screenshots/'.$s['image_path']); ?>" class="img-fluid" alt=""></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="card-dark p-4 mb-3">
        <h5 class="text-white">Description</h5>
        <p><?php echo nl2br(e($p['description'])); ?></p>
        <?php if ($p['features']): ?>
        <h5 class="text-white mt-3">Features</h5>
        <p><?php echo nl2br(e($p['features'])); ?></p>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card-dark p-4 mb-3">
        <ul class="list-unstyled mb-0">
          <li class="mb-2"><strong>Category:</strong> <?php echo e($p['category_name']); ?></li>
          <li class="mb-2"><strong>Version:</strong> <?php echo e($p['version']); ?></li>
          <li class="mb-2"><strong>File Type:</strong> <?php echo e($p['file_type']); ?></li>
          <li class="mb-2"><strong>File Size:</strong> <?php echo e($p['file_size']); ?></li>
          <li class="mb-2"><strong>Uploaded:</strong> <?php echo e(date('M d, Y', strtotime($p['created_at']))); ?></li>
          <li class="mb-2"><strong>Updated:</strong> <?php echo e(date('M d, Y', strtotime($p['updated_at']))); ?></li>
          <li class="mb-3"><strong>Downloads:</strong> <?php echo (int)$p['download_count']; ?></li>
        </ul>
        <?php $dlAd = setting('download_ad_url', $adUrl); ?>
        <a class="btn wide dl-btn" href="<?php echo base_url('go=' . $p['slug']); ?>" data-ad="<?php echo e($dlAd); ?>">
          <svg class="ic" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11m0 0l-4-4m4 4l4-4M5 20h14"></path></svg>
          Download
        </a>
      </div>
      <?php echo render_ad(get_ads('sidebar')); ?>
    </div>
  </div>

  <?php if ($related): ?>
  <h3 class="section-title mt-4">Related <span>Projects</span></h3>
  <div class="row">
    <?php foreach ($related as $p): include __DIR__ . '/includes/project_card.php'; endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>