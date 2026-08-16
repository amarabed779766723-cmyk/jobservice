<?php
$pageTitle = 'إنشاء طلب خدمة';
require_once __DIR__ . '/../includes/init.php';

// يجب أن يكون مسجل الدخول
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $budget = trim($_POST['budget'] ?? '');

    if (empty($title) || empty($budget)) {
        $error = 'العنوان والميزانية مطلوبان.';
    } elseif (!is_numeric($budget) || $budget <= 0) {
        $error = 'يرجى إدخال ميزانية صحيحة.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO service_requests (user_id, title, description, budget) VALUES (?, ?, ?, ?)");
        $stmt->execute([$currentUser['id'], $title, $description, $budget]);
        $success = 'تم نشر طلبك بنجاح.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 650px; margin: 0 auto; padding: 1.5rem 1rem;">
    <h2 style="font-weight: 700; font-size: 1.3rem; margin-bottom: 1.25rem;">إنشاء طلب خدمة جديد</h2>

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success-msg"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="card">
        <div class="input-group">
            <label>عنوان الطلب</label>
            <input type="text" name="title" placeholder="مثلاً: أريد شعاراً لشركتي" required>
        </div>
        <div class="input-group">
            <label>وصف الخدمة المطلوبة</label>
            <textarea name="description" rows="4" placeholder="اشرح تفاصيل ما تريده..."></textarea>
        </div>
        <div class="input-group">
            <label>الميزانية (ر.س)</label>
            <input type="number" name="budget" step="0.01" min="1" placeholder="500" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">نشر الطلب</button>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>