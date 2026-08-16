<?php
// pages/logout.php

// 1. تشغيل الجلسة
session_start();

// 2. الاتصال بقاعدة البيانات (لحذف رموز تذكرني)
require_once __DIR__ . '/../config/db.php';

// 3. حذف رمز "تذكرني" من قاعدة البيانات إذا كان موجوداً في الكوكيز
if (isset($_COOKIE['remember_token'])) {
    $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE token = ?");
    $stmt->execute([$_COOKIE['remember_token']]);
}

// 4. مسح كوكيز "تذكرني" من المتصفح
setcookie('remember_token', '', time() - 3600, '/');
setcookie('remember_user', '', time() - 3600, '/');

// 5. تدمير الجلسة بالكامل
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// 6. التوجيه إلى صفحة تسجيل الدخول
header('Location: login.php');
exit;