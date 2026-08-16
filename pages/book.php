<?php
$pageTitle = 'حجز خدمة';
require_once __DIR__ . '/../includes/init.php';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$serviceId = $_GET['service_id'] ?? 0;

// جلب الخدمة
$stmt = $pdo->prepare("
    SELECT s.*, u.name AS provider_name, u.avatar AS provider_avatar
    FROM services s
    JOIN users u ON s.provider_id = u.id
    WHERE s.id = ?
");
$stmt->execute([$serviceId]);
$service = $stmt->fetch();

if (!$service) {
    echo '<div class="card"><p>الخدمة غير موجودة.</p></div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['booking_date'] ?? '';
    $time = $_POST['booking_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    // التحقق من التاريخ
    if (empty($date) || strtotime($date) < strtotime('today')) {
        $error = 'يرجى اختيار تاريخ صحيح (لا يمكن الحجز في الماضي).';
    } elseif (empty($time)) {
        $error = 'يرجى اختيار وقت.';
    } else {
        // إدخال الحجز
        $stmt = $pdo->prepare("INSERT INTO bookings (service_id, client_id, provider_id, booking_date, booking_time, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$serviceId, $currentUser['id'], $service['provider_id'], $date, $time, $notes]);

        // إشعار لمقدم الخدمة
        $msg = $currentUser['name'] . ' حجز خدمتك "' . $service['title'] . '" بتاريخ ' . $date;
        $pdo->prepare("INSERT INTO notifications (user_id, sender_id, type, message, link) VALUES (?, ?, 'booking_new', ?, ?)")
            ->execute([$service['provider_id'], $currentUser['id'], $msg, 'my-bookings.php']);

        header('Location: my-bookings.php?booked=1');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 600px; margin: 0 auto; padding: 1.5rem 1rem;">
    <a href="service.php?id=<?= $serviceId ?>" style="color: var(--primary); text-decoration: none;">← رجوع للخدمة</a>

    <h2 style="margin: 1rem 0;">📅 حجز: <?= htmlspecialchars($service['title']) ?></h2>
    <p>مقدم الخدمة: <strong><?= htmlspecialchars($service['provider_name']) ?></strong></p>
    <p>السعر: <strong><?= number_format($service['price'], 2) ?> ر.س</strong></p>

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="card">
        <div class="input-group">
            <label>📅 تاريخ الحجز</label>
            <input type="date" name="booking_date" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="input-group">
            <label>⏰ الوقت المناسب</label>
            <select name="booking_time" required>
                <option value="">اختر الوقت</option>
                <?php for ($h = 8; $h <= 20; $h++): ?>
                    <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</option>
                    <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:30"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:30</option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="input-group">
            <label>📝 ملاحظات (اختياري)</label>
            <textarea name="notes" rows="3" placeholder="أي تفاصيل إضافية..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">تأكيد الحجز</button>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>