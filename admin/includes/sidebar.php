<div class="admin-sidebar p-3" style="width:100%;min-height:auto;background:var(--bg2);border-right:none;border-bottom:1px solid var(--border);">
  <div class="d-flex align-items-center gap-2 mb-3 pb-2" style="border-bottom:1px solid var(--border);">
    <i data-lucide="package" style="color:#2563eb;"></i>
    <span class="fw-bold" style="color:#0f172a;"><?php echo e(setting('site_name')); ?></span>
  </div>
  <h5 class="text-white mb-3">Admin</h5>
  <ul class="nav flex-column">
    <li class="nav-item"><a class="nav-link text-light" href="<?php echo base_url('admin'); ?>"><i data-lucide="layout-dashboard"></i> Dashboard</a></li>
    <li class="nav-item"><a class="nav-link text-light" href="<?php echo base_url('admin/projects'); ?>"><i data-lucide="box"></i> Projects</a></li>
    <li class="nav-item"><a class="nav-link text-light" href="<?php echo base_url('admin/categories'); ?>"><i data-lucide="folder"></i> Categories</a></li>
    <li class="nav-item"><a class="nav-link text-light" href="<?php echo base_url('admin/advertisements'); ?>"><i data-lucide="megaphone"></i> Advertisements</a></li>
    <li class="nav-item"><a class="nav-link text-light" href="<?php echo base_url('admin/settings'); ?>"><i data-lucide="settings"></i> Settings</a></li>
    <li class="nav-item"><a class="nav-link text-light" href="<?php echo base_url('admin/users'); ?>"><i data-lucide="users"></i> Users</a></li>
    <li class="nav-item"><a class="nav-link text-light" href="<?php echo base_url('admin/account'); ?>"><i data-lucide="user"></i> Account</a></li>
    <li class="nav-item"><a class="nav-link text-light" href="<?php echo base_url('admin/logout'); ?>"><i data-lucide="log-out"></i> Logout</a></li>
  </ul>
</div>
