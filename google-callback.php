<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/user_auth.php';
require_once __DIR__ . '/includes/google_oauth.php';

if (is_user_logged_in()) {
    redirect('');
}

// Validate state
$state = $_GET['state'] ?? '';
if (empty($state) || empty($_SESSION['oauth_state']) || !hash_equals($_SESSION['oauth_state'], $state)) {
    redirect('login?error=invalid_state');
}
unset($_SESSION['oauth_state']);

$code = $_GET['code'] ?? '';
if (empty($code)) {
    redirect('login?error=no_code');
}

$tokenData = google_exchange_code($code);
if (empty($tokenData['id_token'])) {
    redirect('login?error=token_exchange');
}

$claims = google_verify_id_token($tokenData['id_token']);
if (!$claims) {
    redirect('login?error=invalid_token');
}

$googleId = $claims['sub'] ?? '';
$email = $claims['email'] ?? '';
$name = $claims['name'] ?? '';
$picture = $claims['picture'] ?? '';
$emailVerified = !empty($claims['email_verified']);

if (!$googleId || !$email || !$emailVerified) {
    redirect('login?error=unverified_email');
}

// Find or create user
$stmt = db()->prepare("SELECT * FROM users WHERE google_id=? OR email=? LIMIT 1");
$stmt->execute([$googleId, $email]);
$user = $stmt->fetch();

if ($user) {
    // Check status
    if ($user['status'] === 'suspended' || $user['status'] === 'inactive') {
        redirect('login?error=account_inactive');
    }
    // Update google_id if missing, profile picture, last_login
    $upd = db()->prepare("UPDATE users SET google_id=?, name=?, profile_picture=?, email_verified=1, last_login_at=NOW() WHERE id=?");
    $upd->execute([$googleId, $name, $picture, $user['id']]);
} else {
    // Create new user
    $ins = db()->prepare("INSERT INTO users (google_id, name, email, profile_picture, email_verified, status, last_login_at) VALUES (?,?,?,?,1,'active',NOW())");
    $ins->execute([$googleId, $name, $email, $picture]);
    $userId = db()->lastInsertId();
    $stmt = db()->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
}

// Check status again
if ($user['status'] === 'suspended' || $user['status'] === 'inactive') {
    redirect('login?error=account_inactive');
}

// Create session
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];

$redir = $_SESSION['login_redirect'] ?? '';
unset($_SESSION['login_redirect']);
if ($redir) redirect(ltrim($redir, '/'));
redirect('');
