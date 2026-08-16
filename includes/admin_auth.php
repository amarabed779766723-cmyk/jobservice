<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$adminUser = $stmt->fetch();

// الشرط الوحيد: يجب أن يكون role = 'admin'
if (!$adminUser || $adminUser['role'] !== 'admin') {
    header('Location: ../pages/home.php');
    exit;
}
?>