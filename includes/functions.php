<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    session_name(defined('SESSION_NAME') ? SESSION_NAME : 'stz_session');
    session_start();
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function slugify($str) {
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    $str = trim($str, '-');
    return $str ?: 'item';
}

function redirect($url) {
    if (strpos($url, 'http') !== 0) $url = SITE_URL . '/' . ltrim($url, '/');
    header('Location: ' . $url);
    exit;
}

function is_admin_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function csrf_token() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function csrf_field() {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . e(csrf_token()) . '">';
}

function csrf_verify() {
    $token = $_POST[CSRF_TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $token)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}

function setting($key, $default = '') {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            $rows = db()->query('SELECT setting_key, setting_value FROM website_settings')->fetchAll();
            foreach ($rows as $r) $cache[$r['setting_key']] = $r['setting_value'];
        } catch (Exception $e) {}
    }
    return $cache[$key] ?? $default;
}

function format_size($bytes) {
    if (!$bytes) return '';
    $units = ['B','KB','MB','GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < 3) { $bytes /= 1024; $i++; }
    return round($bytes, 2) . ' ' . $units[$i];
}

function upload_file($file, $targetDir, $allowedExt, $allowedMime = []) {
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) return [false, 'Upload error'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) return [false, 'Invalid file extension'];
    if (!empty($allowedMime)) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowedMime)) return [false, 'Invalid file type'];
    }
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    $name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
    $dest = rtrim($targetDir, '/') . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return [false, 'Failed to move file'];
    return [true, $name];
}

function track_visitor() {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
        $page = substr($_SERVER['REQUEST_URI'] ?? '', 0, 255);
        $stmt = db()->prepare('INSERT INTO visitors (ip_address, user_agent, page) VALUES (?,?,?)');
        $stmt->execute([$ip, $ua, $page]);
    } catch (Exception $e) {}
}

function get_ads($position) {
    try {
        $stmt = db()->prepare("SELECT * FROM advertisements WHERE position=? AND status='enabled' ORDER BY id DESC");
        $stmt->execute([$position]);
        return $stmt->fetchAll();
    } catch (Exception $e) { return []; }
}

function render_ad($ads) {
    if (empty($ads)) return '';
    $out = '';
    foreach ($ads as $ad) {
        if (!empty($ad['code'])) {
            if (($ad['position'] ?? '') === 'socialbar') {
                $out .= $ad['code'];
            } elseif (($ad['position'] ?? '') === 'footer') {
                $out .= '<div class="ad-banner d-block mb-1">' . $ad['code'] . '</div>';
            } else {
                $out .= '<div class="ad-banner d-block mb-3">' . $ad['code'] . '</div>';
            }
        } else {
            $img = $ad['image_path'] ? SITE_URL . '/uploads/ads/' . $ad['image_path'] : '';
            $mb = (($ad['position'] ?? '') === 'footer') ? 'mb-1' : 'mb-3';
            $out .= '<a href="' . e($ad['url']) . '" target="_blank" rel="noopener" class="ad-banner d-block ' . $mb . '">';
            if ($img) $out .= '<img src="' . e($img) . '" alt="' . e($ad['title']) . '" class="img-fluid rounded" style="width:100%;max-height:120px;object-fit:cover;">';
            else $out .= '<div class="ad-slot"><script>atOptions = { "key" : "101f9cf4aa9700db314ed8908a39fd03", "format" : "iframe", "height" : 90, "width" : 728, "params" : {} };</script><script src="https://bauval.org/22/101f9cf4aa9700db314ed8908a39fd03"></script></div>';
            $out .= '</a>';
        }
    }
    return $out;
}

function paginate($total, $perPage, $current) {
    $pages = (int)ceil($total / $perPage);
    if ($pages < 1) $pages = 1;
    if ($current < 1) $current = 1;
    if ($current > $pages) $current = $pages;
    return ['total'=>$total,'pages'=>$pages,'current'=>$current,'offset'=>($current-1)*$perPage,'perPage'=>$perPage];
}

function base_url($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}
