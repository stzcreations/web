<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$id = (int)($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $stmt = db()->prepare("DELETE FROM advertisements WHERE id=?");
    $stmt->execute([$id]);
    redirect('admin/advertisements');
}
?>
<div class="container" style="max-width:500px;margin-top:60px;">
  <div class="card-dark p-4 text-center">
    <h4 class="text-white">Delete Advertisement?</h4>
    <form method="post">
      <?php echo csrf_field(); ?>
      <button class="btn btn-danger" type="submit">Delete</button>
      <a href="<?php echo base_url('admin/advertisements'); ?>" class="btn btn-outline-glow">Cancel</a>
    </form>
  </div>
</div>
