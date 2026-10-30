<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/user_auth.php';
require_once __DIR__ . '/includes/google_oauth.php';

if (is_user_logged_in()) {
    redirect('');
}

$error = $_GET['error'] ?? '';
$errorMsg = '';
if ($error === 'invalid_state') $errorMsg = 'Invalid OAuth state. Please try again.';
elseif ($error === 'no_code') $errorMsg = 'Authorization failed. Please try again.';
elseif ($error === 'token_exchange') $errorMsg = 'Could not verify your Google account.';
elseif ($error === 'invalid_token') $errorMsg = 'Invalid Google token.';
elseif ($error === 'unverified_email') $errorMsg = 'Your Google email is not verified.';
elseif ($error === 'account_inactive') $errorMsg = 'Your account is inactive or suspended.';
elseif ($error === 'google_not_configured') $errorMsg = 'Google login is not configured yet. Please contact admin.';

$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>
<style>
  .auth-wrap { min-height: 70vh; display: flex; align-items: center; justify-content: center; padding: 40px 16px; }
  .auth-card { background: #fff; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,.08); padding: 40px 32px; max-width: 420px; width: 100%; text-align: center; border: 1px solid #e2e8f0; }
  .google-logo-circle { width: 64px; height: 64px; border-radius: 50%; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; }
  .auth-title { font-size: 1.6rem; font-weight: 700; color: #0f172a; margin-bottom: 6px; font-family: 'Sora', sans-serif; }
  .auth-sub { color: #64748b; margin-bottom: 24px; font-size: .95rem; }
  .btn-google { display: inline-flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 12px 16px; border-radius: 10px; background: #16a34a; color: #fff; font-weight: 600; font-size: 1rem; border: none; text-decoration: none; transition: background .2s, transform .2s; }
  .btn-google:hover { background: #15803d; color: #fff; transform: translateY(-1px); }
  .auth-note { color: #94a3b8; font-size: .85rem; margin-top: 16px; }
  .auth-link { margin-top: 18px; font-size: .9rem; color: #64748b; }
  .auth-link a { color: #2563eb; font-weight: 600; }
</style>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="google-logo-circle">
      <svg viewBox="0 0 48 48" width="32" height="32"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
    </div>
    <h1 class="auth-title">Login</h1>
    <p class="auth-sub">Sign in to your account with Google</p>
    <?php if ($errorMsg): ?><div class="alert alert-danger"><?php echo e($errorMsg); ?></div><?php endif; ?>
    <a href="<?php echo base_url('google-login.php' . (isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '')); ?>" class="btn-google">
      <svg viewBox="0 0 48 48" width="20" height="20"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
      Continue with Google
    </a>
    <p class="auth-note">Fast &amp; secure — no password needed</p>
    <p class="auth-link">Don't have an account? <a href="<?php echo base_url('register'); ?>">Sign Up</a></p>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
