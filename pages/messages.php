<?php
$pageTitle = 'الرسائل';
require_once __DIR__ . '/../includes/init.php';
if (!$isLoggedIn) { header('Location: login.php'); exit; }

// جلب المحادثات التي يشارك فيها المستخدم مع آخر رسالة وعدد غير المقروء
$stmt = $pdo->prepare("
    SELECT c.id AS chat_id, u.id AS other_user_id, u.name AS other_user_name, u.avatar AS other_user_avatar,
           (SELECT message_text FROM messages WHERE chat_id = c.id ORDER BY created_at DESC LIMIT 1) AS last_message,
           (SELECT created_at FROM messages WHERE chat_id = c.id ORDER BY created_at DESC LIMIT 1) AS last_time,
           (SELECT COUNT(*) FROM messages WHERE chat_id = c.id AND sender_id != ?) AS unread_count
    FROM chats c
    JOIN chat_participants cp1 ON c.id = cp1.chat_id AND cp1.user_id = ?
    JOIN chat_participants cp2 ON c.id = cp2.chat_id AND cp2.user_id != ?
    JOIN users u ON u.id = cp2.user_id
    ORDER BY last_time DESC
");
$stmt->execute([$currentUser['id'], $currentUser['id'], $currentUser['id']]);
$chats = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 600px; margin: 0 auto; padding: 1.5rem 1rem;">
    <h2>💬 المحادثات</h2>
    <?php if (count($chats) === 0): ?>
        <div class="card"><p>لا توجد محادثات حالياً. ابدأ محادثة من صفحة طلب أو ملف شخصي.</p></div>
    <?php endif; ?>
    <?php foreach ($chats as $chat): ?>
        <a href="chat.php?id=<?= $chat['chat_id'] ?>" style="text-decoration:none; color:inherit;">
            <div class="card" style="display:flex; align-items:center; gap:1rem;">
                <img src="../uploads/avatars/<?= htmlspecialchars($chat['other_user_avatar'] ?? 'default-avatar.png') ?>" style="width:48px;height:48px;border-radius:50%;">
                <div style="flex:1;">
                    <strong><?= htmlspecialchars($chat['other_user_name']) ?></strong>
                    <p style="color:var(--text-secondary); margin:0.25rem 0 0;"><?= htmlspecialchars($chat['last_message'] ?? 'لا توجد رسائل') ?></p>
                </div>
                <div style="text-align:left;">
                    <?php if ($chat['last_time']): ?>
                        <small style="color:var(--text-secondary); display:block;"><?= date('H:i', strtotime($chat['last_time'])) ?></small>
                    <?php endif; ?>
                    <?php if ($chat['unread_count'] > 0): ?>
                        <span style="background:var(--primary); color:white; border-radius:50%; padding:0.1rem 0.4rem; font-size:0.7rem;"><?= $chat['unread_count'] ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>