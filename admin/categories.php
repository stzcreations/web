<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$cats = db()->query("SELECT c.*, (SELECT COUNT(*) FROM projects p WHERE p.category_id=c.id) as cnt FROM categories c ORDER BY c.name")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="text-white mb-0">Categories</h2>
  <a href="<?php echo base_url('admin/categories/create'); ?>" class="btn btn-glow">Add Category</a>
</div>
<div class="card-dark p-3">
<div class="table-responsive"><table class="table table-darkish">
  <thead><tr><th>Name</th><th>Slug</th><th>Status</th><th>Projects</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach ($cats as $c): ?>
  <tr>
    <td><?php echo e($c['name']); ?></td>
    <td><?php echo e($c['slug']); ?></td>
    <td><?php echo e($c['status']); ?></td>
    <td><?php echo (int)$c['cnt']; ?></td>
    <td>
      <a href="<?php echo base_url('admin/categories/edit/' . $c['id']); ?>" class="btn btn-sm btn-outline-glow">Edit</a>
      <a href="<?php echo base_url('admin/categories/delete/' . $c['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
</div>
