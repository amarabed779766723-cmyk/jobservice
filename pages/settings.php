<?php
$pageTitle = 'الإعدادات';
require_once __DIR__ . '/../includes/init.php';

if (!$isLoggedIn) { header('Location: login.php'); exit; }

// فك الإعدادات
$userSettings = json_decode($currentUser['settings'] ?? '{}', true) ?: [];
$darkMode = $userSettings['dark_mode'] ?? false;
$notifSound = $userSettings['notification_sound'] ?? true;
$privacy = $userSettings['privacy'] ?? 'public';

$success = '';
$error = '';

// --------------- معالجة AJAX للحفظ الفوري ---------------
if (isset($_POST['action']) && $_POST['action'] === 'ajax_preference') {
    $key = $_POST['key'] ?? '';
    $value = $_POST['value'] ?? '';

    // نقرأ الإعدادات الحالية من القاعدة
    $stmt = $pdo->prepare("SELECT settings FROM users WHERE id = ?");
    $stmt->execute([$currentUser['id']]);
    $row = $stmt->fetch();
    $currentSettings = json_decode($row['settings'] ?? '{}', true) ?: [];

    // نحدث القيمة
    if ($key === 'dark_mode' || $key === 'notification_sound') {
        $currentSettings[$key] = ($value === 'true' || $value === '1');
    } else {
        $currentSettings[$key] = $value;
    }

    $newJson = json_encode($currentSettings, JSON_UNESCAPED_UNICODE);
    $stmt = $pdo->prepare("UPDATE users SET settings = ? WHERE id = ?");
    $stmt->execute([$newJson, $currentUser['id']]);

    echo json_encode(['success' => true]);
    exit;
}

// --------------- معالجة النموذج التقليدي (كلمة المرور، حذف الحساب) ---------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'password') {
        $currentPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPass, $currentUser['password'])) {
            $error = 'كلمة المرور الحالية غير صحيحة.';
        } elseif (strlen($newPass) < 8) {
            $error = 'كلمة المرور الجديدة قصيرة.';
        } elseif ($newPass !== $confirmPass) {
            $error = 'كلمتا المرور غير متطابقتين.';
        } else {
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $currentUser['id']]);
            $success = 'تم تغيير كلمة المرور بنجاح!';
        }
    }

    if ($_POST['action'] === 'delete') {
        $confirm = $_POST['confirm_delete'] ?? '';
        if ($confirm === 'نعم احذف حسابي') {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$currentUser['id']]);
            session_destroy();
            header('Location: login.php');
            exit;
        } else {
            $error = 'اكتب عبارة التأكيد بشكل صحيح.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-container">
    <main>
        <h2 style="font-weight: 700; font-size: 1.3rem; margin-bottom: 1.5rem;">الإعدادات</h2>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-msg"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- تبويب كلمة المرور -->
        <div class="card">
            <h3>🔒 تغيير كلمة المرور</h3>
            <form method="POST">
                <input type="hidden" name="action" value="password">
                <div class="input-group">
                    <label>كلمة المرور الحالية</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="input-group">
                    <label>كلمة المرور الجديدة</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="input-group">
                    <label>تأكيد كلمة المرور الجديدة</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
            </form>
        </div>

        <!-- تبويب المظهر والإشعارات -->
      <div class="card" style="margin-top: 1rem;">
    <h3>🎨 المظهر والإشعارات</h3>
    <div style="display: flex; justify-content: space-between; align-items: center; margin: 1rem 0;">
        <span>🌙 الوضع الليلي</span>
        <input type="checkbox" class="switch" id="darkModeToggle" <?= $darkMode ? 'checked' : '' ?> onchange="toggleDarkMode(this)">
    </div>
    <!-- باقي الإعدادات -->
        </div>

            <!-- صوت الإشعارات -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin: 1rem 0;">
                <span>🔔 صوت الإشعارات</span>
                <label style="position: relative; display: inline-block; width: 48px; height: 24px;">
                    <input type="checkbox" id="soundToggle" <?= $notifSound ? 'checked' : '' ?> style="opacity:0; width:0; height:0;">
                    <span style="position: absolute; cursor: pointer; top:0; left:0; right:0; bottom:0; background: <?= $notifSound ? '#2563eb' : '#d1d5db' ?>; border-radius:24px; transition:0.3s;"></span>
                </label>
            </div>

            <!-- الخصوصية -->
            <div class="input-group" style="margin-top: 1rem;">
                <label>🔐 من يمكنه رؤية ملفي الشخصي؟</label>
                <select id="privacySelect" style="width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:0.75rem;">
                    <option value="public" <?= $privacy === 'public' ? 'selected' : '' ?>>الجميع</option>
                    <option value="friends" <?= $privacy === 'friends' ? 'selected' : '' ?>>المستخدمون المسجلون فقط</option>
                    <option value="private" <?= $privacy === 'private' ? 'selected' : '' ?>>أنا فقط</option>
                </select>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 1rem;">يتم حفظ الإعدادات تلقائياً.</p>
        </div>

        <!-- تبويب حذف الحساب -->
        <div class="card" style="margin-top: 1rem; border: 1px solid #fecaca;">
            <h3 style="color: #dc2626;">⚠️ حذف الحساب نهائياً</h3>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">هذا الإجراء لا يمكن التراجع عنه.</p>
            <form method="POST" onsubmit="return confirm('آخر تحذير: هل أنت متأكد تماماً؟');">
                <input type="hidden" name="action" value="delete">
                <div class="input-group">
                    <label>اكتب: <strong>نعم احذف حسابي</strong></label>
                    <input type="text" name="confirm_delete" required>
                </div>
                <button type="submit" class="btn btn-danger">حذف الحساب</button>
            </form>
        </div>

    </main>

    <aside class="sidebar">
        <div class="sidebar-card">
            <div class="sidebar-title">⚙️ الإعدادات السريعة</div>
            <a href="edit-profile.php" class="sidebar-item">👤 تعديل الملف الشخصي</a>
            <a href="settings.php" class="sidebar-item">⚙️ جميع الإعدادات</a>
        </div>
    </aside>
</div>

<!-- تحميل سكربت الإعدادات -->
<script src="../assets/js/settings.js"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>