<?php
require_once __DIR__ . '/../includes/init.php';
if (!$isLoggedIn) { header('Location: login.php'); exit; }

$otherId = $_GET['with'] ?? 0;

// لا يمكن مراسلة النفس
if ($otherId == $currentUser['id']) {
    header('Location: messages.php');
    exit;
}

// هل توجد محادثة أصلاً بينهم؟
$stmt = $pdo->prepare("
    SELECT c.id FROM chats c
    JOIN chat_participants cp1 ON c.id = cp1.chat_id AND cp1.user_id = ?
    JOIN chat_participants cp2 ON c.id = cp2.chat_id AND cp2.user_id = ?
");
$stmt->execute([$currentUser['id'], $otherId]);
$chat = $stmt->fetch();

if ($chat) {
    // توجد محادثة، نذهب إليها
    header('Location: chat.php?id=' . $chat['id']);
    exit;
}

// لا توجد، ننشئ واحدة جديدة
$pdo->prepare("INSERT INTO chats () VALUES ()")->execute();
$newChatId = $pdo->lastInsertId();

$pdo->prepare("INSERT INTO chat_participants (chat_id, user_id) VALUES (?, ?), (?, ?)")
    ->execute([$newChatId, $currentUser['id'], $newChatId, $otherId]);

header('Location: chat.php?id=' . $newChatId);
exit;