<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/user_auth.php';
$siteName = setting('site_name', 'STZ Creations');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($pageTitle ?? $siteName); ?></title>
<meta name="description" content="<?php echo e($pageDesc ?? setting('seo_description')); ?>">
<link rel="icon" href="<?php echo e(setting('site_favicon') ? base_url('uploads/'.setting('site_favicon')) : ''); ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
<?php if (!empty($ogImage)): ?>
<meta property="og:image" content="<?php echo e($ogImage); ?>">
<?php endif; ?>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top hd-nav">
  <div class="container">
    <a class="navbar-brand" href="<?php echo base_url(); ?>">
      <i data-lucide="package"></i> <?php echo e($siteName); ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse gap-2 justify-content-between" id="nav">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url(); ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('browse'); ?>">Browse</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('categories'); ?>">Categories</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('latest'); ?>">Latest</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('popular'); ?>">Popular</a></li>
      </ul>
      <?php if (is_user_logged_in()): ?>
        <span class="user-shimmer ms-2 me-2 small align-self-center d-none d-lg-inline"><?php echo e($_SESSION['user_name']); ?></span>
        <a href="<?php echo base_url('my-downloads'); ?>" class="btn btn-outline-glow w-100 w-lg-auto mb-2 mb-lg-0">My Downloads</a>
        <a href="<?php echo base_url('logout'); ?>" class="btn btn-outline-glow w-100 w-lg-auto">Logout</a>
      <?php else: ?>
        <a href="<?php echo base_url('login'); ?>" class="btn btn-outline-glow w-100 w-lg-auto me-2 mb-2 mb-lg-0">Login</a>
        <a href="<?php echo base_url('register'); ?>" class="btn btn-glow w-100 w-lg-auto mb-2 mb-lg-0">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<?php echo render_ad(get_ads('header')); ?>
<div class="container mt-3">
