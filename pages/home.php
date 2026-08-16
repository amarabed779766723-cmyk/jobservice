<?php
$pageTitle = 'الرئيسية';
require_once __DIR__ . '/../includes/header.php';

// حذف القصص المنتهية
$pdo->query("DELETE FROM stories WHERE expires_at < NOW()");

// جلب الخدمات
$stmt = $pdo->prepare("
    SELECT s.*, u.name AS provider_name, u.avatar AS provider_avatar
    FROM services s
    JOIN users u ON s.provider_id = u.id
    ORDER BY s.created_at DESC LIMIT 10
");
$stmt->execute();
$services = $stmt->fetchAll();

// جلب الطلبات المفتوحة
$reqStmt = $pdo->prepare("
    SELECT sr.*, u.name AS requester_name, u.avatar AS requester_avatar
    FROM service_requests sr
    JOIN users u ON sr.user_id = u.id
    WHERE sr.status = 'open'
    ORDER BY sr.created_at DESC LIMIT 5
");
$reqStmt->execute();
$latestRequests = $reqStmt->fetchAll();

// جلب أحدث المنشورات
$postsStmt = $pdo->prepare("
    SELECT p.*, u.name AS user_name, u.avatar AS user_avatar,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS likes_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC LIMIT 10
");
$postsStmt->execute();
$posts = $postsStmt->fetchAll();

// جلب القصص (من أتابعهم + قصصي)
if ($isLoggedIn) {
    $storiesStmt = $pdo->prepare("
        SELECT s.*, u.name, u.avatar
        FROM stories s
        JOIN users u ON s.user_id = u.id
        WHERE s.user_id IN (SELECT following_id FROM follows WHERE follower_id = ?)
           OR s.user_id = ?
        ORDER BY s.created_at DESC
    ");
    $storiesStmt->execute([$currentUser['id'], $currentUser['id']]);
    $stories = $storiesStmt->fetchAll();
} else {
    $stories = [];
}
?>

<div class="page-container">
    <main class="page-content">

        <?php if ($isLoggedIn): ?>
            <!-- صندوق إنشاء منشور -->
            <div class="card" style="margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <img src="../uploads/avatars/<?= htmlspecialchars($currentUser['avatar'] ?? 'default-avatar.png') ?>"
                         style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;">
                    <a href="create-post.php"
                       style="background: var(--bg); border-radius: 2rem; padding: 0.7rem 1.25rem; flex: 1; color: var(--text-secondary); text-decoration: none; font-size: 0.95rem;">
                        ✍️ ما الذي تريد مشاركته؟
                    </a>
                </div>
                <div style="display: flex; justify-content: space-around; margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 0.75rem;">
                    <a href="create-post.php" style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;">📷 صورة</a>
                    <a href="<?= $currentUser['user_type'] === 'provider' ? 'create-service.php' : 'create-request.php' ?>"
                       style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;">
                       <?= $currentUser['user_type'] === 'provider' ? '🛠️ خدمة' : '📝 طلب' ?>
                    </a>
                </div>
            </div>

            <!-- شريط القصص -->
            <div class="card" style="margin-bottom: 1rem; padding: 0.75rem;">
                <div style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 0.5rem;">
                    <a href="stories.php" style="text-align: center; text-decoration: none; color: inherit; min-width: 70px;">
                        <div style="width: 65px; height: 65px; border-radius: 50%; background: var(--bg); border: 2px dashed var(--text-secondary); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <span style="font-size: 2rem; color: var(--primary);">+</span>
                        </div>
                        <p style="font-size: 0.7rem; margin-top: 0.25rem;">قصتك</p>
                    </a>
                    <?php foreach ($stories as $story): ?>
                        <a href="../uploads/stories/<?= htmlspecialchars($story['image']) ?>" target="_blank" style="text-align: center; text-decoration: none; color: inherit; min-width: 70px;">
                            <div style="width: 65px; height: 65px; border-radius: 50%; background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); padding: 3px; margin: 0 auto;">
                                <img src="../uploads/stories/<?= htmlspecialchars($story['image']) ?>" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 2px solid white;">
                            </div>
                            <p style="font-size: 0.7rem; margin-top: 0.25rem;"><?= htmlspecialchars($story['name']) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- بطاقة خدمات قريبة -->
            <?php if (!empty($currentUser['latitude']) && !empty($currentUser['longitude'])): ?>
                <div class="card" style="margin-bottom: 1rem; background: #eff6ff; border: 1px solid #bfdbfe;">
                    <h3 style="margin-bottom: 0.75rem;">📍 خدمات قريبة منك</h3>
                    <?php
                    $lat = $currentUser['latitude'];
                    $lng = $currentUser['longitude'];
                    $nearbyStmt = $pdo->prepare("
                        SELECT s.*, u.name AS provider_name, u.avatar AS provider_avatar, u.latitude, u.longitude,
                               ( 6371 * acos( cos( radians( ? ) ) * cos( radians( u.latitude ) )
                               * cos( radians( u.longitude ) - radians( ? ) ) + sin( radians( ? ) )
                               * sin( radians( u.latitude ) ) ) ) AS distance
                        FROM services s
                        JOIN users u ON s.provider_id = u.id
                        WHERE u.latitude IS NOT NULL AND u.longitude IS NOT NULL
                        ORDER BY distance ASC LIMIT 5
                    ");
                    $nearbyStmt->execute([$lat, $lng, $lat]);
                    $nearbyServices = $nearbyStmt->fetchAll();
                    ?>
                    <?php foreach ($nearbyServices as $srv): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                            <div>
                                <strong><?= htmlspecialchars($srv['title']) ?></strong>
                                <span style="color: var(--text-secondary); font-size: 0.85rem;">
                                    <?= htmlspecialchars($srv['provider_name']) ?> - 📍 <?= number_format($srv['distance'], 1) ?> كم
                                </span>
                            </div>
                            <a href="service.php?id=<?= $srv['id'] ?>" class="btn btn-primary btn-sm">عرض</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- المنشورات -->
        <?php if (count($posts) > 0): ?>
            <h3 style="font-weight: 700; font-size: 1.15rem; margin: 1.5rem 0 1rem;">📢 أحدث المنشورات</h3>
            <?php foreach ($posts as $post): ?>
            <div class="card" style="margin-bottom: 1rem;" id="post-<?= $post['id'] ?>">
                <div class="card-header" style="display: flex; align-items: center; gap: 0.75rem;">
                    <img src="../uploads/avatars/<?= htmlspecialchars($post['user_avatar'] ?? 'default-avatar.png') ?>" class="card-avatar" alt="">
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                        <div>
                            <div class="card-user"><?= htmlspecialchars($post['user_name']) ?></div>
                            <div class="card-meta"><?= date('Y/m/d H:i', strtotime($post['created_at'])) ?></div>
                        </div>
                        <?php if ($isLoggedIn && $currentUser['id'] != $post['user_id']): ?>
                            <a href="start_chat.php?with=<?= $post['user_id'] ?>"
                               style="margin-right: auto; font-size: 0.8rem; color: var(--primary); text-decoration: none; background: var(--bg); padding: 0.2rem 0.6rem; border-radius: 2rem;">
                                💬 مراسلة
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($post['content']): ?>
                    <p style="margin-bottom: 0.75rem; line-height: 1.6;"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <?php endif; ?>
                <?php if ($post['image']): ?>
                    <img src="../uploads/posts/<?= htmlspecialchars($post['image']) ?>" style="width:100%; border-radius:8px; margin-bottom:0.75rem; max-height:400px; object-fit:cover;" alt="">
                <?php endif; ?>
                <?php if ($post['video']): ?>
                    <video controls style="width:100%; border-radius:8px; margin-bottom:0.75rem; max-height:400px;">
                        <source src="../uploads/videos/<?= htmlspecialchars($post['video']) ?>" type="video/mp4">
                    </video>
                <?php endif; ?>
                <?php if ($post['file_attachment']): ?>
                    <div style="background: var(--bg); padding: 0.75rem; border-radius: 8px; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 1.5rem;">📄</span>
                        <a href="../uploads/files/<?= htmlspecialchars($post['file_attachment']) ?>" download style="color: var(--primary); text-decoration: none;"><?= htmlspecialchars($post['file_name'] ?? 'تحميل الملف') ?></a>
                    </div>
                <?php endif; ?>

                <div class="card-footer" style="border-top: 1px solid var(--border); padding-top: 0.75rem;">
                    <div style="display: flex; gap: 1.5rem;">
                        <button onclick="toggleLike(<?= $post['id'] ?>, this)" style="background:none; border:none; cursor:pointer; color: var(--text-secondary); font-family: 'Tajawal', sans-serif; font-size: 0.9rem;">
                            ❤️ <span class="like-count"><?= $post['likes_count'] ?></span> إعجاب
                        </button>
                        <button onclick="toggleCommentBox(<?= $post['id'] ?>)" style="background:none; border:none; cursor:pointer; color: var(--text-secondary); font-family: 'Tajawal', sans-serif; font-size: 0.9rem;">
                            💬 <span class="comment-count"><?= $post['comments_count'] ?></span> تعليق
                        </button>
                    </div>
                </div>

                <div id="commentBox-<?= $post['id'] ?>" style="display: none; margin-top: 0.75rem; border-top: 1px solid var(--border); padding-top: 0.75rem;">
                    <?php if ($isLoggedIn): ?>
                    <form onsubmit="submitComment(event, <?= $post['id'] ?>, null)" style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <input type="text" name="comment_text" placeholder="اكتب تعليقاً..." required
                               style="flex:1; padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: 2rem;">
                        <button type="submit" class="btn btn-primary btn-sm">إرسال</button>
                    </form>
                    <?php else: ?>
                        <p style="text-align:center;"><a href="login.php">سجل دخولك</a> للتعليق.</p>
                    <?php endif; ?>
                    <div class="comments-preview" id="commentsPreview-<?= $post['id'] ?>">
                        <a href="post-detail.php?id=<?= $post['id'] ?>" style="font-size:0.9rem; color: var(--text-secondary);">عرض جميع التعليقات</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- أحدث الخدمات -->
        <h3 style="font-weight: 700; font-size: 1.15rem; margin: 1.5rem 0 1rem;">🛠️ أحدث الخدمات</h3>
        <?php foreach ($services as $srv): ?>
        <div class="card">
            <div class="card-header">
                <img src="../uploads/avatars/<?= htmlspecialchars($srv['provider_avatar'] ?? 'default-avatar.png') ?>" class="card-avatar" alt="">
                <div>
                    <div class="card-user"><?= htmlspecialchars($srv['provider_name']) ?></div>
                    <div class="card-meta"><?= date('Y/m/d', strtotime($srv['created_at'])) ?></div>
                </div>
                <?php if ($isLoggedIn && $currentUser['id'] != $srv['provider_id']): ?>
                    <?php
                    $checkFollow = $pdo->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
                    $checkFollow->execute([$currentUser['id'], $srv['provider_id']]);
                    $isFollowing = $checkFollow->fetch();
                    ?>
                    <a href="follow_action.php?user=<?= $srv['provider_id'] ?>"
                       style="font-size: 0.75rem; color: <?= $isFollowing ? 'var(--text-secondary)' : 'var(--primary)' ?>; text-decoration: none;">
                        <?= $isFollowing ? '✅ تتابعه' : '➕ متابعة' ?>
                    </a>
                <?php endif; ?>
            </div>
            <a href="service.php?id=<?= $srv['id'] ?>" style="text-decoration:none;color:inherit;">
                <h3 class="card-title"><?= htmlspecialchars($srv['title']) ?></h3>
            </a>
            <p class="card-body"><?= htmlspecialchars(mb_strimwidth($srv['description'] ?? 'بدون وصف', 0, 150, '...')) ?></p>
            <div class="card-footer">
                <span class="card-price"><?= number_format($srv['price'], 2) ?> ر.س</span>
                <a href="service.php?id=<?= $srv['id'] ?>" class="btn btn-primary btn-sm">عرض التفاصيل</a>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- طلبات مفتوحة -->
        <?php if (count($latestRequests) > 0): ?>
            <h3 style="font-weight: 700; font-size: 1.15rem; margin: 1.5rem 0 1rem;">📋 طلبات تحتاج منفذين</h3>
            <?php foreach ($latestRequests as $req): ?>
            <div class="card">
                <div class="card-header">
                    <img src="../uploads/avatars/<?= htmlspecialchars($req['requester_avatar'] ?? 'default-avatar.png') ?>" class="card-avatar" alt="">
                    <div>
                        <div class="card-user"><?= htmlspecialchars($req['requester_name']) ?></div>
                        <div class="card-meta"><?= date('Y/m/d', strtotime($req['created_at'])) ?></div>
                    </div>
                </div>
                <a href="request-detail.php?id=<?= $req['id'] ?>" style="text-decoration:none;color:inherit;">
                    <h3 class="card-title"><?= htmlspecialchars($req['title']) ?></h3>
                </a>
                <p class="card-body"><?= htmlspecialchars(mb_strimwidth($req['description'] ?? 'بدون وصف', 0, 150, '...')) ?></p>
                <div class="card-footer">
                    <span class="card-price">الميزانية: <?= number_format($req['budget'], 2) ?> ر.س</span>
                    <a href="request-detail.php?id=<?= $req['id'] ?>" class="btn btn-primary btn-sm">قدّم عرضاً</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </main>

    <!-- الشريط الجانبي -->
    <aside class="sidebar">
        <?php if ($isLoggedIn): ?>
            <div class="sidebar-card">
                <div class="sidebar-title">👤 حسابي</div>
                <a href="profile.php" class="sidebar-item">
                    <img src="../uploads/avatars/<?= htmlspecialchars($currentUser['avatar'] ?? 'default-avatar.png') ?>" style="width:28px;height:28px;border-radius:50%;">
                    <?= htmlspecialchars($currentUser['name']) ?>
                </a>
                <a href="edit-profile.php" class="sidebar-item">✏️ تعديل الملف</a>
                <a href="settings.php" class="sidebar-item">⚙️ الإعدادات</a>
                <a href="logout.php" class="sidebar-item">🚪 تسجيل خروج</a>
            </div>
            <div class="sidebar-card">
                <div class="sidebar-title">⚡ إجراءات سريعة</div>
                <a href="create-post.php" class="sidebar-item">📝 منشور جديد</a>
                <?php if ($currentUser['user_type'] === 'client'): ?>
                    <a href="create-request.php" class="sidebar-item">➕ طلب خدمة جديد</a>
                <?php else: ?>
                    <a href="create-service.php" class="sidebar-item">🛠️ إضافة خدمة جديدة</a>
                <?php endif; ?>
                <a href="requests.php" class="sidebar-item">📋 تصفح الطلبات</a>
            </div>
        <?php else: ?>
            <div class="sidebar-card">
                <div class="sidebar-title">🔐 انضم إلى المنصة</div>
                <a href="register.php" class="btn btn-primary" style="display:block; text-align:center; margin-bottom:0.5rem;">إنشاء حساب</a>
                <a href="login.php" class="btn btn-outline" style="display:block; text-align:center;">تسجيل الدخول</a>
            </div>
        <?php endif; ?>
    </aside>
</div>

<script>
function toggleLike(postId, btn) {
    fetch('like_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'post_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            var countSpan = btn.querySelector('.like-count');
            if (countSpan) countSpan.textContent = data.likes_count;
        }
    })
    .catch(error => console.error(error));
}

function toggleCommentBox(postId) {
    var box = document.getElementById('commentBox-' + postId);
    if (box) box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
}

function submitComment(event, postId, parentId = null) {
    event.preventDefault();
    var form = event.target;
    var input = form.querySelector('input[name="comment_text"]');
    if (!input) return;
    var text = input.value.trim();
    if (!text) return;

    var body = 'post_id=' + postId + '&comment_text=' + encodeURIComponent(text);
    if (parentId) body += '&parent_id=' + parentId;

    fetch('comment_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            var countSpan = document.querySelector('#post-' + postId + ' .comment-count');
            if (countSpan) countSpan.textContent = data.comments_count;
            var preview = document.getElementById('commentsPreview-' + postId);
            if (preview) {
                var div = document.createElement('div');
                div.style.cssText = 'margin-top:0.5rem; font-size:0.9rem;';
                div.innerHTML = '<strong>' + data.user_name + '</strong> ' + data.comment_text;
                preview.insertBefore(div, preview.firstChild);
            }
        }
    })
    .catch(error => console.error(error));
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>