<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $userType = $_POST['user_type'] ?? 'client';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'يرجى ملء الحقول المطلوبة.';
    } elseif ($password !== $password_confirm) {
        $error = 'كلمتا المرور غير متطابقتين.';
    } elseif (strlen($password) < 8) {
        $error = 'كلمة المرور قصيرة جداً.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'البريد الإلكتروني مستخدم بالفعل.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, user_type) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed, $phone, $userType]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            header('Location: home.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء حساب - Job Service</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-box">
        <div class="brand-logo">Job Service</div>
        <div class="brand-slogan">أنشئ حسابك المهني</div>
        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <label>الاسم الكامل</label>
                <input type="text" name="name" required>
            </div>
            <div class="input-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>رقم الجوال (اختياري)</label>
                <input type="tel" name="phone">
            </div>
            <div class="input-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" required>
            </div>
            <div class="input-group">
                <label>تأكيد كلمة المرور</label>
                <input type="password" name="password_confirm" required>
            </div>

            <label style="font-weight:500; display:block; margin-bottom:0.5rem;">نوع الحساب</label>
            <!-- أزرار راديو حقيقية تعمل بدون جافاسكريبت -->
            <div class="type-selector">
                <input type="radio" id="clientRadio" name="user_type" value="client" checked hidden>
                <label for="clientRadio" class="type-btn">باحث عن خدمات</label>

                <input type="radio" id="providerRadio" name="user_type" value="provider" hidden>
                <label for="providerRadio" class="type-btn">مقدم خدمات</label>
            </div>

            <button type="submit" class="btn-primary" style="width:100%; margin-top:1rem;">إنشاء حساب</button>
        </form>
        <div class="auth-link">
            لديك حساب؟ <a href="login.php">سجل دخولك</a>
        </div>
    </div>
</body>
</html>