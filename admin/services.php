<?php
require_once __DIR__ . '/../includes/admin_auth.php';

// حذف خدمة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_service'])) {
    $serviceId = $_POST['service_id'];
    $stmt = $pdo->prepare("SELECT title FROM services WHERE id = ?");
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch();
    if ($service) {
        $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$serviceId]);
        $pdo->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details) VALUES (?, 'delete', 'service', ?, ?)")
            ->execute([$adminUser['id'], $serviceId, 'حذف خدمة: ' . $service['title']]);
    }
    header('Location: services.php');
    exit;
}

// جلب كل الخدمات
$services = $pdo->query("
    SELECT s.*, u.name AS provider_name 
    FROM services s 
    JOIN users u ON s.provider_id = u.id 
    ORDER BY s.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة الخدمات - Job Service</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-logo">⚙️ Job Service</div>
        <a href="dashboard.php">📊 الرئيسية</a>
        <a href="users.php">👥 المستخدمون</a>
        <a href="services.php" class="active">🛠️ الخدمات</a>
        <a href="reports.php">🚩 البلاغات</a>
        <a href="logout.php">🚪 تسجيل خروج</a>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h2>🛠️ إدارة جميع الخدمات</h2>
            <span>إجمالي الخدمات: <?= count($services) ?></span>
        </div>

        <?php if (count($services) === 0): ?>
            <div class="stat-card" style="text-align:center; padding: 2rem;">
                <p>لا توجد خدمات في المنصة حالياً.</p>
            </div>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>العنوان</th>
                        <th>مقدم الخدمة</th>
                        <th>السعر</th>
                        <th>التاريخ</th>
                        <th>إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $srv): ?>
                    <tr>
                        <td><?= $srv['id'] ?></td>
                        <td><?= htmlspecialchars($srv['title']) ?></td>
                        <td><?= htmlspecialchars($srv['provider_name']) ?></td>
                        <td><?= number_format($srv['price'], 2) ?> ر.س</td>
                        <td><?= date('Y/m/d', strtotime($srv['created_at'])) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟');">
                                <input type="hidden" name="service_id" value="<?= $srv['id'] ?>">
                                <button type="submit" name="delete_service" class="btn btn-danger btn-sm">حذف</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>