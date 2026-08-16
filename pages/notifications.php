<?php
$pageTitle = 'الإشعارات';
require_once __DIR__ . '/../includes/init.php';
if (!$isLoggedIn) { header('Location: login.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$currentUser['id']]);
$notifications = $stmt->fetchAll();

// تعليم الكل كمقروء
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0")->execute([$currentUser['id']]);

require_once __DIR__ . '/../includes/header.php';
?>
<main style="max-width: 600px; margin: 0 auto; padding: 1.5rem 1rem;">
    <h2>🔔 الإشعارات</h2>
    <?php if (count($notifications) === 0): ?>
        <div class="card"><p>لا توجد إشعارات.</p></div>
    <?php endif; ?>
    <?php foreach ($notifications as $notif): ?>
        <a href="<?= htmlspecialchars($notif['link'] ?? '#') ?>" style="text-decoration:none; color:inherit;">
            <div style="background: var(--bg-card); border-radius:12px; padding:0.75rem; margin-bottom:0.5rem; border:1px solid var(--border); <?= $notif['is_read'] ? '' : 'border-right:4px solid var(--primary);' ?>">
                <p><?= htmlspecialchars($notif['message']) ?></p>
                <small style="color: var(--text-secondary);"><?= date('Y/m/d H:i', strtotime($notif['created_at'])) ?></small>
            </div>
        </a>
    <?php endforeach; ?>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>