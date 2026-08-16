<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// تذكرني تلقائي
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'], $_COOKIE['remember_user'])) {
    $token = $_COOKIE['remember_token'];
    $userId = $_COOKIE['remember_user'];
    
    $stmt = $pdo->prepare("SELECT user_id FROM remember_tokens WHERE token = ? AND user_id = ? AND expires_at > NOW()");
    $stmt->execute([$token, $userId]);
    $tokenRow = $stmt->fetch();
    
    if ($tokenRow) {
        $_SESSION['user_id'] = $tokenRow['user_id'];
        $newToken = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        $stmt = $pdo->prepare("UPDATE remember_tokens SET token = ?, expires_at = ? WHERE token = ?");
        $stmt->execute([$newToken, $expires, $token]);
        setcookie('remember_token', $newToken, strtotime('+30 days'), '/', '', false, true);
        setcookie('remember_user', $userId, strtotime('+30 days'), '/', '', false, true);
    } else {
        setcookie('remember_token', '', time() - 3600, '/');
        setcookie('remember_user', '', time() - 3600, '/');
    }
}

$currentUser = null;
$isLoggedIn = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch();
    if ($currentUser) {
        $isLoggedIn = true;
    } else {
        session_destroy();
    }
}
?>