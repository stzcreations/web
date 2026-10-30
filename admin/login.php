<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
// login page (no guard)
if (is_admin_logged_in()) { redirect('admin'); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!check_login_rate($ip)) {
        $error = 'Too many attempts. Try later.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';
        if (admin_login($u, $p)) {
            record_login_attempt($ip, true);
            redirect('admin');
        } else {
            record_login_attempt($ip, false);
            $error = 'Invalid credentials';
        }
    }
}
?>
<div class="container" style="max-width:420px;margin-top:80px;">
  <div class="card-dark p-4">
    <h3 class="text-white text-center mb-3">Admin Login</h3>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label class="form-label">Username or Email</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-glow w-100" type="submit">Login</button>
    </form>
    <p class="text-muted small mt-3 text-center">Default: admin / admin123</p>
  </div>
</div>
<?php
// prevent footer guard? footer ok
