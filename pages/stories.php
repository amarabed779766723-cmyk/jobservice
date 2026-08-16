<?php
$pageTitle = 'القصص';
require_once __DIR__ . '/../includes/init.php';
if (!$isLoggedIn) { header('Location: login.php'); exit; }

// حذف القصص المنتهية
$pdo->query("DELETE FROM stories WHERE expires_at < NOW()");

// جلب قصص من أتابعهم + قصصي
$stories = $pdo->prepare("
    SELECT s.*, u.name, u.avatar
    FROM stories s
    JOIN users u ON s.user_id = u.id
    WHERE s.user_id IN (SELECT following_id FROM follows WHERE follower_id = ?)
       OR s.user_id = ?
    ORDER BY s.created_at DESC
");
$stories->execute([$currentUser['id'], $currentUser['id']]);
$stories = $stories->fetchAll();

// رفع قصة جديدة
if (isset($_FILES['story']) && $_FILES['story']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['story'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (in_array($file['type'], $allowed) && $file['size'] < 5 * 1024 * 1024) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageName = 'story_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/stories/' . $imageName);
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $pdo->prepare("INSERT INTO stories (user_id, image, caption, expires_at) VALUES (?, ?, ?, ?)")
            ->execute([$currentUser['id'], $imageName, $_POST['caption'] ?? '', $expires]);
        header('Location: stories.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 700px; margin: 0 auto; padding: 1.5rem 1rem;">
    <h2>📖 القصص</h2>

    <!-- إضافة قصة -->
    <div class="card" style="margin-bottom: 1rem;">
        <h3>➕ أضف قصة</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>صورة القصة</label>
                <input type="file" name="story" accept="image/*" required>
            </div>
            <div class="input-group">
                <label>تعليق (اختياري)</label>
                <input type="text" name="caption" placeholder="اكتب شيئاً...">
            </div>
            <button type="submit" class="btn btn-primary">نشر القصة (تختفي بعد 24 ساعة)</button>
        </form>
    </div>

    <!-- عرض القصص -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
        <?php foreach ($stories as $story): ?>
            <a href="../uploads/stories/<?= htmlspecialchars($story['image']) ?>" target="_blank" style="text-decoration: none;">
                <div style="position: relative; border-radius: 12px; overflow: hidden; height: 200px; background: #000;">
                    <img src="../uploads/stories/<?= htmlspecialchars($story['image']) ?>" style="width:100%; height:100%; object-fit:cover;">
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 0.5rem; background: linear-gradient(transparent, rgba(0,0,0,0.7)); color: white;">
                        <strong><?= htmlspecialchars($story['name']) ?></strong>
                        <?php if ($story['caption']): ?>
                            <p style="font-size:0.8rem;"><?= htmlspecialchars($story['caption']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>