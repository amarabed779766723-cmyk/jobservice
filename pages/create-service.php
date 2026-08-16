<?php
$pageTitle = 'إنشاء خدمة';
require_once __DIR__ . '/../includes/init.php';

// يجب أن يكون مسجل الدخول
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

// الصلاحية: فقط مقدم الخدمة
if ($currentUser['user_type'] !== 'provider') {
    header('Location: home.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $duration = trim($_POST['duration'] ?? '');

    // تحقق بسيط
    if (empty($title) || empty($price)) {
        $error = 'العنوان والسعر مطلوبان.';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'يرجى إدخال سعر صحيح.';
    } else {
        // معالجة الصورة (اختياري)
        $imageName = 'service-default.png'; // صورة افتراضية
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($file['type'], $allowed)) {
                $error = 'صيغة الصورة غير مدعومة.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = 'حجم الصورة كبير (أقصى حد 2MB).';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $imageName = 'service_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $dest = __DIR__ . '/../uploads/services/' . $imageName;
                if (!move_uploaded_file($file['tmp_name'], $dest)) {
                    $error = 'فشل رفع الصورة.';
                }
            }
        }

        // حفظ في القاعدة إذا لم يكن هناك خطأ
        if (!$error) {
            $stmt = $pdo->prepare("INSERT INTO services (provider_id, title, description, price, duration, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$currentUser['id'], $title, $description, $price, $duration, $imageName]);
            $success = 'تم نشر الخدمة بنجاح.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 650px; margin: 0 auto; padding: 1.5rem 1rem;">
    <!-- زر الرجوع -->
    <div style="margin-bottom: 1rem;">
        <a href="home.php" style="text-decoration: none; color: #2563eb;">← رجوع للرئيسية</a>
    </div>

    <h2 style="font-weight: 700; font-size: 1.3rem; margin-bottom: 1.25rem;">إنشاء خدمة جديدة</h2>

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success-msg"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card">
        <div class="input-group">
            <label>عنوان الخدمة</label>
            <input type="text" name="title" placeholder="مثلاً: تصميم شعار احترافي" required>
        </div>
        <div class="input-group">
            <label>وصف الخدمة</label>
            <textarea name="description" rows="4" placeholder="اشرح تفاصيل خدمتك..."></textarea>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="input-group">
                <label>السعر (ر.س)</label>
                <input type="number" name="price" step="0.01" min="1" required>
            </div>
            <div class="input-group">
                <label>المدة (مثلاً: 3 أيام)</label>
                <input type="text" name="duration" placeholder="يومين">
            </div>
        </div>
        <div class="input-group">
            <label>صورة الخدمة (اختياري)</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">نشر الخدمة</button>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>