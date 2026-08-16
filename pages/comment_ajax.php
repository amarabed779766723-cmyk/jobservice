<?php
require_once __DIR__ . '/../includes/init.php';

if (!$isLoggedIn) {
    echo json_encode(['success' => false, 'error' => 'تسجيل الدخول مطلوب']);
    exit;
}

$postId = $_POST['post_id'] ?? 0;
$text = trim($_POST['comment_text'] ?? '');
$parentId = $_POST['parent_id'] ?? null;

if (empty($text)) {
    echo json_encode(['success' => false, 'error' => 'نص فارغ']);
    exit;
}

// إدخال التعليق
$stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment_text, parent_id) VALUES (?, ?, ?, ?)");
$stmt->execute([$currentUser['id'], $postId, $text, $parentId ? $parentId : null]);

// إشعار لصاحب المنشور
$stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->execute([$postId]);
$postOwnerId = $stmt->fetchColumn();

if ($postOwnerId && $postOwnerId != $currentUser['id']) {
    $msg = $currentUser['name'] . ' علّق على منشورك.';
    $link = 'post-detail.php?id=' . $postId;
    try {
        $pdo->prepare("INSERT INTO notifications (user_id, sender_id, type, message, link) VALUES (?, ?, 'comment', ?, ?)")
            ->execute([$postOwnerId, $currentUser['id'], $msg, $link]);
    } catch (Exception $e) { }
}

// إشعار لصاحب التعليق الأصلي إذا كان رداً
if ($parentId) {
    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
    $stmt->execute([$parentId]);
    $parentOwnerId = $stmt->fetchColumn();
    if ($parentOwnerId && $parentOwnerId != $currentUser['id']) {
        $msg = $currentUser['name'] . ' رد على تعليقك.';
        $link = 'post-detail.php?id=' . $postId;
        try {
            $pdo->prepare("INSERT INTO notifications (user_id, sender_id, type, message, link) VALUES (?, ?, 'reply', ?, ?)")
                ->execute([$parentOwnerId, $currentUser['id'], $msg, $link]);
        } catch (Exception $e) { }
    }
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
$countStmt->execute([$postId]);
$newCount = $countStmt->fetchColumn();

echo json_encode(['success' => true, 'comments_count' => (int)$newCount]);