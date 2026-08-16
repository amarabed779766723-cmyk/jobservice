<?php
$pageTitle = 'المحادثة';
require_once __DIR__ . '/../includes/init.php';
if (!$isLoggedIn) { header('Location: login.php'); exit; }

$chatId = $_GET['id'] ?? 0;

// التأكد من المشاركة
$stmt = $pdo->prepare("SELECT * FROM chat_participants WHERE chat_id = ? AND user_id = ?");
$stmt->execute([$chatId, $currentUser['id']]);
if (!$stmt->fetch()) { header('Location: messages.php'); exit; }

// تحديث آخر ظهور للمستخدم الحالي
$pdo->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?")->execute([$currentUser['id']]);

// جلب الطرف الآخر
$other = $pdo->prepare("
    SELECT u.id, u.name, u.avatar, u.last_seen
    FROM chat_participants cp
    JOIN users u ON cp.user_id = u.id
    WHERE cp.chat_id = ? AND cp.user_id != ?
");
$other->execute([$chatId, $currentUser['id']]);
$otherUser = $other->fetch();

// هل الطرف الآخر متصل؟ (آخر ظهور منذ أقل من 5 دقائق)
$isOnline = false;
if ($otherUser['last_seen']) {
    $lastSeen = strtotime($otherUser['last_seen']);
    $isOnline = (time() - $lastSeen) < 300; // 5 دقائق
}

// معالجة إرسال رسالة نصية
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $pdo->prepare("INSERT INTO messages (chat_id, sender_id, message_text) VALUES (?, ?, ?)")
            ->execute([$chatId, $currentUser['id'], $msg]);
        
        // إشعار للطرف الآخر
        $notifMsg = $currentUser['name'] . ' أرسل لك رسالة.';
        $pdo->prepare("INSERT INTO notifications (user_id, sender_id, type, message, link) VALUES (?, ?, 'chat_message', ?, ?)")
            ->execute([$otherUser['id'], $currentUser['id'], $notifMsg, 'chat.php?id=' . $chatId]);
    }
    header('Location: chat.php?id=' . $chatId);
    exit;
}

// معالجة إرسال صورة
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (in_array($file['type'], $allowed) && $file['size'] < 5 * 1024 * 1024) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageName = 'chat_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/chats/' . $imageName);
        $pdo->prepare("INSERT INTO messages (chat_id, sender_id, image) VALUES (?, ?, ?)")
            ->execute([$chatId, $currentUser['id'], $imageName]);
    }
    header('Location: chat.php?id=' . $chatId);
    exit;
}

// معالجة إرسال ملف
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['file'];
    if ($file['size'] < 20 * 1024 * 1024) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'file_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/chats/' . $fileName);
        $pdo->prepare("INSERT INTO messages (chat_id, sender_id, file_attachment, file_name) VALUES (?, ?, ?, ?)")
            ->execute([$chatId, $currentUser['id'], $fileName, $file['name']]);
    }
    header('Location: chat.php?id=' . $chatId);
    exit;
}

// تعليم رسائل هذا الشات كمقروءة للمستخدم الحالي
// (يمكن إضافة حقل read_at في المستقبل)

