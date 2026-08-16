<?php
$pageTitle = 'حجوزاتي';
require_once __DIR__ . '/../includes/init.php';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

// معالجة إلغاء الحجز
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $bookingId = $_GET['cancel'];
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND client_id = ?");
    $stmt->execute([$bookingId, $currentUser['id']]);
    header('Location: my-bookings.php');
    exit;
}

// جلب حجوزاتي (كعميل)
$myBookings = $pdo->prepare("
    SELECT b.*, s.title AS service_title, u.name AS provider_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u ON b.provider_id = u.id
    WHERE b.client_id = ?
    ORDER BY b.booking_date DESC, b.booking_time DESC
");
$myBookings->execute([$currentUser['id']]);
$myBookings = $myBookings->fetchAll();

// جلب الحجوزات عندي (كمقدم خدمة)
$providerBookings = [];
if ($currentUser['user_type'] === 'provider') {
    $stmt = $pdo->prepare("
        SELECT b.*, s.title AS service_title, u.name AS client_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users u ON b.client_id = u.id
        WHERE b.provider_id = ?
        ORDER BY b.booking_date DESC, b.booking_time DESC
    ");
    $stmt->execute([$currentUser['id']]);
    $providerBookings = $stmt->fetchAll();
}

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 700px; margin: 0 auto; padding: 1.5rem 1rem;">

    <?php if (isset($_GET['booked'])): ?>
        <div class="success-msg">✅ تم الحجز بنجاح! سيتم إشعار مقدم الخدمة.</div>
    <?php endif; ?>

    <!-- حجوزاتي كعميل -->
    <h2>📅 حجوزاتي</h2>
    <?php if (count($myBookings) === 0): ?>
        <div class="card"><p>ليس لديك أي حجوزات بعد.</p></div>
    <?php endif; ?>
    <?php foreach ($myBookings as $b): ?>
        <div class="card" style="margin-bottom: 0.75rem;">
            <strong><?= htmlspecialchars($b['service_title']) ?></strong>
            <p>مع: <?= htmlspecialchars($b['provider_name']) ?></p>
            <p>📅 <?= $b['booking_date'] ?> ⏰ <?= substr($b['booking_time'], 0, 5) ?></p>
            <p>الحالة: <?= $b['status'] === 'pending' ? '⏳ قيد الانتظار' : ($b['status'] === 'confirmed' ? '✅ مؤكد' : ($b['status'] === 'completed' ? '✔️ مكتمل' : '❌ ملغي')) ?></p>
            <?php if ($b['status'] === 'pending'): ?>
                <a href="?cancel=<?= $b['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('إلغاء هذا الحجز؟')">إلغاء</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <!-- الحجوزات عندي كمقدم خدمة -->
    <?php if ($currentUser['user_type'] === 'provider'): ?>
        <h2 style="margin-top: 2rem;">📋 الحجوزات عندي</h2>
        <?php if (count($providerBookings) === 0): ?>
            <div class="card"><p>لا توجد حجوزات عندك بعد.</p></div>
        <?php endif; ?>
        <?php foreach ($providerBookings as $b): ?>
            <div class="card" style="margin-bottom: 0.75rem;">
                <strong><?= htmlspecialchars($b['service_title']) ?></strong>
                <p>العميل: <?= htmlspecialchars($b['client_name']) ?></p>
                <p>📅 <?= $b['booking_date'] ?> ⏰ <?= substr($b['booking_time'], 0, 5) ?></p>
                <p>الحالة: <?= $b['status'] === 'pending' ? '⏳ قيد الانتظار' : ($b['status'] === 'confirmed' ? '✅ مؤكد' : ($b['status'] === 'completed' ? '✔️ مكتمل' : '❌ ملغي')) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>