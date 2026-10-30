<?php
// Copy this file to config/config.php and fill in your details.
define('DB_HOST', 'localhost');
define('DB_NAME', 'stzcreations');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_URL', 'http://localhost/stzcreations');
define('BASE_PATH', __DIR__ . '/..');

define('UPLOAD_DIR', BASE_PATH . '/uploads');
define('STORAGE_DIR', BASE_PATH . '/storage');

define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_NAME', 'stz_session');

// Set to false after installation
define('INSTALL_MODE', false);
