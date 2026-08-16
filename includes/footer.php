<nav class="bottom-nav">
    <a href="home.php" class="<?= basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : '' ?>">
        <span class="nav-icon">🏠</span>
        <span>الرئيسية</span>
    </a>
    <a href="requests.php" class="<?= basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'active' : '' ?>">
        <span class="nav-icon">📋</span>
        <span>الطلبات</span>
    </a>
    <a href="create-request.php" class="<?= basename($_SERVER['PHP_SELF']) == 'create-request.php' || basename($_SERVER['PHP_SELF']) == 'create-service.php' ? 'active' : '' ?>">
        <span class="nav-icon">➕</span>
        <span>إنشاء</span>
    </a>
    <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
        <span class="nav-icon">👤</span>
        <span>حسابي</span>
    </a>
</nav>
<script src="../assets/js/main.js"></script>
</body>
</html>