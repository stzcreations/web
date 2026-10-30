<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$totalProjects = (int)db()->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$totalCats = (int)db()->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalDownloads = (int)db()->query("SELECT COALESCE(SUM(download_count),0) FROM projects")->fetchColumn();
$totalVisitors = (int)db()->query("SELECT COUNT(*) FROM visitors")->fetchColumn();
$totalUsers = (int)db()->query("SELECT COUNT(*) FROM users")->fetchColumn();
$activeUsers = (int)db()->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetchColumn();
$bannedUsers = (int)db()->query("SELECT COUNT(*) FROM users WHERE status='suspended'")->fetchColumn();
$recentProjects = db()->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5")->fetchAll();
$popularProjects = db()->query("SELECT * FROM projects ORDER BY download_count DESC LIMIT 5")->fetchAll();
?>
<h2 class="text-white mb-3">Dashboard</h2>
<div class="row mb-4">
  <div class="col-md-3 mb-2"><div class="stat-box"><div class="num"><?php echo $totalProjects; ?></div><div>Total Projects</div></div></div>
  <div class="col-md-3 mb-2"><div class="stat-box"><div class="num"><?php echo $totalCats; ?></div><div>Total Categories</div></div></div>
  <div class="col-md-3 mb-2"><div class="stat-box"><div class="num"><?php echo $totalDownloads; ?></div><div>Total Downloads</div></div></div>
  <div class="col-md-3 mb-2"><div class="stat-box"><div class="num"><?php echo $totalVisitors; ?></div><div>Total Visitors</div></div></div>
  <div class="col-md-3 mb-2"><div class="stat-box"><div class="num"><?php echo $totalUsers; ?></div><div>Total Users</div></div></div>
  <div class="col-md-3 mb-2"><div class="stat-box"><div class="num"><?php echo $activeUsers; ?></div><div>Active Users</div></div></div>
  <div class="col-md-3 mb-2"><div class="stat-box"><div class="num"><?php echo $bannedUsers; ?></div><div>Banned Users</div></div></div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="card-dark p-3">
      <h5 class="text-white">Recent Projects</h5>
      <div class="table-responsive"><table class="table table-darkish table-sm">
        <thead><tr><th>Title</th><th>Status</th><th>Downloads</th></tr></thead>
        <tbody>
        <?php foreach ($recentProjects as $p): ?>
        <tr><td><?php echo e($p['title']); ?></td><td><?php echo e($p['status']); ?></td><td><?php echo (int)$p['download_count']; ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card-dark p-3">
      <h5 class="text-white">Most Popular</h5>
      <div class="table-responsive"><table class="table table-darkish table-sm">
        <thead><tr><th>Title</th><th>Downloads</th></tr></thead>
        <tbody>
        <?php foreach ($popularProjects as $p): ?>
        <tr><td><?php echo e($p['title']); ?></td><td><?php echo (int)$p['download_count']; ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    </div>
  </div>
</div>
