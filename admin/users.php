<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
// Toggle status
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $uid = (int)$_GET['id'];
    $newStatus = $_GET['toggle'] === 'ban' ? 'suspended' : 'active';
    $stmt = db()->prepare("UPDATE users SET status=? WHERE id=?");
    $stmt->execute([$newStatus, $uid]);
    redirect('admin/users');
}
$users = db()->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="text-white mb-0">Users</h2>
</div>
<div class="card-dark p-3">
  <div class="table-responsive">
    <table class="table table-darkish">
      <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Joined</th><th>Last Login</th><th>Action</th></tr></thead>
      <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td><?php echo (int)$u['id']; ?></td>
        <td><?php echo e($u['name']); ?></td>
        <td><?php echo e($u['email']); ?></td>
        <td><?php echo e($u['status']); ?></td>
        <td><?php echo e(date('M d, Y', strtotime($u['created_at']))); ?></td>
        <td><?php echo $u['last_login_at'] ? e(date('M d, Y H:i', strtotime($u['last_login_at']))) : '-'; ?></td>
        <td>
          <?php if ($u['status'] === 'suspended'): ?>
            <a href="<?php echo base_url('admin/users?toggle=activate&id=' . $u['id']); ?>" class="btn btn-sm btn-success">Activate</a>
          <?php else: ?>
            <a href="<?php echo base_url('admin/users?toggle=ban&id=' . $u['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Ban this user?')">Ban</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
