<?php
// includes/init.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

$currentUser = null;
$isLoggedIn = false;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch();
    if ($currentUser) {
        $isLoggedIn = true;
    } else {
        // جلسة غير صالحة
        session_destroy();
    }
}
?>