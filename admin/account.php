<?php if (!defined('IN_ADMIN')) { http_response_code(403); exit('Forbidden'); } ?>
<?php
$adminId = $_SESSION['admin_id'];
$stmt = db()->prepare("SELECT * FROM admins WHERE id=?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();
$msg = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!password_verify($current, $admin['password'])) $errors[] = 'Current password wrong';
        elseif (strlen($new) < 6) $errors[] = 'New password too short';
        elseif ($new !== $confirm) $errors[] = 'Passwords do not match';
        else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $u = db()->prepare("UPDATE admins SET password=? WHERE id=?");
            $u->execute([$hash, $adminId]);
            $msg = 'Password changed';
        }
    }
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if ($name === '' || $email === '') $errors[] = 'Name and email required';
        else {
            $u = db()->prepare("UPDATE admins SET name=?, email=? WHERE id=?");
            $u->execute([$name, $email, $adminId]);
            $_SESSION['admin_name'] = $name;
            $msg = 'Profile updated';
            $stmt = db()->prepare("SELECT * FROM admins WHERE id=?");
            $stmt->execute([$adminId]);
            $admin = $stmt->fetch();
        }
    }
}
?>
<h2 class="text-white mb-3">Account</h2>
<?php if ($msg): ?><div class="alert alert-success"><?php echo e($msg); ?></div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach($errors as $er) echo e($er).'<br>'; ?></div><?php endif; ?>
<div class="row">
  <div class="col-md-6">
    <div class="card-dark p-4">
      <h5 class="text-white">Profile</h5>
      <form method="post">
        <?php echo csrf_field(); ?>
        <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="<?php echo e($admin['name']); ?>"></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo e($admin['email']); ?>"></div>
        <div class="mb-3"><label class="form-label">Username</label><input type="text" class="form-control" value="<?php echo e($admin['username']); ?>" disabled></div>
        <button class="btn btn-glow" name="update_profile" value="1" type="submit">Update Profile</button>
      </form>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card-dark p-4">
      <h5 class="text-white">Change Password</h5>
      <form method="post">
        <?php echo csrf_field(); ?>
        <div class="mb-3"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
        <button class="btn btn-glow" name="change_password" value="1" type="submit">Change Password</button>
      </form>
    </div>
  </div>
</div>
