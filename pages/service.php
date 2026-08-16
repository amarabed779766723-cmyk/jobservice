<?php
$pageTitle = 'تفاصيل الخدمة';
require_once __DIR__ . '/../includes/init.php';

$serviceId = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT s.*, u.name AS provider_name, u.avatar AS provider_avatar
    FROM services s
    JOIN users u ON s.provider_id = u.id
    WHERE s.id = ?
");
$stmt->execute([$serviceId]);
$service = $stmt->fetch();

if (!$service) {
    echo '<div class="card" style="text-align:center; padding:2rem;">الخدمة غير موجودة.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-container">
    <main>
        <div style="margin-bottom: 1rem;">
            <a href="home.php" style="text-decoration: none; color: var(--primary);">← رجوع للرئيسية</a>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <!-- صورة الخدمة -->
            <div style="background: var(--bg); height: 220px; border-radius: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                🛠️
            </div>

            <h2 style="font-size: 1.4rem; font-weight: 700;"><?= htmlspecialchars($service['title']) ?></h2>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin: 0.75rem 0;">
                <img src="../uploads/avatars/<?= htmlspecialchars($service['provider_avatar'] ?? 'default-avatar.png') ?>" style="width: 32px; height: 32px; border-radius: 50%;">
                <span style="font-weight: 500;"><?= htmlspecialchars($service['provider_name']) ?></span>
                <!-- زر المراسلة -->
                <?php if ($isLoggedIn && $currentUser['id'] != $service['provider_id']): ?>
                    <a href="start_chat.php?with=<?= $service['provider_id'] ?>" class="btn btn-outline btn-sm" style="margin-right: auto;">💬 مراسلة</a>
                <?php endif; ?>
            </div>
            <p style="color: var(--text-secondary); line-height: 1.7; margin: 1rem 0;"><?= nl2br(htmlspecialchars($service['description'] ?? 'لا يوجد وصف.')) ?></p>

            <div style="display: flex; gap: 2rem; background: var(--bg); padding: 1rem; border-radius: 0.75rem; margin: 1.5rem 0;">
                <div>
                    <span style="color: var(--text-secondary);">السعر</span><br>
                    <span style="font-weight: 700; color: var(--primary);"><?= number_format($service['price'], 2) ?> ر.س</span>
                </div>
                <div>
                    <span style="color: var(--text-secondary);">المدة</span><br>
                    <span style="font-weight: 700;"><?= htmlspecialchars($service['duration'] ?? 'غير محدد') ?></span>
                </div>
            </div>

            <?php if ($isLoggedIn): ?>
    <?php if ($currentUser['id'] != $service['provider_id']): ?>
        <a href="book.php?service_id=<?= $service['id'] ?>" class="btn btn-primary" style="padding: 0.75rem 2rem;">📅 احجز الخدمة</a>
    <?php else: ?>
        <p style="color: var(--text-secondary);">هذه خدمتك أنت.</p>
    <?php endif; ?>
<?php else: ?>
    <a href="login.php" class="btn btn-outline" style="padding: 0.75rem 2rem;">سجل دخول للحجز</a>
<?php endif; ?>
        </div>
        <!-- تقييمات الخدمة -->
<?php
// جلب التقييمات لمقدم الخدمة هذا
$reviewsStmt = $pdo->prepare("
    SELECT r.*, u.name AS reviewer_name, u.avatar AS reviewer_avatar
    FROM ratings r
    JOIN users u ON r.rater_id = u.id
    WHERE r.rated_user_id = ?
    ORDER BY r.created_at DESC
    LIMIT 5
");
$reviewsStmt->execute([$service['provider_id']]);
$reviews = $reviewsStmt->fetchAll();

// متوسط التقييم
$avgStmt = $pdo->prepare("SELECT AVG(score) FROM ratings WHERE rated_user_id = ?");
$avgStmt->execute([$service['provider_id']]);
$avgRating = round($avgStmt->fetchColumn(), 1);
?>

<?php if (count($reviews) > 0): ?>
    <div style="margin-top: 2rem; padding: 1.5rem; background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow);">
        <h3 style="font-weight: 700; margin-bottom: 1rem;">
            ⭐ التقييمات (<?= $avgRating ?: '—' ?>)
        </h3>
        <?php foreach ($reviews as $review): ?>
            <div style="padding: 1rem 0; border-bottom: 1px solid var(--border);">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <img src="../uploads/avatars/<?= htmlspecialchars($review['reviewer_avatar'] ?? 'default-avatar.png') ?>" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <strong><?= htmlspecialchars($review['reviewer_name']) ?></strong>
                        <div style="color: #fbbf24;"><?= str_repeat('★', $review['score']) ?><?= str_repeat('☆', 5 - $review['score']) ?></div>
                    </div>
                    <span style="margin-right: auto; font-size: 0.8rem; color: var(--text-secondary);"><?= date('Y/m/d', strtotime($review['created_at'])) ?></span>
                </div>
                <?php if ($review['review']): ?>
                    <p style="color: var(--text-secondary); margin-right: 3.25rem; line-height: 1.6;"><?= htmlspecialchars($review['review']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>