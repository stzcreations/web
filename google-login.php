<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/user_auth.php';
require_once __DIR__ . '/includes/google_oauth.php';

if (is_user_logged_in()) {
    $redir = $_SESSION['login_redirect'] ?? '';
    unset($_SESSION['login_redirect']);
    if ($redir) redirect(ltrim($redir, '/'));
    redirect('');
}
if (google_client_id() === '' || google_client_id() === 'YOUR_GOOGLE_CLIENT_ID') {
    redirect('login?error=google_not_configured');
}
$redir = $_GET['redirect'] ?? '';
if ($redir) $_SESSION['login_redirect'] = $redir;
header('Location: ' . google_auth_url());
exit;
