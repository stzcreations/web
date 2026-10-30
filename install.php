<?php
$lockFile = __DIR__ . '/storage/installed.lock';
$installed = file_exists($lockFile);
if ($installed) { echo "Already installed. Delete storage/installed.lock to reinstall."; exit; }

$errors = []; $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['db_host'] ?? 'localhost');
    $name = trim($_POST['db_name'] ?? 'stzcreations');
    $user = trim($_POST['db_user'] ?? 'root');
    $pass = $_POST['db_pass'] ?? '';
    $adminUser = trim($_POST['admin_user'] ?? 'admin');
    $adminPass = $_POST['admin_pass'] ?? 'admin123';
    $adminEmail = trim($_POST['admin_email'] ?? 'admin@example.com');
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$name`");
        $sql = file_get_contents(__DIR__ . '/database/database.sql');
        // strip CREATE DATABASE / USE lines
        $sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
        $sql = preg_replace('/USE .*?;/is', '', $sql);
        $pdo->exec($sql);
        // set admin password
        $hash = password_hash($adminPass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE admins SET username=?, email=?, password=? WHERE id=1")->execute([$adminUser, $adminEmail, $hash]);
        // write config
        $config = "<?php\ndefine('DB_HOST', " . var_export($host, true) . ");\ndefine('DB_NAME', " . var_export($name, true) . ");\ndefine('DB_USER', " . var_export($user, true) . ");\ndefine('DB_PASS', " . var_export($pass, true) . ");\ndefine('DB_CHARSET', 'utf8mb4');\n";
        $config .= "define('SITE_URL', " . var_export((isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']),'/'), true) . ");\n";
        $config .= "define('BASE_PATH', dirname(__DIR__));\n";
        $config .= "define('UPLOAD_DIR', BASE_PATH . '/uploads');\ndefine('STORAGE_DIR', BASE_PATH . '/storage');\n";
        $config .= "define('CSRF_TOKEN_NAME', 'csrf_token');\ndefine('SESSION_NAME', 'stz_session');\ndefine('INSTALL_MODE', false);\n";
        file_put_contents(__DIR__ . '/config/config.php', $config);
        file_put_contents($lockFile, date('Y-m-d H:i:s'));
        $msg = "Installation successful. <a href='index.php'>Go to site</a>";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Install</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body style="background:#f8fafc;padding:40px;">
<div class="container" style="max-width:600px;">
  <div class="card p-4">
    <h3>Install Project Download Library</h3>
    <?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
    <?php if ($errors): ?><div class="alert alert-danger"><?php foreach($errors as $er) echo e($er).'<br>'; ?></div><?php endif; ?>
    <?php if (!$msg): ?>
    <form method="post">
      <h5>Database</h5>
      <input class="form-control mb-2" name="db_host" value="localhost" placeholder="Host">
      <input class="form-control mb-2" name="db_name" value="stzcreations" placeholder="Database">
      <input class="form-control mb-2" name="db_user" value="root" placeholder="User">
      <input class="form-control mb-2" name="db_pass" placeholder="Password" type="password">
      <h5 class="mt-3">Admin Account</h5>
      <input class="form-control mb-2" name="admin_user" value="admin" placeholder="Admin username">
      <input class="form-control mb-2" name="admin_pass" value="admin123" placeholder="Admin password">
      <input class="form-control mb-2" name="admin_email" value="admin@example.com" placeholder="Admin email">
      <button class="btn btn-primary mt-2" type="submit">Install</button>
    </form>
    <?php endif; ?>
  </div>
</div>
</body></html>
<?php
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
