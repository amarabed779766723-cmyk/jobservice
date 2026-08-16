<?php
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'], $_POST['action'])) {
    $reportId = $_POST['report_id'];
    if ($_POST['action'] === 'delete_content' && isset($_POST['service_id'])) {
        $serviceId = $_POST['service_id'];
        $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$serviceId]);
        $pdo->prepare("UPDATE reports SET status = 'resolved' WHERE id = ?")->execute([$reportId]);
    } else {
        $newStatus = ($_POST['action'] === 'reviewed') ? 'reviewed' : 'resolved';
        $pdo->prepare("UPDATE reports SET status = ? WHERE id = ?")->execute([$newStatus, $reportId]);
    }
    $pdo->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id) VALUES (?, 'review_report', 'report', ?)")
        ->execute([$adminUser['id'], $reportId]);
    header('Location: reports.php');
    exit;
}

$reports = $pdo->query("SELECT r.*, u1.name AS reporter_name, u2.name AS reported_name, s.title AS service_title FROM reports r LEFT JOIN users u1 ON r.reporter_id = u1.id LEFT JOIN users u2 ON r.reported_user_id = u2.id LEFT JOIN services s ON r.service_id = s.id ORDER BY r.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة البلاغات</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-logo">⚙️ Job Service</div>
        <a href="dashboard.php">📊 الرئيسية</a>
        <a href="users.php">👥 المستخدمون</a>
        <a href="services.php">🛠️ الخدمات</a>
        <a href="reports.php" class="active">🚩 البلاغات</a>
        <a href="logout.php">🚪 تسجيل خروج</a>
    </aside>

    <main class="admin-main">
        <h2>🚩 إدارة البلاغات</h2>
        <table class="admin-table">
            <thead>
                <tr><th>ID</th><th>المُبلِّغ</th><th>المُبلَّغ عنه</th><th>الخدمة</th><th>السبب</th><th>الحالة</th><th>إجراء</th></tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $rep): ?>
                <tr>
                    <td><?= $rep['id'] ?></td>
                    <td><?= htmlspecialchars($rep['reporter_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($rep['reported_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($rep['service_title'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($rep['reason']) ?></td>
                    <td><?= $rep['status'] ?></td>
                    <td>
                        <form method="POST" style="display:flex; gap:4px;">
                            <input type="hidden" name="report_id" value="<?= $rep['id'] ?>">
                            <?php if ($rep['status'] === 'pending'): ?>
                                <button type="submit" name="action" value="reviewed" class="btn btn-outline btn-sm">مراجعة</button>
                                <?php if ($rep['service_id']): ?>
                                    <input type="hidden" name="service_id" value="<?= $rep['service_id'] ?>">
                                    <button type="submit" name="action" value="delete_content" class="btn btn-danger btn-sm">حذف المحتوى</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>