// جلب الرسائل
$msgs = $pdo->prepare("
    SELECT m.*, u.name AS sender_name, u.avatar AS sender_avatar 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id 
    WHERE m.chat_id = ? 
    ORDER BY m.created_at ASC
");
$msgs->execute([$chatId]);
$messages = $msgs->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 700px; margin: 0 auto; padding: 1rem; display:flex; flex-direction:column; height:85vh;">
    <!-- رأس المحادثة -->
    <div style="display:flex; align-items:center; gap:1rem; padding: 0.75rem; background: var(--bg-card); border-radius: 12px; margin-bottom: 0.5rem; box-shadow: var(--shadow);">
        <a href="messages.php" style="color:var(--primary); text-decoration:none; font-size:1.2rem;">←</a>
        <div style="position:relative;">
            <img src="../uploads/avatars/<?= htmlspecialchars($otherUser['avatar'] ?? 'default-avatar.png') ?>" 
                 style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
            <span style="position:absolute; bottom:2px; right:2px; width:12px; height:12px; border-radius:50%; background:<?= $isOnline ? '#10b981' : '#9ca3af' ?>; border:2px solid white;"></span>
        </div>
        <div>
            <strong><?= htmlspecialchars($otherUser['name']) ?></strong>
            <div style="font-size:0.75rem; color: var(--text-secondary);">
                <?= $isOnline ? '🟢 متصل الآن' : 'آخر ظهور: ' . ($otherUser['last_seen'] ? date('H:i', strtotime($otherUser['last_seen'])) : 'غير معروف') ?>
            </div>
        </div>
        <!-- أيقونة البحث في المحادثة (اختياري) -->
        <button onclick="toggleSearch()" style="margin-right:auto; background:none; border:none; font-size:1.2rem; cursor:pointer;">🔍</button>
    </div>

    <!-- شريط البحث (مخفي) -->
    <div id="searchBar" style="display:none; margin-bottom:0.5rem;">
        <input type="text" id="searchInput" placeholder="ابحث في المحادثة..." 
               style="width:100%; padding:0.5rem; border:1px solid var(--border); border-radius:2rem;"
               oninput="searchMessages()">
    </div>

    <!-- منطقة الرسائل -->
    <div id="messagesContainer" style="flex:1; overflow-y:auto; padding: 1rem; background: var(--bg); border-radius: 12px; margin-bottom: 0.5rem;">
        <?php foreach ($messages as $msg): ?>
            <div class="message-item" data-search="<?= htmlspecialchars($msg['message_text'] ?? '') ?>" 
                 style="margin-bottom:0.75rem; text-align:<?= $msg['sender_id'] == $currentUser['id'] ? 'left' : 'right' ?>;">
                <!-- إذا كانت صورة -->
                <?php if ($msg['image']): ?>
                    <a href="../uploads/chats/<?= htmlspecialchars($msg['image']) ?>" target="_blank">
                        <img src="../uploads/chats/<?= htmlspecialchars($msg['image']) ?>" 
                             style="max-width:200px; max-height:200px; border-radius:12px; cursor:pointer;">
                    </a>
                <?php endif; ?>
                
                <!-- إذا كان ملف -->
                <?php if ($msg['file_attachment']): ?>
                    <div style="display:inline-block; max-width:75%; padding:0.5rem 1rem; border-radius:12px; background:<?= $msg['sender_id'] == $currentUser['id'] ? 'var(--primary)' : 'var(--bg-card)' ?>; color:<?= $msg['sender_id'] == $currentUser['id'] ? 'white' : 'var(--text)' ?>;">
                        📎 <a href="../uploads/chats/<?= htmlspecialchars($msg['file_attachment']) ?>" download style="color:inherit;"><?= htmlspecialchars($msg['file_name'] ?? 'تحميل') ?></a>
                    </div>
                <?php endif; ?>
                
                <!-- إذا كانت رسالة نصية -->
                <?php if ($msg['message_text']): ?>
                    <div style="display:inline-block; max-width:75%; padding:0.5rem 1rem; border-radius:12px; background:<?= $msg['sender_id'] == $currentUser['id'] ? 'var(--primary)' : 'var(--bg-card)' ?>; color:<?= $msg['sender_id'] == $currentUser['id'] ? 'white' : 'var(--text)' ?>;">
                        <?= htmlspecialchars($msg['message_text']) ?>
                    </div>
                <?php endif; ?>
                
                <div style="font-size:0.7rem; color:var(--text-secondary); margin-top:0.25rem;">
                    <?= $msg['sender_id'] == $currentUser['id'] ? 'أنت' : htmlspecialchars($msg['sender_name']) ?> · <?= date('H:i', strtotime($msg['created_at'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- شريط الإدخال السفلي -->
    <div style="display:flex; gap:0.5rem; align-items:center; padding:0.5rem; background:var(--bg-card); border-radius:12px; box-shadow:var(--shadow);">
        <!-- رفع ملف -->
        <form method="POST" enctype="multipart/form-data" style="display:flex; gap:0.5rem; flex:1; align-items:center;">
            <label for="fileUpload" style="cursor:pointer; font-size:1.3rem;">📎</label>
            <input type="file" id="fileUpload" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.txt,.mp3,.wav" 
                   style="display:none;" onchange="this.form.submit()">
            
            <label for="imageUpload" style="cursor:pointer; font-size:1.3rem;">📷</label>
            <input type="file" id="imageUpload" name="image" accept="image/*" 
                   style="display:none;" onchange="this.form.submit()">
            
            <input type="text" name="message" placeholder="اكتب رسالتك..." 
                   style="flex:1; padding:0.75rem; border:1px solid var(--border); border-radius:2rem; font-family: 'Tajawal', sans-serif;"
                   id="messageInput">
            
            <button type="submit" style="background:var(--primary); color:white; border:none; width:40px; height:40px; border-radius:50%; cursor:pointer; font-size:1.2rem;">➤</button>
        </form>
    </div>
</main>

<script>
// التمرير التلقائي لآخر رسالة
window.onload = function() {
    var container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
};

// إظهار/إخفاء البحث
function toggleSearch() {
    var bar = document.getElementById('searchBar');
    bar.style.display = bar.style.display === 'none' ? 'block' : 'none';
    if (bar.style.display === 'block') {
        document.getElementById('searchInput').focus();
    }
}

// البحث في الرسائل
function searchMessages() {
    var input = document.getElementById('searchInput').value.toLowerCase();
    var items = document.querySelectorAll('.message-item');
    items.forEach(function(item) {
        var text = item.getAttribute('data-search').toLowerCase();
        item.style.display = text.includes(input) ? 'block' : 'none';
    });
}

// تحديث تلقائي كل 5 ثواني (اختياري)
setInterval(function() {
    fetch('chat_refresh.php?id=<?= $chatId ?>&last=<?= count($messages) ?>')
        .then(res => res.json())
        .then(data => {
            if (data.new_messages > 0) {
                location.reload();
            }
        });
}, 5000);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>