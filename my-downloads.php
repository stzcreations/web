<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/user_auth.php';
require_user();

$pageTitle = 'My Downloads';
$userId = (int)$_SESSION['user_id'];
$stmt = db()->prepare("SELECT d.*, p.title, p.slug, p.thumbnail, p.file_type, p.version FROM downloads d LEFT JOIN projects p ON d.project_id=p.id WHERE d.user_id=? ORDER BY d.downloaded_at DESC");
$stmt->execute([$userId]);
$downloads = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<div class="container mt-4">
  <h2 class="section-title">My <span>Downloads</span></h2>
  <?php if (empty($downloads)): ?>
    <p class="text-muted">You haven't downloaded anything yet.</p>
  <?php else: ?>
  <div class="card-dark p-3 mb-5">
    <div class="table-responsive">
      <table class="table table-darkish">
        <thead><tr><th>Project</th><th>Type</th><th>Version</th><th>Downloaded At</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($downloads as $d): ?>
        <tr>
          <td><?php echo e($d['title']); ?></td>
          <td><?php echo e($d['file_type']); ?></td>
          <td>v<?php echo e($d['version']); ?></td>
          <td><?php echo e(date('M d, Y H:i', strtotime($d['downloaded_at']))); ?></td>
          <td><a href="<?php echo base_url('project/' . $d['slug']); ?>" class="btn btn-sm btn-outline-glow">View</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
