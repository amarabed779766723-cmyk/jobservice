<?php
$pageTitle = 'إنشاء منشور';
require_once __DIR__ . '/../includes/init.php';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    $imageName = null;
    $videoName = null;
    $fileName = null;
    $fileOriginalName = null;

    // ------ معالجة الصورة ------
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            $error = 'صيغة الصورة غير مدعومة.';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $error = 'حجم الصورة كبير (أقصى حد 5MB).';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imageName = 'img_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/posts/' . $imageName);
        }
    }

    // ------ معالجة الفيديو ------
    if (!$error && isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['video'];
        $allowed = ['video/mp4', 'video/webm', 'video/ogg'];
        if (!in_array($file['type'], $allowed)) {
            $error = 'صيغة الفيديو غير مدعومة (mp4, webm, ogg فقط).';
        } elseif ($file['size'] > 50 * 1024 * 1024) {
            $error = 'حجم الفيديو كبير (أقصى حد 50MB).';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $videoName = 'vid_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/videos/' . $videoName);
        }
    }

    // ------ معالجة الملف ------
    if (!$error && isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['attachment'];
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip',
            'application/x-rar-compressed',
            'text/plain',
            'audio/mpeg',
            'audio/wav'
        ];
        if (!in_array($file['type'], $allowedTypes)) {
            $error = 'نوع الملف غير مدعوم.';
        } elseif ($file['size'] > 20 * 1024 * 1024) {
            $error = 'حجم الملف كبير (أقصى حد 20MB).';
        } else {
            $fileOriginalName = $file['name'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'file_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/files/' . $fileName);
        }
    }

    if (empty($content) && !$imageName && !$videoName && !$fileName) {
        $error = 'يجب كتابة نص أو إرفاق وسيط واحد على الأقل.';
    }

    if (!$error) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, image, video, file_attachment, file_name) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$currentUser['id'], $content, $imageName, $videoName, $fileName, $fileOriginalName]);
        header('Location: home.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 600px; margin: 0 auto; padding: 1.5rem 1rem;">
    <div style="margin-bottom: 1rem;">
        <a href="home.php" style="text-decoration: none; color: var(--primary);">← رجوع للرئيسية</a>
    </div>

    <h2 style="font-weight: 700; margin-bottom: 1rem;">📝 إنشاء منشور</h2>

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success-msg"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
            <img src="../uploads/avatars/<?= htmlspecialchars($currentUser['avatar'] ?? 'default-avatar.png') ?>"
                 style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border);">
            <span style="font-weight: 600; font-size: 1rem;"><?= htmlspecialchars($currentUser['name']) ?></span>
        </div>

        <div class="input-group">
            <textarea name="content" rows="4" placeholder="ما الذي تريد مشاركته؟" style="min-height: 100px;"></textarea>
        </div>

        <!-- صورة وفيديو في صف واحد -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="input-group">
                <label>📷 صورة</label>
                <div class="file-input-wrapper">
                    <input type="file" name="image" accept="image/*">
                </div>
            </div>
            <div class="input-group">
                <label>🎬 فيديو</label>
                <div class="file-input-wrapper">
                    <input type="file" name="video" accept="video/*">
                </div>
            </div>
        </div>

        <!-- ملف مرفق -->
        <div class="input-group">
            <label>📎 ملف (PDF, Word, Excel, Zip, صوت...)</label>
            <div class="file-input-wrapper">
                <input type="file" name="attachment">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; margin-top: 0.5rem;">نشر</button>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>