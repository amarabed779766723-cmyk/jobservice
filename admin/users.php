<?php
require_once __DIR__ . '/../includes/admin_auth.php';

// معالجة الحظر وفك الحظر
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $targetId = $_POST['user_id'];
    $newStatus = ($_POST['action'] === 'ban') ? 'banned' : 'active';
    
    $pdo->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$newStatus, $targetId]);
    
    // تسجيل الإجراء في سجل الأدمن
    $pdo->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id) VALUES (?, ?, 'user', ?)")
        ->execute([$adminUser['id'], $_POST['action'], $targetId]);
    
    header('Location: users.php');
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المستخدمين - Job Service</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-logo">⚙️ Job Service</div>
        <a href="dashboard.php">📊 الرئيسية</a>
        <a href="users.php" class="active">👥 المستخدمون</a>
        <a href="services.php">🛠️ الخدمات</a>
        <a href="reports.php">🚩 البلاغات</a>
        <a href="logout.php">🚪 تسجيل خروج</a>
    </aside>

    <main class="admin-main">
        <h2>👥 جميع المستخدمين</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>الاسم</th>
                    <th>البريد</th>
                    <th>النوع</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['user_type'] === 'provider' ? 'مقدم' : 'باحث' ?></td>
                    <td>
                        <?php if (($u['status'] ?? 'active') === 'banned'): ?>
                            <span style="color: red;">🚫 محظور</span>
                        <?php else: ?>
                            <span style="color: green;">✅ نشط</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <?php if (($u['status'] ?? 'active') === 'banned'): ?>
                                <button type="submit" name="action" value="unban" class="btn btn-success btn-sm">فك الحظر</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="ban" class="btn btn-danger btn-sm">حظر</button>
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