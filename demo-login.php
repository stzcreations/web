<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/user_auth.php';

// Find or create demo user
$stmt = db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->execute(['demo@stzcreations.com']);
$user = $stmt->fetch();
if (!$user) {
    $ins = db()->prepare("INSERT INTO users (google_id, name, email, profile_picture, email_verified, status, last_login_at) VALUES (?,?,?,?,1,'active',NOW())");
    $ins->execute(['demo-google-id', 'Demo User', 'demo@stzcreations.com', '']);
    $id = db()->lastInsertId();
    $stmt = db()->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
} else {
    db()->prepare("UPDATE users SET last_login_at=NOW() WHERE id=?")->execute([$user['id']]);
}

session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];

$redir = $_GET['redirect'] ?? $_SESSION['login_redirect'] ?? '';
unset($_SESSION['login_redirect']);
if ($redir) redirect(ltrim($redir, '/'));
redirect('');
