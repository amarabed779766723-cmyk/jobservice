<?php
$pageTitle = 'تفاصيل المنشور';
require_once __DIR__ . '/../includes/init.php';

$postId = $_GET['id'] ?? 0;

// جلب المنشور
$stmt = $pdo->prepare("
    SELECT p.*, u.name AS user_name, u.avatar AS user_avatar,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS likes_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    echo '<p>المنشور غير موجود.</p>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// معالجة إضافة تعليق
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    $commentText = trim($_POST['comment_text']);
    if (!empty($commentText)) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$currentUser['id'], $postId, $commentText]);
        header('Location: post-detail.php?id=' . $postId);
        exit;
    }
}

// جلب التعليقات
$commentsStmt = $pdo->prepare("
    SELECT c.*, u.name AS user_name, u.avatar AS user_avatar
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.post_id = ?
    ORDER BY c.created_at ASC
");
$commentsStmt->execute([$postId]);
$comments = $commentsStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 650px; margin: 0 auto; padding: 1.5rem 1rem;">
    <a href="home.php" style="color: #2563eb; text-decoration: none;">← رجوع</a>

    <!-- المنشور -->
    <div class="card" style="margin-top: 1rem;">
        <div class="card-header">
            <img src="../uploads/avatars/<?= htmlspecialchars($post['user_avatar'] ?? 'default-avatar.png') ?>" class="card-avatar" alt="">
            <div>
                <div class="card-user"><?= htmlspecialchars($post['user_name']) ?></div>
                <div class="card-meta"><?= date('Y/m/d H:i', strtotime($post['created_at'])) ?></div>
            </div>
        </div>
        <?php if ($post['content']): ?>
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        <?php endif; ?>
        <?php if ($post['image']): ?>
            <img src="../uploads/posts/<?= htmlspecialchars($post['image']) ?>" style="width:100%; border-radius:8px; max-height:400px; object-fit:cover;">
        <?php endif; ?>
    </div>

    <!-- التعليقات -->
    <h3 style="margin-top: 1.5rem;">💬 التعليقات (<?= count($comments) ?>)</h3>
    <?php foreach ($comments as $comment): ?>
        <div style="display: flex; gap: 0.75rem; margin-bottom: 1rem; background: white; padding: 0.75rem; border-radius: 12px;">
            <img src="../uploads/avatars/<?= htmlspecialchars($comment['user_avatar'] ?? 'default-avatar.png') ?>" style="width:36px;height:36px;border-radius:50%;">
            <div>
                <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                <p style="margin: 0.25rem 0 0;"><?= htmlspecialchars($comment['comment_text']) ?></p>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- إضافة تعليق -->
    <?php if ($isLoggedIn): ?>
        <form method="POST" style="display: flex; gap: 0.5rem; margin-top: 1rem;">
            <input type="text" name="comment_text" placeholder="اكتب تعليقاً..." required style="flex:1; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 2rem;">
            <button type="submit" class="btn btn-primary">إرسال</button>
        </form>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>