<?php
require_once __DIR__ . '/includes/header.php';

$route = $_GET['route'] ?? '';
$route = trim($route, '/');
$parts = $route === '' ? [] : explode('/', $route);

// map
$page = $parts[0] ?? 'dashboard';
$allowed = ['login','logout','dashboard','projects','project_create','project_edit','project_delete','categories','category_create','category_edit','category_delete','advertisements','advertisement_create','advertisement_edit','advertisement_delete','settings','account','users'];
// normalize projects/create etc
if ($page === 'projects' && isset($parts[1])) {
    if ($parts[1] === 'create') $page = 'project_create';
    elseif ($parts[1] === 'edit') { $page = 'project_edit'; $_GET['id'] = $parts[2] ?? 0; }
    elseif ($parts[1] === 'delete') { $page = 'project_delete'; $_GET['id'] = $parts[2] ?? 0; }
}
if ($page === 'categories' && isset($parts[1])) {
    if ($parts[1] === 'create') $page = 'category_create';
    elseif ($parts[1] === 'edit') { $page = 'category_edit'; $_GET['id'] = $parts[2] ?? 0; }
    elseif ($parts[1] === 'delete') { $page = 'category_delete'; $_GET['id'] = $parts[2] ?? 0; }
}
if ($page === 'advertisements' && isset($parts[1])) {
    if ($parts[1] === 'create') $page = 'advertisement_create';
    elseif ($parts[1] === 'edit') { $page = 'advertisement_edit'; $_GET['id'] = $parts[2] ?? 0; }
    elseif ($parts[1] === 'delete') { $page = 'advertisement_delete'; $_GET['id'] = $parts[2] ?? 0; }
}
if ($page === 'dashboard' && $route === '') $page = 'dashboard';
if (!in_array($page, $allowed)) { $page = 'dashboard'; }

$file = __DIR__ . '/' . $page . '.php';
if (!is_file($file)) { $file = __DIR__ . '/dashboard.php'; }
define('IN_ADMIN', true);
include $file;

require_once __DIR__ . '/includes/footer.php';
