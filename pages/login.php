<?php
require_once __DIR__ . '/../includes/init.php';

// لو المستخدم مسجل دخول بالفعل، نحوله على الرئيسية
if ($isLoggedIn) {
    header('Location: home.php');
    exit;
}

// نحاول نحتفظ برابط الصفحة اللي جاي منها (عشان نرجعه بعد الدخول)
if (!isset($_SESSION['redirect_after_login']) && !empty($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    // ما نحفظش الرابط لو كان هو نفسه صفحة الدخول أو التسجيل
    if (strpos($referer, 'login.php') === false && strpos($referer, 'register.php') === false) {
        $_SESSION['redirect_after_login'] = $referer;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']); // هل فعل "تذكرني"؟

    // تحقق بسيط
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'يرجى إدخال بريد إلكتروني صحيح.';
    } elseif (empty($password)) {
        $error = 'يرجى إدخال كلمة المرور.';
    } else {
        // نجيب المستخدم من قاعدة البيانات
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // الحساب محظور؟
            if (($user['status'] ?? 'active') === 'banned') {
                $error = 'تم حظر هذا الحساب.';
            } else {
                // تسجيل الدخول فعلياً
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                // ------ نظام "تذكرني" ------
                if ($remember) {
                    $token = bin2hex(random_bytes(32)); // رمز آمن
                    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

                    // تخزين الرمز في قاعدة البيانات
                    $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                    $stmt->execute([$user['id'], $token, $expires]);

                    // وضع الكوكيز في المتصفح لمدة 30 يوم
                    setcookie('remember_token', $token, strtotime('+30 days'), '/', '', false, true);
                    setcookie('remember_user', $user['id'], strtotime('+30 days'), '/', '', false, true);
                }

                // توجيه إلى الصفحة اللي كان فيها أو الرئيسية
                $redirect = $_SESSION['redirect_after_login'] ?? 'home.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            }
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
        }
    }
}

// نجهز العنوان للـ <title>
$pageTitle = 'تسجيل الدخول - Job Service';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-page">

    <div class="auth-box">
        <div class="brand-logo">Job Service</div>
        <div class="brand-slogan">أهلاً بعودتك</div>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" required>
            </div>

            <!-- مربع "تذكرني" -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem; color: #374151;">
                    <input type="checkbox" name="remember" style="width: 16px; height: 16px;">
                    تذكرني
                </label>
            </div>

            <button type="submit" class="btn-primary" style="width:100%;">تسجيل الدخول</button>
        </form>

        <div class="auth-link">
            ليس لديك حساب؟ <a href="register.php">أنشئ حساباً</a>
        </div>
    </div>

</body>
</html>