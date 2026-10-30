<?php
require_once __DIR__ . '/functions.php';

function admin_login($username, $password) {
    $stmt = db()->prepare('SELECT * FROM admins WHERE username=? OR email=? LIMIT 1');
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }
    return false;
}

function admin_logout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function require_admin() {
    if (!is_admin_logged_in()) {
        redirect('admin/login');
    }
}

function check_login_rate($ip) {
    // simple: store attempts in session
    $key = 'login_attempts_' . md5($ip);
    $attempts = $_SESSION[$key] ?? ['count'=>0,'time'=>time()];
    if (time() - $attempts['time'] > 900) {
        $attempts = ['count'=>0,'time'=>time()];
    }
    if ($attempts['count'] >= 5) {
        return false;
    }
    return true;
}

function record_login_attempt($ip, $success) {
    $key = 'login_attempts_' . md5($ip);
    $attempts = $_SESSION[$key] ?? ['count'=>0,'time'=>time()];
    if (time() - $attempts['time'] > 900) $attempts = ['count'=>0,'time'=>time()];
    if ($success) {
        unset($_SESSION[$key]);
    } else {
        $attempts['count']++;
        $attempts['time'] = $attempts['time'] ?: time();
        $_SESSION[$key] = $attempts;
    }
}
