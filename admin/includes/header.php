<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

// auth guard except login
$route = $_GET['route'] ?? '';
$route = trim($route, '/');
if ($route !== 'login' && $route !== 'logout') {
    if (!is_admin_logged_in()) {
        header('Location: ' . SITE_URL . '/admin/login');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - <?php echo e(setting('site_name')); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/admin.css'); ?>" rel="stylesheet">
</head>
<body style="background:var(--bg);">
<div class="d-flex flex-column flex-md-row">
  <?php if (is_admin_logged_in() && $route !== 'login'): ?>
  <div class="d-md-none p-2" style="background:var(--bg2);border-bottom:1px solid var(--border);">
    <button class="btn btn-outline-glow btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#adminSidebar">Menu</button>
  </div>
  <div class="collapse d-md-block" id="adminSidebar" style="min-width:220px;">
    <?php include __DIR__ . '/sidebar.php'; ?>
  </div>
  <?php endif; ?>
  <div class="flex-grow-1" style="min-height:100vh;min-width:0;">
    <div class="admin-topbar d-flex justify-content-between align-items-center px-3 py-2" style="background:var(--bg2);border-bottom:1px solid var(--border);">
      <span class="text-white fw-bold"><?php echo e(setting('site_name')); ?> Admin</span>
      <div class="d-flex align-items-center gap-2">
        <?php if (is_admin_logged_in()): ?>
        <span class="text-muted small"><?php echo e($_SESSION['admin_name'] ?? ''); ?></span>
        <a href="<?php echo base_url('admin/logout'); ?>" class="btn btn-sm btn-outline-glow">Logout</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="container-fluid py-3">
