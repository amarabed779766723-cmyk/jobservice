<?php
$pageTitle = 'طلبات الخدمات';
require_once __DIR__ . '/../includes/init.php';

// جلب الطلبات المفتوحة مع اسم صاحبها
$stmt = $pdo->prepare("
    SELECT sr.*, u.name AS requester_name, u.avatar AS requester_avatar
    FROM service_requests sr
    JOIN users u ON sr.user_id = u.id
    WHERE sr.status = 'open'
    ORDER BY sr.created_at DESC
");
$stmt->execute();
$requests = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-container">
    <main>
        <h2 style="margin-bottom: 1rem;">📋 طلبات الخدمة المفتوحة</h2>

        <?php if (count($requests) === 0): ?>
            <div class="card"><p style="text-align:center;color:#65676b;">لا توجد طلبات حالياً. كن أول من يطلب!</p></div>
        <?php endif; ?>

        <?php foreach ($requests as $req): ?>
        <div class="card">
            <div class="card-header">
                <img src="../uploads/avatars/<?= htmlspecialchars($req['requester_avatar'] ?? 'default-avatar.png') ?>"
                     class="card-avatar" alt="">
                <div>
                    <div class="card-user"><?= htmlspecialchars($req['requester_name']) ?></div>
                    <div class="card-meta"><?= date('Y/m/d', strtotime($req['created_at'])) ?></div>
                </div>
            </div>
            <a href="request-detail.php?id=<?= $req['id'] ?>" style="text-decoration:none;color:inherit;">
                <h3 class="card-title"><?= htmlspecialchars($req['title']) ?></h3>
            </a>
            <p class="card-body"><?= htmlspecialchars(mb_strimwidth($req['description'] ?? 'بدون وصف', 0, 150, '...')) ?></p>
            <div class="card-footer">
                <span class="card-price"><?= number_format($req['budget'], 2) ?> ر.س</span>
                <a href="request-detail.php?id=<?= $req['id'] ?>" class="btn btn-primary">تقديم عرض</a>
            </div>
        </div>
        <?php endforeach; ?>
    </main>

    <aside class="sidebar">
        <?php if ($isLoggedIn): ?>
            <div class="sidebar-card">
                <div class="sidebar-title">⚡ إجراءات</div>
                <a href="create-request.php" class="sidebar-item">➕ أنشئ طلباً جديداً</a>
            </div>
        <?php endif; ?>
    </aside>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>