<?php require_once __DIR__ . '/init.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Job Service' ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-left">
        <a href="home.php" class="topbar-logo">Job Service</a>
    </div>
    <div class="topbar-center">
        <a href="home.php" class="topbar-icon <?= basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : '' ?>">🏠</a>
        <?php if ($isLoggedIn): ?>
            <a href="requests.php" class="topbar-icon">📋</a>
            <a href="messages.php" class="topbar-icon">💬</a>
            <a href="stories.php" class="topbar-icon">📖</a>
            <!-- أيقونة الإشعارات مع العداد -->
            <a href="notifications.php" class="topbar-icon" style="position:relative;">
                🔔
                <?php
                $unreadStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
                $unreadStmt->execute([$currentUser['id']]);
                $unread = $unreadStmt->fetchColumn();
                ?>
                <?php if ($unread > 0): ?>
                    <span style="position:absolute; top:2px; right:2px; background:red; color:white; border-radius:50%; width:18px; height:18px; font-size:0.7rem; display:flex; align-items:center; justify-content:center;">
                        <?= $unread > 9 ? '9+' : $unread ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endif; ?>
    </div>
    <div class="topbar-right">
        <?php if ($isLoggedIn): ?>
            <span style="font-size:0.85rem;">💰 <?= number_format($currentUser['wallet_balance'] ?? 0, 2) ?></span>
            <a href="profile.php">
                <img src="../uploads/avatars/<?= htmlspecialchars($currentUser['avatar'] ?? 'default-avatar.png') ?>"
                     class="topbar-avatar" alt="أنت">
            </a>
        <?php else: ?>
            <a href="register.php" class="btn btn-primary" style="padding: 0.4rem 1rem; font-size: 0.9rem;">إنشاء حساب</a>
            <a href="login.php" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.9rem;">تسجيل الدخول</a>
        <?php endif; ?>
    </div>
</header>

<!-- مخزن الصوت -->
<audio id="notifAudio" preload="auto" style="display:none;">
    <source src="../assets/audio/notification.mp3" type="audio/mpeg">
</audio>

<!-- تطبيق الوضع الليلي فورًا -->
<script>
(function() {
    var theme = localStorage.getItem('theme');
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
    }
})();
</script>

<!-- منطق صوت الإشعارات -->
<?php if ($isLoggedIn): ?>
<script>
(function() {
    var currentUnread = <?= (int)$unread ?>;
    var lastUnread = parseInt(localStorage.getItem('lastUnread') || '0');
    if (currentUnread > lastUnread) {
        var audio = document.getElementById('notifAudio');
        if (audio) {
            audio.play().then(function() {}).catch(function(e) {
                document.body.addEventListener('click', function playOnce() {
                    audio.play().catch(function(){});
                    audio.pause();
                    audio.currentTime = 0;
                    document.body.removeEventListener('click', playOnce);
                }, { once: true });
            });
        }
        localStorage.setItem('lastUnread', currentUnread);
    }
    if (currentUnread === 0) {
        localStorage.removeItem('lastUnread');
    }
})();
</script>
<?php endif; ?>