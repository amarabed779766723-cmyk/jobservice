<?php
require_once __DIR__ . '/../includes/init.php';

if (!$isLoggedIn) {
    echo json_encode(['success' => false, 'error' => 'تسجيل الدخول مطلوب']);
    exit;
}

$postId = $_POST['post_id'] ?? 0;

// جلب مالك المنشور
$ownerStmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
$ownerStmt->execute([$postId]);
$postOwnerId = $ownerStmt->fetchColumn();

// هل معجب سابقاً؟
$stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->execute([$currentUser['id'], $postId]);
$existing = $stmt->fetch();

if ($existing) {
    $pdo->prepare("DELETE FROM likes WHERE id = ?")->execute([$existing['id']]);
    $liked = false;
} else {
    $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)")->execute([$currentUser['id'], $postId]);
    $liked = true;

    // إشعار إذا لم يكن المنشور له
    if ($postOwnerId && $postOwnerId != $currentUser['id']) {
        $msg = $currentUser['name'] . ' أعجب بمنشورك.';
        $link = 'post-detail.php?id=' . $postId;
        try {
            $pdo->prepare("INSERT INTO notifications (user_id, sender_id, type, message, link) VALUES (?, ?, 'like', ?, ?)")
                ->execute([$postOwnerId, $currentUser['id'], $msg, $link]);
        } catch (Exception $e) {
            // إذا فشل الإشعار، لا يؤثر على الإعجاب
        }
    }
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
$countStmt->execute([$postId]);
$newCount = $countStmt->fetchColumn();

echo json_encode(['success' => true, 'liked' => $liked, 'likes_count' => (int)$newCount]);