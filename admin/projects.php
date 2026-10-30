<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$projects = db()->query("SELECT p.*, c.name as category_name FROM projects p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.created_at DESC")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="text-white mb-0">Projects</h2>
  <a href="<?php echo base_url('admin/projects/create'); ?>" class="btn btn-glow">Add Project</a>
</div>
<div class="card-dark p-3">
<div class="table-responsive"><table class="table table-darkish">
  <thead><tr><th>Title</th><th>Category</th><th>Type</th><th>Status</th><th>Featured</th><th>Downloads</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach ($projects as $p): ?>
  <tr>
    <td><?php echo e($p['title']); ?></td>
    <td><?php echo e($p['category_name']); ?></td>
    <td><?php echo e($p['file_type']); ?></td>
    <td><?php echo e($p['status']); ?></td>
    <td><?php echo $p['featured'] ? 'Yes' : 'No'; ?></td>
    <td><?php echo (int)$p['download_count']; ?></td>
    <td>
      <a href="<?php echo base_url('admin/projects/edit/' . $p['id']); ?>" class="btn btn-sm btn-outline-glow">Edit</a>
      <a href="<?php echo base_url('admin/projects/delete/' . $p['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
</div>
