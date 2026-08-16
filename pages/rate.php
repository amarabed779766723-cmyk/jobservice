<?php
$pageTitle = 'تقييم الخدمة';
require_once __DIR__ . '/../includes/init.php';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$requestId = $_GET['request_id'] ?? 0;

// جلب الطلب
$stmt = $pdo->prepare("SELECT * FROM service_requests WHERE id = ?");
$stmt->execute([$requestId]);
$request = $stmt->fetch();

if (!$request || $request['status'] != 'in_progress') {
    echo '<p>هذا الطلب غير متاح للتقييم.</p>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// تحديد الطرف الآخر
if ($currentUser['id'] == $request['user_id']) {
    // صاحب الطلب يقيّم مقدم الخدمة المقبول
    $offerStmt = $pdo->prepare("SELECT provider_id FROM offers WHERE request_id = ? AND status = 'accepted'");
    $offerStmt->execute([$requestId]);
    $ratedUserId = $offerStmt->fetchColumn();
} else {
    // مقدم الخدمة يقيّم صاحب الطلب
    $ratedUserId = $request['user_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = $_POST['score'] ?? 0;
    $review = trim($_POST['review'] ?? '');

    if ($score < 1 || $score > 5) {
        $error = 'يرجى اختيار تقييم من 1 إلى 5.';
    } else {
        // إدخال التقييم
        $stmt = $pdo->prepare("INSERT INTO ratings (request_id, rater_id, rated_user_id, score, review) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$requestId, $currentUser['id'], $ratedUserId, $score, $review]);

        // إشعار للشخص الذي تم تقييمه
        $msg = $currentUser['name'] . ' قيّمك بـ ' . $score . ' نجوم.';
        $link = 'profile.php?id=' . $ratedUserId;
        $pdo->prepare("INSERT INTO notifications (user_id, sender_id, type, message, link) VALUES (?, ?, 'rating_new', ?, ?)")
            ->execute([$ratedUserId, $currentUser['id'], $msg, $link]);

        // تغيير حالة الطلب إلى مكتمل
        $pdo->prepare("UPDATE service_requests SET status = 'completed' WHERE id = ?")->execute([$requestId]);

        header('Location: request-detail.php?id=' . $requestId . '&rated=1');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 600px; margin: 0 auto; padding: 1.5rem 1rem;">
    <div style="margin-bottom: 1rem;">
        <a href="request-detail.php?id=<?= $requestId ?>" style="text-decoration: none; color: #2563eb;">← رجوع للطلب</a>
    </div>

    <div class="card">
        <h2 style="margin-bottom: 1rem;">⭐ تقييم الخدمة</h2>
        <p>الطلب: <strong><?= htmlspecialchars($request['title']) ?></strong></p>

        <?php if (isset($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>تقييمك (من 1 إلى 5)</label>
                <div style="display: flex; gap: 0.5rem; font-size: 2rem; cursor: pointer;" id="starRating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span data-value="<?= $i ?>" style="color: #d1d5db;">★</span>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="score" id="scoreInput" required>
            </div>
            <div class="input-group">
                <label>مراجعة (اختياري)</label>
                <textarea name="review" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">إرسال التقييم</button>
        </form>
    </div>
</main>

<script>
// تفعيل نجوم التقييم
document.querySelectorAll('#starRating span').forEach(star => {
    star.addEventListener('click', function() {
        const value = this.getAttribute('data-value');
        document.getElementById('scoreInput').value = value;
        document.querySelectorAll('#starRating span').forEach((s, index) => {
            s.style.color = index < value ? '#fbbf24' : '#d1d5db';
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>