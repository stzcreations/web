<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/user_auth.php';
require_once __DIR__ . '/includes/user_auth.php';

if (!is_user_logged_in()) {
    redirect('login?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? ''));
}

$id = (int)($_GET['id'] ?? 0);
$slug = trim($_GET['slug'] ?? '');
if ($id <= 0 && $slug === '') { http_response_code(400); echo "Invalid request"; exit; }
if ($slug !== '') {
  $stmt = db()->prepare("SELECT p.*, c.name as category_name FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE p.slug=?");
  $stmt->execute([$slug]);
} else {
  $stmt = db()->prepare("SELECT p.*, c.name as category_name FROM projects p LEFT JOIN categories c ON p.category_id=c.id WHERE p.id=?");
  $stmt->execute([$id]);
}
$p = $stmt->fetch();
if (!$p) { http_response_code(404); echo "Project not found"; exit; }
$id = (int)$p['id'];

$hasFile = !empty($p['file_path']);
$countdown = (int)setting('download_countdown', '60');
if ($countdown < 1) $countdown = 60;
$adUrl = setting('download_ad_url', 'https://www.profitableratecpmnetwork.com/h7mm3034rf?key=71a92769225b798cc017c15982d72277');
$fileSize = $p['file_size'] ?: '—';
$pageTitle = $p['title'] . ' - Download';
$dlHref = base_url('download/' . $id . '?get=1');

// Stream file if requested
if (isset($_GET['get'])) {
    if (!$hasFile) {
        http_response_code(404);
        echo "File not available yet. Please check back later.";
        exit;
    }
    $base = realpath(STORAGE_DIR . '/projects');
    if (!$base) { $base = realpath(UPLOAD_DIR . '/projects'); }
    $filePath = realpath($base . '/' . basename($p['file_path']));
    if ($filePath === false || strpos($filePath, $base) !== 0 || !is_file($filePath)) {
        $base2 = realpath(UPLOAD_DIR . '/projects');
        $filePath = realpath($base2 . '/' . basename($p['file_path']));
        if ($filePath === false || strpos($filePath, $base2) !== 0 || !is_file($filePath)) {
            http_response_code(404); echo "File not found"; exit;
        }
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
    $userId = is_user_logged_in() ? (int)$_SESSION['user_id'] : null;
    $ins = db()->prepare("INSERT INTO downloads (project_id, user_id, ip_address, user_agent) VALUES (?,?,?,?)");
    $ins->execute([$id, $userId, $ip, $ua]);
    $upd = db()->prepare("UPDATE projects SET download_count = download_count + 1 WHERE id=?");
    $upd->execute([$id]);

    $filename = basename($p['file_path']);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($filePath) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filePath));
    header('X-Content-Type-Options: nosniff');
    readfile($filePath);
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>
<main class="container" style="max-width:760px;margin:30px auto;">
  <div class="adrow mb-3">
    <div class="adslot text-center"><?php echo render_ad(get_ads('header')); ?></div>
  </div>
  <section class="card-dark p-4">
    <div class="text-center mb-2"><span class="adtag" style="background:rgba(37,99,235,.1);color:#2563eb;padding:3px 10px;border-radius:999px;font-size:.8rem;">Sponsored</span></div>
    <h1 class="text-white mb-2" style="font-size:1.4rem;">Step 1: Preparing your download</h1>
    <p class="text-muted mb-3"><?php echo e($p['title']); ?> &middot; <?php echo e($p['file_type']); ?> &middot; <?php echo e($fileSize); ?></p>

    <div class="adrow mb-3">
      <div class="adslot text-center">
        <?php echo render_ad(get_ads("homepage")); ?>
      </div>
    </div>

    <div class="wbar mb-2" aria-hidden="true" style="height:8px;background:var(--card2);border-radius:999px;overflow:hidden;">
      <i id="cdbar" style="display:block;height:100%;width:0%;background:linear-gradient(90deg,#2563eb,#1d4ed8);transition:width .3s linear;"></i>
    </div>
    <p id="cdmsg" class="text-muted mb-3">Download unlocks in <b id="cdn"><?php echo (int)$countdown; ?></b> seconds</p>

    <a id="cdbtn" class="btn btn-glow wide dl-btn disabled" href="<?php echo e($dlHref); ?>" aria-disabled="true" style="pointer-events:none;opacity:.6;">
      <svg class="ic" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11m0 0l-4-4m4 4l4-4M5 20h14"/></svg>
      Download
    </a>
    <div id="notAvail" class="alert alert-warning d-none mt-2">File not available yet. Please check back later.</div>

    <div class="adrow mt-3 mb-3">
      <div class="adslot text-center"><?php echo render_ad(get_ads('homepage')); ?></div>
    </div>
    <div class="adrow mb-3">
      <div class="adslot text-center">
        <?php echo render_ad(get_ads("homepage")); ?>
      </div>
    </div>

    <a class="btn btn-outline-glow wide" href="<?php echo e($adUrl); ?>" target="_blank" rel="noopener sponsored nofollow">Visit sponsor</a>
    <p class="text-muted mt-3 mb-0"><a href="<?php echo base_url('project/' . $p['slug']); ?>">&larr; Back to project</a></p>
    <span id="cd" data-left="0" data-total="<?php echo (int)$countdown; ?>" hidden></span>
  </section>
  <div class="adrow mt-3">
    <div class="adslot text-center"><?php echo render_ad(get_ads('footer')); ?></div>
  </div>
</main>

<script>
(function(){
  var total = <?php echo (int)$countdown; ?>;
  var left = total;
  var bar = document.getElementById('cdbar');
  var cdn = document.getElementById('cdn');
  var cdbtn = document.getElementById('cdbtn');
  var hasFile = <?php echo $hasFile ? 'true' : 'false'; ?>;
  function tick(){
    left--;
    if (cdn) cdn.textContent = left;
    if (bar) bar.style.width = Math.round(((total-left)/total)*100) + '%';
    if (left <= 0) {
      clearInterval(timer);
      if (cdn) cdn.textContent = '0';
      if (bar) bar.style.width = '100%';
      var msg = document.getElementById('cdmsg');
      if (msg) msg.innerHTML = 'Your download is ready!';
      if (cdbtn) {
        cdbtn.classList.remove('disabled');
        cdbtn.style.pointerEvents = '';
        cdbtn.style.opacity = '';
        cdbtn.removeAttribute('aria-disabled');
      }
      // Do not auto-download; user must click the button
      if (!hasFile) {
        var na = document.getElementById('notAvail');
        if (na) na.classList.remove('d-none');
      }
    }
  }
  if (total <= 0) {
    if (cdn) cdn.textContent = '0';
    if (bar) bar.style.width = '100%';
    var msg0 = document.getElementById('cdmsg');
    if (msg0) msg0.innerHTML = 'Your download is ready!';
    if (cdbtn) {
      cdbtn.classList.remove('disabled');
      cdbtn.style.pointerEvents = '';
      cdbtn.style.opacity = '';
      cdbtn.removeAttribute('aria-disabled');
    }
    if (!hasFile) {
      var na0 = document.getElementById('notAvail');
      if (na0) na0.classList.remove('d-none');
    }
  } else {
    var timer = setInterval(tick, 1000);
  }
  // initial bar
  if (bar) bar.style.width = '0%';
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>