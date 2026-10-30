<?php
require_once __DIR__ . '/functions.php';

function is_user_logged_in() {
    return !empty($_SESSION['user_id']);
}
function user_login($email, $password) {
    $stmt = db()->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && password_verify($password, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['user_name'] = $u['name'];
        $_SESSION['user_email'] = $u['email'];
        return true;
    }
    return false;
}
function user_register($name, $email, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('INSERT INTO users (name, email, password) VALUES (?,?,?)');
    try {
        $stmt->execute([$name, $email, $hash]);
        return (int)db()->lastInsertId();
    } catch (Exception $e) {
        return false;
    }
}
function user_logout() {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']);
}
function require_user() {
    if (!is_user_logged_in()) {
        redirect('login');
    }
}
