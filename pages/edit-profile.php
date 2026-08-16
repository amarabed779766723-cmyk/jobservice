<?php
$pageTitle = 'تعديل الملف الشخصي';
require_once __DIR__ . '/../includes/init.php';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $bio     = trim($_POST['bio'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $city    = trim($_POST['city'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $lat     = $_POST['latitude'] ?? null;
    $lng     = $_POST['longitude'] ?? null;
    $address = trim($_POST['address'] ?? '');

    if (empty($name)) {
        $error = 'الاسم مطلوب.';
    } else {
        // --- معالجة الصورة الشخصية (Avatar) ---
        $avatarPath = $currentUser['avatar'] ?? 'default-avatar.png';
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                $error = 'صيغة الصورة غير مدعومة. استخدم JPG أو PNG أو GIF.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = 'حجم الصورة كبير جداً. أقصى حد مسموح 2 ميجابايت.';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFileName = 'avatar_' . $currentUser['id'] . '_' . time() . '.' . $ext;
                $destination = __DIR__ . '/../uploads/avatars/' . $newFileName;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    if ($avatarPath && $avatarPath !== 'default-avatar.png' && file_exists(__DIR__ . '/../uploads/avatars/' . $avatarPath)) {
                        unlink(__DIR__ . '/../uploads/avatars/' . $avatarPath);
                    }
                    $avatarPath = $newFileName;
                } else {
                    $error = 'فشل رفع الصورة. تأكد من صلاحيات المجلد.';
                }
            }
        }

        // --- معالجة صورة الغلاف (Cover) ---
        $coverPath = $currentUser['cover'] ?? 'default-cover.jpg';
        if (!$error && isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['cover'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                $error = 'صيغة صورة الغلاف غير مدعومة.';
            } elseif ($file['size'] > 3 * 1024 * 1024) {
                $error = 'حجم صورة الغلاف كبير جداً.';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFileName = 'cover_' . $currentUser['id'] . '_' . time() . '.' . $ext;
                $destination = __DIR__ . '/../uploads/covers/' . $newFileName;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    if ($coverPath && $coverPath !== 'default-cover.jpg' && file_exists(__DIR__ . '/../uploads/covers/' . $coverPath)) {
                        unlink(__DIR__ . '/../uploads/covers/' . $coverPath);
                    }
                    $coverPath = $newFileName;
                }
            }
        }

        // --- تحديث قاعدة البيانات ---
        if (!$error) {
            $stmt = $pdo->prepare("UPDATE users SET name=?, bio=?, phone=?, city=?, website=?, avatar=?, cover=?, latitude=?, longitude=?, address=? WHERE id=?");
            $stmt->execute([$name, $bio, $phone, $city, $website, $avatarPath, $coverPath, $lat, $lng, $address, $currentUser['id']]);

            // إعادة قراءة بيانات المستخدم بعد التحديث
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$currentUser['id']]);
            $currentUser = $stmt->fetch();

            $_SESSION['user_name'] = $name;

            $success = 'تم تحديث الملف الشخصي بنجاح!';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main style="max-width: 650px; margin: 0 auto; padding: 1.5rem 1rem;">
    <h2 style="font-weight: 700; font-size: 1.3rem; margin-bottom: 1.25rem;">تعديل الملف الشخصي</h2>

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success-msg"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card">
        <!-- معاينة الصورة الشخصية -->
        <div class="input-group" style="text-align: center;">
            <label>الصورة الشخصية</label>
            <img src="../uploads/avatars/<?= htmlspecialchars($currentUser['avatar'] ?? 'default-avatar.png') ?>" alt="صورتي" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #dbeafe; display: block; margin: 0 auto 0.5rem;">
            <input type="file" name="avatar" accept="image/*">
        </div>

        <!-- معاينة صورة الغلاف -->
        <div class="input-group">
            <label>صورة الغلاف</label>
            <div style="background: url('../uploads/covers/<?= htmlspecialchars($currentUser['cover'] ?? 'default-cover.jpg') ?>') center/cover no-repeat; height: 120px; border-radius: 8px; margin-bottom: 0.5rem; background-color: #e5e7eb;"></div>
            <input type="file" name="cover" accept="image/*">
        </div>

        <!-- الحقول النصية -->
        <div class="input-group">
            <label>الاسم الكامل</label>
            <input type="text" name="name" value="<?= htmlspecialchars($currentUser['name']) ?>" required>
        </div>
        <div class="input-group">
            <label>النبذة التعريفية</label>
            <textarea name="bio" rows="3"><?= htmlspecialchars($currentUser['bio'] ?? '') ?></textarea>
        </div>
        <div class="input-group">
            <label>رقم الجوال</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>">
        </div>
        <div class="input-group">
            <label>المدينة</label>
            <input type="text" name="city" value="<?= htmlspecialchars($currentUser['city'] ?? '') ?>" placeholder="الرياض">
        </div>
        <div class="input-group">
            <label>الموقع الإلكتروني</label>
            <input type="url" name="website" value="<?= htmlspecialchars($currentUser['website'] ?? '') ?>" placeholder="https://example.com">
        </div>

        <!-- تحديد الموقع الجغرافي -->
        <div class="input-group">
            <label>📍 موقعك</label>
            <input type="text" id="address" name="address" placeholder="اليمن" value="<?= htmlspecialchars($currentUser['address'] ?? '') ?>">
            <input type="hidden" name="latitude" id="lat" value="<?= htmlspecialchars($currentUser['latitude'] ?? '') ?>">
            <input type="hidden" name="longitude" id="lng" value="<?= htmlspecialchars($currentUser['longitude'] ?? '') ?>">
            <button type="button" onclick="getLocation()" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: 0.5rem; cursor: pointer;">
                📍 حدد موقعي تلقائياً
            </button>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">حفظ التغييرات</button>
    </form>
</main>

<!-- سكريبت تحديد الموقع (مرة واحدة فقط) -->
<script>
function getLocation() {
    if (!navigator.geolocation) {
        alert('متصفحك لا يدعم تحديد الموقع.');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        function(position) {
            document.getElementById('lat').value = position.coords.latitude;
            document.getElementById('lng').value = position.coords.longitude;
            document.getElementById('address').value = 'تم تحديد الموقع';
            alert('تم تحديد موقعك بنجاح!');
        },
        function(error) {
            alert('لم نتمكن من تحديد موقعك. تأكد من صلاحيات المتصفح للموقع.');
        }
    );
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>