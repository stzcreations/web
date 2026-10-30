<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$ads = db()->query("SELECT * FROM advertisements ORDER BY id DESC")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="text-white mb-0">Advertisements</h2>
  <a href="<?php echo base_url('admin/advertisements/create'); ?>" class="btn btn-glow">Add Advertisement</a>
</div>
<div class="card-dark p-3">
<div class="table-responsive"><table class="table table-darkish">
  <thead><tr><th>Title</th><th>Position</th><th>Status</th><th>URL</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach ($ads as $a): ?>
  <tr>
    <td><?php echo e($a['title']); ?></td>
    <td><?php echo e($a['position']); ?></td>
    <td><?php echo e($a['status']); ?></td>
    <td class="small"><?php echo e($a['url']); ?></td>
    <td>
      <a href="<?php echo base_url('admin/advertisements/edit/' . $a['id']); ?>" class="btn btn-sm btn-outline-glow">Edit</a>
      <a href="<?php echo base_url('admin/advertisements/delete/' . $a['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
</div>
