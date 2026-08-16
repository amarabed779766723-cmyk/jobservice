<?php
require_once __DIR__ . '/../includes/admin_auth.php';

$usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$servicesCount = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
$reportsCount = $pdo->query("SELECT COUNT(*) FROM reports WHERE status = 'pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم - Job Service</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">

    <aside class="admin-sidebar">
        <div class="admin-sidebar-logo">⚙️ Job Service</div>
        <a href="dashboard.php" class="active">📊 الرئيسية</a>
        <a href="users.php">👥 المستخدمون</a>
        <a href="services.php">🛠️ الخدمات</a>
        <a href="reports.php">🚩 البلاغات</a>
        <a href="logout.php">🚪 تسجيل خروج</a>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1>أهلاً، <?= htmlspecialchars($adminUser['name']) ?></h1>
        </div>
        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-card-value"><?= number_format($usersCount) ?></div>
                <div class="stat-card-label">👥 مستخدم</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value"><?= number_format($servicesCount) ?></div>
                <div class="stat-card-label">🛠️ خدمة</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-value"><?= number_format($reportsCount) ?></div>
                <div class="stat-card-label">🚩 بلاغ جديد</div>
            </div>
        </div>
    </main>

</body>
</html>