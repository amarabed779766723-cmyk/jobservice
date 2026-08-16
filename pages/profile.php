<?php
$pageTitle = 'الملف الشخصي';
require_once __DIR__ . '/../includes/init.php';

// إذا لم يُعطى معرف، نعرض ملف المستخدم الحالي
$profileId = $_GET['id'] ?? $currentUser['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$profileId]);
$profileUser = $stmt->fetch();

if (!$profileUser) {
    echo '<div class="card" style="text-align:center; padding:2rem;">المستخدم غير موجود.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// جلب خدمات المستخدم إذا كان مقدم خدمة
$myServices = [];
if ($profileUser['user_type'] === 'provider') {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE provider_id = ? ORDER BY created_at DESC");
    $stmt->execute([$profileUser['id']]);
    $myServices = $stmt->fetchAll();
}

// عدد الخدمات
$servicesCount = count($myServices);

// عدد المتابعين
$followersCount = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE following_id = ?");
$followersCount->execute([$profileUser['id']]);
$followersCount = $followersCount->fetchColumn();

// عدد الذين يتابعهم
$followingCount = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ?");
$followingCount->execute([$profileUser['id']]);
$followingCount = $followingCount->fetchColumn();

// التقييمات
$ratingsStmt = $pdo->prepare("SELECT r.*, u.name AS rater_name FROM ratings r JOIN users u ON r.rater_id = u.id WHERE r.rated_user_id = ? ORDER BY r.created_at DESC LIMIT 5");
$ratingsStmt->execute([$profileUser['id']]);
$ratings = $ratingsStmt->fetchAll();
$avgStmt = $pdo->prepare("SELECT AVG(score) FROM ratings WHERE rated_user_id = ?");
$avgStmt->execute([$profileUser['id']]);
$avgRating = round($avgStmt->fetchColumn(), 1);

// قائمة المتابعين (للزر)
$isFollowing = false;
if ($isLoggedIn && $currentUser['id'] != $profileUser['id']) {
    $checkFollow = $pdo->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
    $checkFollow->execute([$currentUser['id'], $profileUser['id']]);
    $isFollowing = $checkFollow->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 700px; margin: 0 auto; padding: 1.5rem 1rem;">

    <!-- بطاقة المعلومات -->
    <div style="background: white; border-radius: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 1.5rem;">
        <!-- صورة الغلاف -->
        <div style="height: 160px; background: url('../uploads/covers/<?= htmlspecialchars($profileUser['cover'] ?? 'default-cover.jpg') ?>') center/cover no-repeat; background-color: #dbeafe;">
        </div>

        <!-- الصورة الشخصية -->
        <div style="text-align: center; margin-top: -50px;">
            <img src="../uploads/avatars/<?= htmlspecialchars($profileUser['avatar'] ?? 'default-avatar.png') ?>" alt="صورة شخصية" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid white; background: #e5e7eb;">
        </div>

        <div style="padding: 0.5rem 1.5rem 1.5rem 1.5rem; text-align: center;">
            <h2 style="font-size: 1.4rem; font-weight: 700; margin-top: 0.5rem;">
                <?= htmlspecialchars($profileUser['name']) ?>
            </h2>
            <p style="color: #6b7280; margin-top: 0.25rem;">
                <?= htmlspecialchars($profileUser['bio'] ?? 'لم تضف نبذة تعريفية بعد.') ?>
            </p>

            <?php if (!empty($profileUser['city']) || !empty($profileUser['website'])): ?>
            <div style="display: flex; justify-content: center; gap: 1.5rem; margin-top: 0.75rem; color: #4b5563; font-size: 0.9rem;">
                <?php if (!empty($profileUser['city'])): ?>
                    <span>📍 <?= htmlspecialchars($profileUser['city']) ?></span>
                <?php endif; ?>
                <?php if (!empty($profileUser['website'])): ?>
                    <span>🔗 <a href="<?= htmlspecialchars($profileUser['website']) ?>" target="_blank" style="color: #2563eb;">الموقع</a></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <span style="display: inline-block; margin-top: 0.75rem; padding: 0.3rem 1rem; border-radius: 2rem; font-size: 0.8rem; font-weight: 600;
                background: <?= $profileUser['user_type'] === 'provider' ? '#d1fae5' : '#dbeafe' ?>;
                color: <?= $profileUser['user_type'] === 'provider' ? '#065f46' : '#1e40af' ?>;">
                <?= $profileUser['user_type'] === 'provider' ? 'مقدم خدمات' : 'باحث عن خدمات' ?>
            </span>

            <!-- إحصائيات -->
            <div style="display: flex; justify-content: center; gap: 2rem; margin: 1rem 0;">
                <div><strong><?= $servicesCount ?></strong><br><small>خدمات</small></div>
                <div><strong><?= $followersCount ?></strong><br><small>متابع</small></div>
                <div><strong><?= $followingCount ?></strong><br><small>يتابع</small></div>
                <div><strong><?= $avgRating ?: '—' ?></strong><br><small>تقييم</small></div>
            </div>

            <div style="display: flex; justify-content: center; gap: 0.75rem; margin-top: 1.25rem;">
                <?php if ($isLoggedIn && $currentUser['id'] != $profileUser['id']): ?>
                    <a href="follow_action.php?user=<?= $profileUser['id'] ?>" class="btn <?= $isFollowing ? 'btn-outline' : 'btn-primary' ?> btn-sm">
                        <?= $isFollowing ? '✅ تتابعه' : '➕ متابعة' ?>
                    </a>
                    <a href="start_chat.php?with=<?= $profileUser['id'] ?>" class="btn btn-outline btn-sm">💬 مراسلة</a>
                <?php endif; ?>
                <?php if ($currentUser['id'] == $profileUser['id']): ?>
                    <a href="edit-profile.php" class="btn btn-primary btn-sm">تعديل الملف الشخصي</a>
                    <a href="settings.php" class="btn btn-outline btn-sm">الإعدادات</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- خدماتي (لمقدم الخدمة) -->
    <?php if ($profileUser['user_type'] === 'provider'): ?>
    <div style="background: white; border-radius: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); padding: 1.5rem; margin-bottom: 1.5rem;">
        <h3 style="font-weight: 700; font-size: 1.15rem; margin-bottom: 1rem;">🛠️ خدماتي</h3>
        <?php if (count($myServices) === 0): ?>
            <p style="color: #6b7280;">لم تضف أي خدمة بعد.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <?php foreach ($myServices as $service): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f9fafb; border-radius: 0.75rem;">
                        <div>
                            <a href="service.php?id=<?= $service['id'] ?>" style="font-weight: 600; color: #1f2937; text-decoration: none;">
                                <?= htmlspecialchars($service['title']) ?>
                            </a>
                            <div style="color: #6b7280; font-size: 0.8rem;"><?= number_format($service['price'], 2) ?> ر.س</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- آخر التقييمات -->
    <?php if (count($ratings) > 0): ?>
        <div style="background: white; border-radius: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); padding: 1.5rem; margin-bottom: 1.5rem;">
            <h3 style="font-weight: 700; margin-bottom: 1rem;">⭐ آخر التقييمات</h3>
            <?php foreach ($ratings as $rating): ?>
                <div style="padding: 0.5rem 0; border-bottom: 1px solid #f0f2f5;">
                    <strong><?= htmlspecialchars($rating['rater_name']) ?></strong>
                    <span style="color: #fbbf24;"> <?= str_repeat('★', $rating['score']) ?></span>
                    <?php if ($rating['review']): ?>
                        <p style="margin-top: 0.25rem; color: #4b5563; font-size: 0.9rem;"><?= htmlspecialchars($rating['review']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>