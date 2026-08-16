<?php
require_once __DIR__ . '/../includes/init.php';
if (!$isLoggedIn) { header('Location: login.php'); exit; }

$userId = $_GET['user'] ?? 0;
if ($userId == $currentUser['id']) { header('Location: profile.php'); exit; }

$stmt = $pdo->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
$stmt->execute([$currentUser['id'], $userId]);
$existing = $stmt->fetch();

if ($existing) {
    $pdo->prepare("DELETE FROM follows WHERE id = ?")->execute([$existing['id']]);
} else {
    $pdo->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)")->execute([$currentUser['id'], $userId]);
    $msg = $currentUser['name'] . ' بدأ متابعتك.';
    $pdo->prepare("INSERT INTO notifications (user_id, sender_id, type, message, link) VALUES (?, ?, 'follow', ?, ?)")
        ->execute([$userId, $currentUser['id'], $msg, 'profile.php?id=' . $currentUser['id']]);
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'profile.php?id=' . $userId));
exit;