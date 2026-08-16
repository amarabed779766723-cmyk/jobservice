<?php
$pageTitle = 'تفاصيل الطلب';
require_once __DIR__ . '/../includes/init.php';

$requestId = $_GET['id'] ?? 0;

// جلب الطلب
$stmt = $pdo->prepare("
    SELECT sr.*, u.name AS requester_name, u.avatar AS requester_avatar
    FROM service_requests sr
    JOIN users u ON sr.user_id = u.id
    WHERE sr.id = ?
");
$stmt->execute([$requestId]);
$request = $stmt->fetch();

if (!$request) {
    echo '<div class="card" style="text-align:center; padding:2rem;">الطلب غير موجود.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// جلب مقدم الخدمة المقبول (إن وجد)
$acceptedProviderId = null;
$acceptedOfferStmt = $pdo->prepare("SELECT provider_id FROM offers WHERE request_id = ? AND status = 'accepted'");
$acceptedOfferStmt->execute([$requestId]);
$acceptedProviderId = $acceptedOfferStmt->fetchColumn();

$offerError = '';
$offerSuccess = '';

// ===========================
// 1. معالجة تقديم عرض جديد
// ===========================
if ($isLoggedIn && $currentUser['user_type'] === 'provider' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_offer'])) {
    $price = trim($_POST['price'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($price) || !is_numeric($price) || $price <= 0) {
        $offerError = 'يرجى إدخال سعر صحيح.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO offers (request_id, provider_id, price, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$requestId, $currentUser['id'], $price, $message]);
        $offerSuccess = 'تم تقديم عرضك بنجاح.';

        // إشعار لصاحب الطلب
        if ($request['user_id'] != $currentUser['id']) {
            $msg = $currentUser['name'] . ' قدم عرضًا على طلبك.';
            $link = 'request-detail.php?id=' . $requestId;
            $pdo->prepare("INSERT INTO notifications (user_id, sender_id, type, message, link) VALUES (?, ?, 'offer_new', ?, ?)")
                ->execute([$request['user_id'], $currentUser['id'], $msg, $link]);
        }
    }
}

// ===========================
// 2. معالجة قبول عرض
// ===========================
if ($isLoggedIn && $currentUser['id'] == $request['user_id'] && isset($_GET['accept_offer'])) {
    $offerId = $_GET['accept_offer'];
    $stmt = $pdo->prepare("SELECT * FROM offers WHERE id = ? AND request_id = ? AND status = 'pending'");
    $stmt->execute([$offerId, $requestId]);
    $offer = $stmt->fetch();

    if ($offer) {
        // قبول هذا العرض
        $pdo->prepare("UPDATE offers SET status = 'accepted' WHERE id = ?")->execute([$offerId]);
        // رفض باقي العروض
        $pdo->prepare("UPDATE offers SET status = 'rejected' WHERE request_id = ? AND id != ? AND status = 'pending'")->execute([$requestId, $offerId]);
        // تغيير حالة الطلب إلى "قيد التنفيذ"
        $pdo->prepare("UPDATE service_requests SET status = 'in_progress' WHERE id = ?")->execute([$requestId]);

        // إشعار لمقدم الخدمة المقبول
        if ($offer['provider_id'] != $currentUser['id']) {
            $msg = $currentUser['name'] . ' قبل عرضك.';
            $link = 'request-detail.php?id=' . $requestId;
            $pdo->prepare("INSERT INTO notifications (user_id, sender_id, type, message, link) VALUES (?, ?, 'offer_accept', ?, ?)")
                ->execute([$offer['provider_id'], $currentUser['id'], $msg, $link]);
        }

        header('Location: request-detail.php?id=' . $requestId);
        exit;
    }
}

// ===========================
// 3. جلب جميع العروض المقدمة
// ===========================
$offersStmt = $pdo->prepare("
    SELECT o.*, u.name AS provider_name, u.avatar AS provider_avatar
    FROM offers o
    JOIN users u ON o.provider_id = u.id
    WHERE o.request_id = ?
    ORDER BY o.created_at DESC
");
$offersStmt->execute([$requestId]);
$offers = $offersStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-container">
    <main>
        <!-- زر الرجوع -->
        <div style="margin-bottom: 1rem;">
            <a href="requests.php" style="text-decoration: none; color: #2563eb;">← رجوع للطلبات</a>
        </div>

        <!-- تفاصيل الطلب -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <img src="../uploads/avatars/<?= htmlspecialchars($request['requester_avatar'] ?? 'default-avatar.png') ?>" class="card-avatar" alt="">
                <div>
                    <div class="card-user"><?= htmlspecialchars($request['requester_name']) ?></div>
                    <div class="card-meta"><?= date('Y/m/d', strtotime($request['created_at'])) ?></div>
                </div>
            </div>
            <h2 class="card-title"><?= htmlspecialchars($request['title']) ?></h2>
            <p class="card-body"><?= nl2br(htmlspecialchars($request['description'] ?? 'لا يوجد وصف.')) ?></p>
            <div class="card-footer">
                <span class="card-price">الميزانية: <?= number_format($request['budget'], 2) ?> ر.س</span>
                <span class="tag" style="background:#e0f2fe; color:#0369a1;">
                    الحالة: <?= $request['status'] == 'open' ? 'مفتوح' : ($request['status'] == 'in_progress' ? 'قيد التنفيذ' : 'مكتمل') ?>
                </span>
            </div>
        </div>

        <!-- زر إتمام الخدمة والتقييم (يظهر فقط عند قيد التنفيذ) -->
        <?php if ($isLoggedIn && $request['status'] == 'in_progress'): ?>
            <?php if ($currentUser['id'] == $request['user_id'] || $currentUser['id'] == $acceptedProviderId): ?>
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <a href="rate.php?request_id=<?= $requestId ?>" class="btn btn-success" style="padding: 0.75rem 2rem; font-size: 1rem;">
                        ⭐ إتمام الخدمة والتقييم
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- نموذج تقديم عرض (لمقدم الخدمة فقط إذا كان الطلب مفتوحاً) -->
        <?php if ($isLoggedIn && $currentUser['user_type'] === 'provider' && $request['status'] === 'open' && $currentUser['id'] != $request['user_id']): ?>
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3>📝 قدّم عرضك</h3>
                <?php if ($offerError): ?>
                    <div class="error-msg"><?= htmlspecialchars($offerError) ?></div>
                <?php endif; ?>
                <?php if ($offerSuccess): ?>
                    <div class="success-msg"><?= htmlspecialchars($offerSuccess) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="submit_offer" value="1">
                    <div class="input-group">
                        <label>سعر العرض (ر.س)</label>
                        <input type="number" name="price" step="0.01" min="1" required>
                    </div>
                    <div class="input-group">
                        <label>رسالة تعريفية (اختياري)</label>
                        <textarea name="message" rows="3" placeholder="عرّف بنفسك وبخبرتك..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">تقديم العرض</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- قائمة العروض المقدمة -->
        <h3 style="margin-bottom: 1rem;">📊 العروض المقدمة (<?= count($offers) ?>)</h3>
        <?php if (count($offers) === 0): ?>
            <div class="card"><p style="color:#65676b;">لا توجد عروض بعد. كن أول من يقدم عرضاً!</p></div>
        <?php endif; ?>

        <?php foreach ($offers as $offer): ?>
        <div class="card" style="margin-bottom: 0.75rem; <?= $offer['status'] == 'accepted' ? 'border: 2px solid #10b981;' : '' ?>">
            <div class="card-header">
                <img src="../uploads/avatars/<?= htmlspecialchars($offer['provider_avatar'] ?? 'default-avatar.png') ?>" class="card-avatar" alt="">
                <div>
                    <div class="card-user"><?= htmlspecialchars($offer['provider_name']) ?></div>
                    <div class="card-meta"><?= date('Y/m/d', strtotime($offer['created_at'])) ?></div>
                </div>
            </div>
            <p><strong>السعر:</strong> <?= number_format($offer['price'], 2) ?> ر.س</p>
            <?php if ($offer['message']): ?>
                <p style="color:#4b5563;"><?= htmlspecialchars($offer['message']) ?></p>
            <?php endif; ?>
            <div style="margin-top: 0.5rem;">
                <?php if ($offer['status'] == 'pending' && $isLoggedIn && $currentUser['id'] == $request['user_id'] && $request['status'] == 'open'): ?>
                    <a href="?id=<?= $requestId ?>&accept_offer=<?= $offer['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟')">✅ قبول العرض</a>
                <?php elseif ($offer['status'] == 'accepted'): ?>
                    <span class="tag" style="background:#d1fae5; color:#065f46;">🎉 تم قبوله</span>
                <?php elseif ($offer['status'] == 'rejected'): ?>
                    <span class="tag" style="background:#fee2e2; color:#991b1b;">❌ مرفوض</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </main>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>