// ==================== CSRF Token ====================
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// ==================== الأدوات المساعدة ====================

function showToast2026(message, type = 'info') {
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    let toast = document.getElementById('toast2026');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast2026';
        toast.className = 'toast-2026';
        document.body.appendChild(toast);
    }
    
    toast.innerHTML = `
        <span class="toast-icon">${icons[type] || 'ℹ️'}</span>
        <span class="toast-message">${message}</span>
    `;
    toast.className = `toast-2026 toast-${type}`;
    
    clearTimeout(toast._timeout);
    toast.classList.add('show');
    toast._timeout = setTimeout(() => toast.classList.remove('show'), 3000);
}

function showToast(message, type = 'info') {
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.add('show');
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => toast.classList.remove('show'), 2500);
}

// ==================== دوال الصفحات ====================

function setUserType(type) {
    const input = document.getElementById('userType');
    if (input) input.value = type;
    document.getElementById('clientBtn')?.classList.toggle('active', type === 'client');
    document.getElementById('providerBtn')?.classList.toggle('active', type === 'provider');
}

function initStarRating() {
    const stars = document.querySelectorAll('.star-rating span');
    if (!stars.length) return;
    const input = document.getElementById('scoreInput');
    stars.forEach(star => {
        star.addEventListener('click', () => {
            const val = star.getAttribute('data-value');
            input.value = val;
            stars.forEach((s, i) => s.classList.toggle('active', i < val));
        });
    });
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
}

function toggleDarkMode(checkbox) {
    const isDark = checkbox.checked;
    applyTheme(isDark ? 'dark' : 'light');
}

function toggleDarkModeAdvanced() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';
    applyTheme(newTheme);
}

function detectSystemTheme() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    return 'light';
}

(function() {
    var saved = localStorage.getItem('theme');
    if (saved === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else if (!saved) {
        var systemTheme = detectSystemTheme();
        document.documentElement.setAttribute('data-theme', systemTheme);
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
    }
})();

function confirmDelete(message = 'هل أنت متأكد؟') {
    return confirm(message);
}

function initFieldValidation() {
    const nameInput = document.querySelector('input[name="name"]');
    const emailInput = document.querySelector('input[name="email"]');
    const passInput = document.querySelector('input[name="password"]');
    
    if (nameInput) {
        nameInput.addEventListener('input', () => {
            const valid = /^[a-zA-Zء-ي\s]+$/u.test(nameInput.value);
            nameInput.style.borderColor = valid ? '#10b981' : '#ef4444';
        });
    }
    if (emailInput) {
        emailInput.addEventListener('input', () => {
            const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value);
            emailInput.style.borderColor = valid ? '#10b981' : '#ef4444';
        });
    }
    if (passInput) {
        passInput.addEventListener('input', () => {
            const v = passInput.value;
            const strong = v.length >= 8 && /[A-Z]/.test(v) && /[a-z]/.test(v) && /[0-9]/.test(v);
            passInput.style.borderColor = strong ? '#10b981' : (v.length > 0 ? '#ef4444' : '');
        });
    }
}

// ==================== شريط البحث ====================
let searchTimeout;

function globalSearch(query) {
    clearTimeout(searchTimeout);
    const resultsDiv = document.getElementById('searchResults');
    if (!resultsDiv) return;
    
    // ✅ إذا كتب # فقط - عرض كل الهاشتاقات
    if (query === '#') {
        searchTimeout = setTimeout(() => {
            fetch('/search?all_hashtags=1')
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    
                    if (data.hashtags && data.hashtags.length) {
                        html += `<div style="padding:0.5rem 1rem;font-weight:700;color:#8B5CF6;font-size:0.8rem;">🏷️ الهاشتاقات</div>`;
                        data.hashtags.forEach(h => {
                            html += `<a href="/hashtag/${h}" style="display:block;padding:0.5rem 1rem;color:var(--text);text-decoration:none;border-bottom:1px solid var(--border);">#${h}</a>`;
                        });
                    }
                    
                    resultsDiv.innerHTML = html || '<p style="padding:1rem;">لا توجد هاشتاقات</p>';
                    resultsDiv.style.display = 'block';
                });
        }, 300);
        return;
    }
    
    // ✅ إذا بدأ بـ # - بحث بالهاشتاق
    if (query.startsWith('#')) {
        const hashtag = query.substring(1);
        
        if (hashtag.length < 1) {
            resultsDiv.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch(`/search?hashtag=${encodeURIComponent(hashtag)}`)
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    
                    if (data.posts && data.posts.length) {
                        html += `<div style="padding:0.5rem 1rem;font-weight:700;color:#8B5CF6;font-size:0.8rem;">📝 منشورات #${hashtag}</div>`;
                        data.posts.forEach(p => {
                            const content = p.content ? p.content.replace(/<[^>]*>/g, '').substring(0, 50) : 'منشور';
                            html += `<a href="/post/${p.id}" style="display:block;padding:0.5rem 1rem;color:var(--text);text-decoration:none;border-bottom:1px solid var(--border);">${content}...</a>`;
                        });
                    }
                    
                    resultsDiv.innerHTML = html || '<p style="padding:1rem;">لا توجد نتائج</p>';
                    resultsDiv.style.display = 'block';
                });
        }, 300);
        return;
    }
    
    // ✅ البحث العادي
    if (query.length < 2) {
        resultsDiv.style.display = 'none';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch(`/search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                let html = '';
                
                if (data.users && data.users.length) {
                    html += `<div style="padding:0.5rem 1rem;font-weight:700;color:var(--primary);font-size:0.8rem;">👤 مقدمي الخدمات</div>`;
                    data.users.forEach(u => {
                        const services = u.services && u.services.length > 0 
                            ? u.services.slice(0,2).map(s => s.title).join('، ') 
                            : '';
                        html += `<a href="/profile/${u.id}" style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem 1rem;color:var(--text);text-decoration:none;border-bottom:1px solid var(--border);">
                            <img src="/uploads/avatars/${u.avatar || 'default-avatar.png'}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                            <div style="flex:1;">
                                <div style="font-weight:600;">${u.name}</div>
                            </div>
                        </a>`;
                    });
                }
                
                if (data.services && data.services.length) {
                    html += `<div style="padding:0.5rem 1rem;font-weight:700;color:#10B981;font-size:0.8rem;">🛠️ خدمات</div>`;
                    data.services.forEach(s => {
                        html += `<a href="/service/${s.id}" style="display:block;padding:0.5rem 1rem;color:var(--text);text-decoration:none;border-bottom:1px solid var(--border);">${s.title}</a>`;
                    });
                }
                
                if (data.posts && data.posts.length) {
                    html += `<div style="padding:0.5rem 1rem;font-weight:700;color:#8B5CF6;font-size:0.8rem;">📝 المنشورات</div>`;
                    data.posts.forEach(p => {
                        const content = p.content ? p.content.substring(0, 50) : 'منشور';
                        html += `<a href="/post/${p.id}" style="display:block;padding:0.5rem 1rem;color:var(--text);text-decoration:none;border-bottom:1px solid var(--border);">${content}...</a>`;
                    });
                }
                
                resultsDiv.innerHTML = html || '<p style="padding:1rem;">لا توجد نتائج</p>';
                resultsDiv.style.display = 'block';
            });
    }, 300);
}

// ==================== الإعجابات (API) ====================
function toggleLike(postId, btn) {
    const likeSpan = btn.querySelector('.like-count');
    const isLiked = btn.classList.contains('liked');
    
    fetch(`/post/${postId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (likeSpan) likeSpan.textContent = data.likes_count;
            btn.classList.toggle('liked', data.liked);
            btn.style.color = data.liked ? '#ef4444' : 'var(--text-secondary)';
        }
    })
    .catch(() => {
        // Backup: تحديث محلي في حالة فشل الاتصال
        let count = parseInt(likeSpan?.textContent) || 0;
        if (isLiked) {
            count--;
            btn.classList.remove('liked');
            btn.style.color = 'var(--text-secondary)';
        } else {
            count++;
            btn.classList.add('liked');
            btn.style.color = '#ef4444';
        }
        if (likeSpan) likeSpan.textContent = count;
    });
}

// ==================== التعليقات (API) ====================
function toggleCommentBox(postId) {
    var box = document.getElementById('commentBox-' + postId);
    if (box) box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
}

function showReplyForm(commentId) {
    var form = document.getElementById('replyForm-' + commentId);
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        var input = form.querySelector('input[name="comment_text"]');
        if (input) input.focus();
    }
}

function submitComment(event, postId, parentId = null) {
    event.preventDefault();
    var form = event.target;
    var input = form.querySelector('input[name="comment_text"]');
    if (!input) return;
    var text = input.value.trim();
    if (!text) return;

    fetch(`/post/${postId}/comment`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ comment_text: text, parent_id: parentId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            showToast2026('تم إضافة التعليق ✅', 'success');
            // إعادة تحميل التعليقات
            loadComments(postId);
        }
    })
    .catch(() => {
        showToast2026('حدث خطأ، حاول مرة أخرى', 'error');
    });
}

function loadComments(postId) {
    fetch(`/post/${postId}/comments`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('commentsPreview-' + postId);
            if (container && data.success) {
                let html = '';
                data.data.forEach(comment => {
                    html += `
                        <div style="display:flex;gap:0.5rem;margin-bottom:0.75rem;align-items:flex-start;">
                            <img src="${comment.user_avatar}" style="width:32px;height:32px;border-radius:50%;">
                            <div style="flex:1;">
                                <div style="background:var(--bg);border-radius:12px;padding:0.5rem 0.75rem;">
                                    <strong style="font-size:0.9rem;">${comment.user_name}</strong>
                                    <span style="color:var(--text-secondary);font-size:0.85rem;display:block;">${comment.comment_text}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            }
        });
}

// ==================== قائمة إنشاء محتوى ====================
function toggleCreateMenu2026() {
    const menu = document.getElementById('createMenu2026');
    if (menu) {
        menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
    }
}

// ==================== دوال القصص (API) ====================
let currentStories = [];
let currentIndex = 0;
let progressInterval;
let videoElement;
let currentStoryId;

function openStoryViewer(userId) {
    fetch(`/stories/user/${userId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.stories.length > 0) {
                currentStories = data.stories;
                currentIndex = 0;
                
                const progressBars = document.getElementById('progressBars');
                if (progressBars) {
                    progressBars.innerHTML = currentStories.map(() => 
                        '<div class="story-progress-bar"><div class="story-progress-fill"></div></div>'
                    ).join('');
                }
                
                document.getElementById('storyViewerModal').style.display = 'flex';
                document.getElementById('storyUserName').textContent = data.user.name;
                document.getElementById('storyUserAvatar').src = data.user.avatar || '/uploads/avatars/default-avatar.png';
                
                showStory(0);
            }
        })
        .catch(() => {
            showToast2026('حدث خطأ في تحميل القصص', 'error');
        });
}

function showStory(index) {
    if (index >= currentStories.length) {
        closeStoryViewer();
        return;
    }

    currentIndex = index;
    const story = currentStories[index];
    currentStoryId = story.id;

    updateProgressBars();

    // تسجيل المشاهدة
    fetch(`/stories/${story.id}/view`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    });

    const timeEl = document.getElementById('storyTime');
    if (timeEl) timeEl.textContent = story.created_at;

    const content = document.getElementById('storyContent');
    if (content) {
        content.innerHTML = '';

        if (story.is_video) {
            videoElement = document.createElement('video');
            videoElement.src = story.image;
            videoElement.autoplay = true;
            videoElement.controls = false;
            videoElement.style.maxWidth = '100%';
            videoElement.style.maxHeight = '70vh';
            videoElement.style.borderRadius = '8px';
            
            videoElement.onended = () => {
                videoElement = null;
                nextStory();
            };
            
            content.appendChild(videoElement);
            clearInterval(progressInterval);
        } else {
            const img = document.createElement('img');
            img.src = story.image;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '70vh';
            img.style.borderRadius = '8px';
            img.style.objectFit = 'contain';
            content.appendChild(img);
            videoElement = null;
            startProgress(5000);
        }
    }

    const leftBtn = document.querySelector('.story-nav-btn.left');
    const rightBtn = document.querySelector('.story-nav-btn.right');
    if (leftBtn) leftBtn.style.display = index > 0 ? 'block' : 'none';
    if (rightBtn) rightBtn.style.display = index < currentStories.length - 1 ? 'block' : 'none';

    updateViewsInfo();
    markProgressBarsAsViewed();
}

function startProgress(duration) {
    clearInterval(progressInterval);
    const progressFill = document.querySelectorAll('.story-progress-fill')[currentIndex];
    if (!progressFill) return;
    
    let startTime = Date.now();
    progressFill.style.transition = 'none';
    progressFill.style.width = '0%';
    
    progressInterval = setInterval(() => {
        const elapsed = Date.now() - startTime;
        const percent = Math.min((elapsed / duration) * 100, 100);
        progressFill.style.transition = 'width 0.1s linear';
        progressFill.style.width = percent + '%';
        
        if (percent >= 100) {
            clearInterval(progressInterval);
            nextStory();
        }
    }, 50);
}

function updateProgressBars() {
    document.querySelectorAll('.story-progress-fill').forEach((fill, i) => {
        if (i < currentIndex) {
            fill.style.width = '100%';
            fill.style.background = 'white';
        } else if (i === currentIndex) {
            fill.style.width = '0%';
            fill.style.background = 'white';
        } else {
            fill.style.width = '0%';
            fill.style.background = 'rgba(255,255,255,0.3)';
        }
    });
}

function markProgressBarsAsViewed() {
    document.querySelectorAll('.story-progress-fill').forEach((fill, i) => {
        if (i < currentIndex) {
            fill.style.width = '100%';
        }
    });
}

function updateViewsInfo() {
    if (!currentStoryId) return;
    
    fetch(`/stories/${currentStoryId}/views`)
        .then(res => res.json())
        .then(data => {
            const viewsInfo = document.getElementById('storyViewsInfo');
            const viewsCount = document.getElementById('storyViewsCount');
            
            if (!viewsInfo || !viewsCount) return;
            
            if (data.is_owner === true) {
                viewsCount.textContent = data.count;
                viewsInfo.style.display = 'block';
            } else {
                viewsInfo.style.display = 'none';
            }
        });
}

function nextStory() {
    clearInterval(progressInterval);
    if (videoElement) {
        videoElement.pause();
        videoElement = null;
    }
    showStory(currentIndex + 1);
}

function prevStory() {
    clearInterval(progressInterval);
    if (videoElement) {
        videoElement.pause();
        videoElement = null;
    }
    if (currentIndex > 0) {
        showStory(currentIndex - 1);
    }
}

function closeStoryViewer() {
    clearInterval(progressInterval);
    if (videoElement) {
        videoElement.pause();
        videoElement = null;
    }
    document.getElementById('storyViewerModal').style.display = 'none';
    currentStories = [];
    currentIndex = 0;
}

function showStoryViewsModal() {
    if (!currentStoryId) return;
    
    fetch(`/stories/${currentStoryId}/views`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('viewsModalCount').textContent = data.count;
            
            let html = '';
            if (data.viewers.length === 0) {
                html = '<p style="text-align:center; opacity:0.7;">لا توجد مشاهدات بعد</p>';
            } else {
                data.viewers.forEach(viewer => {
                    html += `
                        <div class="views-user-item">
                            <img src="${viewer.avatar}" alt="${viewer.name}">
                            <span class="name">${viewer.name}</span>
                        </div>
                    `;
                });
            }
            document.getElementById('viewsModalList').innerHTML = html;
            document.getElementById('storyViewsModal').style.display = 'flex';
        });
}

function closeStoryViewsModal() {
    document.getElementById('storyViewsModal').style.display = 'none';
}

// ==================== المتابعة (API) ====================
function toggleFollowHome(userId, btn) {
    fetch('/follow/' + userId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (data.isFollowing) {
                btn.textContent = '✅ متابَع';
                btn.style.color = 'var(--text-secondary)';
                btn.classList.add('following');
                showToast2026('تم المتابعة ✅', 'success');
            } else {
                btn.textContent = '➕ متابعة';
                btn.style.color = 'var(--primary)';
                btn.classList.remove('following');
                showToast2026('تم إلغاء المتابعة', 'info');
            }
        }
    });
}

// ==================== نافذة القوائم ====================
function openListModal(type) {
    let title, data;
    
    if (type === 'followers') {
        title = '👥 المتابعون';
        data = window.profileData?.followers || [];
    } else if (type === 'following') {
        title = '👤 يتابع';
        data = window.profileData?.following || [];
    } else if (type === 'ratings') {
        title = '⭐ التقييمات';
        data = window.profileData?.ratings || [];
    }
    
    document.getElementById('listModalTitle').textContent = title;
    
    let html = '';
    if (data.length === 0) {
        html = '<p style="text-align:center; color:var(--text-secondary);">لا يوجد</p>';
    } else {
        data.forEach(item => {
            html += `
                <a href="/profile/${item.id}" class="list-user-item">
                    <img src="/uploads/avatars/${item.avatar}" alt="${item.name}">
                    <div class="list-user-info">
                        <strong>${item.name}</strong>
                        ${type === 'ratings' ? `<br><span style="color:#fbbf24;">${'★'.repeat(item.score)}</span> <span style="color:var(--text-secondary);font-size:0.8rem;">${item.review || ''}</span>` : ''}
                    </div>
                </a>
            `;
        });
    }
    
    document.getElementById('listModalContent').innerHTML = html;
    document.getElementById('listModal').style.display = 'flex';
}

function closeListModal() {
    document.getElementById('listModal').style.display = 'none';
}

// ==================== دوال قص الصورة ====================
let cropImage = null;
let cropType = '';
let isDragging = false;
let isResizing = false;
let activeCorner = '';
let startX, startY;
let cropX = 0, cropY = 0;
let cropWidth = 200, cropHeight = 200;
let cropCanvas, cropCtx;
let imgRatio = 1;

document.addEventListener('DOMContentLoaded', function() {
    cropCanvas = document.getElementById('cropCanvas');
    if (!cropCanvas) return;
    cropCtx = cropCanvas.getContext('2d');

    cropCanvas.addEventListener('mousedown', function(e) {
        const rect = cropCanvas.getBoundingClientRect();
        const mx = e.clientX - rect.left;
        const my = e.clientY - rect.top;
        const corners = {
            'tl': [cropX, cropY],
            'tr': [cropX + cropWidth, cropY],
            'bl': [cropX, cropY + cropHeight],
            'br': [cropX + cropWidth, cropY + cropHeight]
        };
        let cornerHit = false;
        for (const [key, [cx, cy]] of Object.entries(corners)) {
            if (Math.abs(mx - cx) < 20 && Math.abs(my - cy) < 20) {
                isResizing = true; activeCorner = key; startX = mx; startY = my; cornerHit = true; break;
            }
        }
        if (!cornerHit && mx >= cropX && mx <= cropX + cropWidth && my >= cropY && my <= cropY + cropHeight) {
            isDragging = true; startX = mx - cropX; startY = my - cropY;
        }
    });

    cropCanvas.addEventListener('mousemove', function(e) {
        if (!isDragging && !isResizing) return;
        const rect = cropCanvas.getBoundingClientRect();
        const mx = e.clientX - rect.left;
        const my = e.clientY - rect.top;
        if (isDragging) {
            cropX = Math.max(0, Math.min(mx - startX, cropCanvas.width - cropWidth));
            cropY = Math.max(0, Math.min(my - startY, cropCanvas.height - cropHeight));
        }
        if (isResizing) {
            const dx = mx - startX; const dy = my - startY;
            if (activeCorner === 'br') { cropWidth = Math.max(50, Math.min(cropWidth + dx, cropCanvas.width - cropX)); cropHeight = Math.max(50, Math.min(cropHeight + dy, cropCanvas.height - cropY)); }
            else if (activeCorner === 'tr') { cropWidth = Math.max(50, Math.min(cropWidth + dx, cropCanvas.width - cropX)); cropY = Math.max(0, Math.min(cropY + dy, cropY + cropHeight - 50)); cropHeight = Math.max(50, Math.min(cropHeight - dy, cropCanvas.height - cropY)); }
            else if (activeCorner === 'bl') { cropX = Math.max(0, Math.min(cropX + dx, cropX + cropWidth - 50)); cropWidth = Math.max(50, Math.min(cropWidth - dx, cropCanvas.width - cropX)); cropHeight = Math.max(50, Math.min(cropHeight + dy, cropCanvas.height - cropY)); }
            else if (activeCorner === 'tl') { cropX = Math.max(0, Math.min(cropX + dx, cropX + cropWidth - 50)); cropWidth = Math.max(50, Math.min(cropWidth - dx, cropCanvas.width - cropX)); cropY = Math.max(0, Math.min(cropY + dy, cropY + cropHeight - 50)); cropHeight = Math.max(50, Math.min(cropHeight - dy, cropCanvas.height - cropY)); }
            startX = mx; startY = my;
        }
        drawCropCanvas();
    });

    cropCanvas.addEventListener('mouseup', function() { isDragging = false; isResizing = false; });

    cropCanvas.addEventListener('touchstart', function(e) {
        e.preventDefault(); const touch = e.touches[0]; const rect = cropCanvas.getBoundingClientRect(); const mx = touch.clientX - rect.left; const my = touch.clientY - rect.top;
        const corners = { 'tl': [cropX, cropY], 'tr': [cropX + cropWidth, cropY], 'bl': [cropX, cropY + cropHeight], 'br': [cropX + cropWidth, cropY + cropHeight] };
        let cornerHit = false;
        for (const [key, [cx, cy]] of Object.entries(corners)) { if (Math.abs(mx - cx) < 30 && Math.abs(my - cy) < 30) { isResizing = true; activeCorner = key; startX = mx; startY = my; cornerHit = true; break; } }
        if (!cornerHit && mx >= cropX && mx <= cropX + cropWidth && my >= cropY && my <= cropY + cropHeight) { isDragging = true; startX = mx - cropX; startY = my - cropY; }
    });
    cropCanvas.addEventListener('touchmove', function(e) {
        if (!isDragging && !isResizing) return; e.preventDefault(); const touch = e.touches[0]; const rect = cropCanvas.getBoundingClientRect(); const mx = touch.clientX - rect.left; const my = touch.clientY - rect.top;
        if (isDragging) { cropX = Math.max(0, Math.min(mx - startX, cropCanvas.width - cropWidth)); cropY = Math.max(0, Math.min(my - startY, cropCanvas.height - cropHeight)); }
        if (isResizing) { const dx = mx - startX; const dy = my - startY;
            if (activeCorner === 'br') { cropWidth = Math.max(50, Math.min(cropWidth + dx, cropCanvas.width - cropX)); cropHeight = Math.max(50, Math.min(cropHeight + dy, cropCanvas.height - cropY)); }
            else if (activeCorner === 'tr') { cropWidth = Math.max(50, Math.min(cropWidth + dx, cropCanvas.width - cropX)); cropY = Math.max(0, Math.min(cropY + dy, cropY + cropHeight - 50)); cropHeight = Math.max(50, Math.min(cropHeight - dy, cropCanvas.height - cropY)); }
            else if (activeCorner === 'bl') { cropX = Math.max(0, Math.min(cropX + dx, cropX + cropWidth - 50)); cropWidth = Math.max(50, Math.min(cropWidth - dx, cropCanvas.width - cropX)); cropHeight = Math.max(50, Math.min(cropHeight + dy, cropCanvas.height - cropY)); }
            else if (activeCorner === 'tl') { cropX = Math.max(0, Math.min(cropX + dx, cropX + cropWidth - 50)); cropWidth = Math.max(50, Math.min(cropWidth - dx, cropCanvas.width - cropX)); cropY = Math.max(0, Math.min(cropY + dy, cropY + cropHeight - 50)); cropHeight = Math.max(50, Math.min(cropHeight - dy, cropCanvas.height - cropY)); }
            startX = mx; startY = my; }
        drawCropCanvas();
    });
    cropCanvas.addEventListener('touchend', function() { isDragging = false; isResizing = false; });
});

function openCropper(input, type) {
    const file = input.files[0]; if (!file) return;
    cropType = type; cropCanvas = document.getElementById('cropCanvas'); cropCtx = cropCanvas.getContext('2d');
    const reader = new FileReader();
    reader.onload = function(e) { cropImage = new Image(); cropImage.onload = function() {
        const maxW = Math.min(window.innerWidth * 0.9, 500); const maxH = Math.min(window.innerHeight * 0.5, 400);
        imgRatio = Math.min(maxW / cropImage.width, maxH / cropImage.height);
        cropCanvas.width = cropImage.width * imgRatio; cropCanvas.height = cropImage.height * imgRatio;
        cropWidth = Math.min(cropCanvas.width * 0.6, 250); cropHeight = type === 'avatar' ? cropWidth : cropWidth / 3;
        cropX = (cropCanvas.width - cropWidth) / 2; cropY = (cropCanvas.height - cropHeight) / 2;
        drawCropCanvas(); document.getElementById('cropperModal').style.display = 'flex';
    }; cropImage.src = e.target.result; };
    reader.readAsDataURL(file);
}

function drawCropCanvas() {
    if (!cropImage || !cropCtx) return;
    cropCtx.clearRect(0, 0, cropCanvas.width, cropCanvas.height);
    cropCtx.drawImage(cropImage, 0, 0, cropCanvas.width, cropCanvas.height);
    cropCtx.fillStyle = 'rgba(0,0,0,0.5)'; cropCtx.fillRect(0, 0, cropCanvas.width, cropCanvas.height);
    cropCtx.clearRect(cropX, cropY, cropWidth, cropHeight);
    cropCtx.drawImage(cropImage, cropX / imgRatio, cropY / imgRatio, cropWidth / imgRatio, cropHeight / imgRatio, cropX, cropY, cropWidth, cropHeight);
    cropCtx.strokeStyle = '#fff'; cropCtx.lineWidth = 2; cropCtx.strokeRect(cropX, cropY, cropWidth, cropHeight);
    cropCtx.fillStyle = '#fff';
    [[cropX, cropY], [cropX + cropWidth, cropY], [cropX, cropY + cropHeight], [cropX + cropWidth, cropY + cropHeight]].forEach(([x, y]) => { cropCtx.fillRect(x - 8, y - 8, 16, 16); });
}

function saveCrop() {
    if (!cropImage) return;
    const realX = cropX / imgRatio; const realY = cropY / imgRatio; const realW = cropWidth / imgRatio; const realH = cropHeight / imgRatio;
    const tmp = document.createElement('canvas'); tmp.width = realW; tmp.height = realH;
    tmp.getContext('2d').drawImage(cropImage, realX, realY, realW, realH, 0, 0, realW, realH);
    const url = tmp.toDataURL('image/jpeg', 0.9);
    if (cropType === 'avatar') { document.getElementById('avatarCropData').value = url; document.getElementById('avatarPreview').src = url; }
    else if (cropType === 'cover') { document.getElementById('coverCropData').value = url; document.getElementById('coverPreview').style.backgroundImage = 'url(' + url + ')'; }
    else if (cropType === 'post') {
        document.getElementById('postImageCropData').value = url;
        document.getElementById('postImagePreview').style.display = 'block';
        document.getElementById('postImagePreviewImg').src = url;
    }
    closeCropper();
}

function closeCropper() {
    document.getElementById('cropperModal').style.display = 'none';
    document.getElementById('avatarInput').value = ''; document.getElementById('coverInput').value = ''; cropImage = null;
}

// ==================== دوال الدردشة ====================
function toggleChatSettings() {
    var modal = document.getElementById('chatSettingsModal');
    if (modal) modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
}

// ==================== دوال الخدمات (filter) ====================
function filterServices(type) {
    var btnLatest = document.getElementById('btnLatest');
    var btnAll = document.getElementById('btnAll');
    if (btnLatest) btnLatest.className = type === 'latest' ? 'btn btn-primary btn-sm' : 'btn btn-outline btn-sm';
    if (btnAll) btnAll.className = type === 'all' ? 'btn btn-primary btn-sm' : 'btn btn-outline btn-sm';
    var cards = document.querySelectorAll('#servicesContainer .service-item-card');
    cards.forEach(function(card, index) {
        card.style.display = (type === 'latest' && index >= 4) ? 'none' : '';
    });
}

// ==================== تحسينات الأداء ====================

function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    images.forEach(img => observer.observe(img));
}

function preventDoubleClick(btn) {
    btn.disabled = true;
    setTimeout(() => {
        btn.disabled = false;
    }, 2000);
}

// ==================== تهيئة عند التحميل ====================
document.addEventListener('DOMContentLoaded', function() {
    initLazyLoading();
    initStarRating();
    initFieldValidation();
    
    // إغلاق القوائم عند النقر خارجها
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.notif-dropdown-2026') && !e.target.closest('[onclick*="toggleNotifications2026"]')) {
            const notif = document.getElementById('notifDropdown2026');
            if (notif) notif.style.display = 'none';
        }
        if (!e.target.closest('#createMenu2026') && !e.target.closest('[onclick*="toggleCreateMenu2026"]')) {
            const menu = document.getElementById('createMenu2026');
            if (menu) menu.style.display = 'none';
        }
        if (!e.target.closest('.search-advanced')) {
            const results = document.getElementById('searchResultsAdvanced');
            if (results) results.style.display = 'none';
        }
        if (!e.target.closest('.profile-menu-wrapper')) {
            document.getElementById('profileDropdown').style.display = 'none';
        }
    });
});

// ==================== زر العودة للأعلى ====================
document.addEventListener('DOMContentLoaded', function() {
    const scrollBtn = document.createElement('button');
    scrollBtn.id = 'scrollToTopBtn';
    scrollBtn.innerHTML = '⬆';
    scrollBtn.title = 'العودة للأعلى';
    document.body.appendChild(scrollBtn);

    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            scrollBtn.style.display = 'block';
        } else {
            scrollBtn.style.display = 'none';
        }
    });

    scrollBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

// ==================== تأثير تغيير اللغة ====================
document.querySelectorAll('a[href*="switch-locale"]').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        var url = this.href;
        document.body.classList.add('locale-switching');
        setTimeout(function() {
            window.location.href = url;
        }, 280);
    });
});

// ==================== حركات لوحة التحكم ====================
function highlightElement(el) {
    if (!el) return;
    el.classList.add('highlight-anim');
    setTimeout(() => el.classList.remove('highlight-anim'), 600);
}

const observer = new MutationObserver((mutations) => {
    mutations.forEach(mutation => {
        if (mutation.type === 'characterData' || mutation.type === 'childList') {
            const target = mutation.target.closest ? mutation.target.closest('.stat-card-value') : null;
            if (target) highlightElement(target);
        }
    });
});

document.querySelectorAll('.stat-card-value').forEach(el => {
    observer.observe(el, { characterData: true, childList: true, subtree: true });
});

// ===== دالة إظهار/إخفاء التعليقات وصندوق التعليق =====
function toggleComments(postId) {
    var commentsList = document.getElementById('commentsList-' + postId);
    var commentBox = document.getElementById('commentBox-' + postId);
    
    if (commentsList) {
        if (commentsList.style.display === 'none' || commentsList.style.display === '') {
            commentsList.style.display = 'block';
            if (commentBox) commentBox.style.display = 'block';
        } else {
            commentsList.style.display = 'none';
            if (commentBox) commentBox.style.display = 'none';
        }
    }
}

// ===== دالة إظهار نموذج الرد =====
function showReplyForm(event, postId, commentId, commentUser) {
    event.stopPropagation();
    var formDiv = document.getElementById('replyForm-' + commentId);
    if (formDiv) {
        if (formDiv.style.display === 'none' || formDiv.style.display === '') {
            formDiv.style.display = 'block';
            formDiv.innerHTML = `
                <form onsubmit="submitComment(event, ${postId}, ${commentId})" style="display:flex;gap:0.3rem;">
                    <input type="text" name="comment_text" placeholder="رد على ${commentUser}..." required style="flex:1;padding:0.4rem 0.6rem;border:1px solid var(--border);border-radius:2rem;font-family:'Tajawal',sans-serif;font-size:0.75rem;">
                    <button type="submit" class="btn btn-primary btn-sm" style="font-size:0.7rem;padding:0.3rem 0.8rem;">رد</button>
                </form>
            `;
        } else {
            formDiv.style.display = 'none';
            formDiv.innerHTML = '';
        }
    }
}
// ==================== صوت الإشعارات ====================
let notificationSound = null;

function initNotificationSound() {
    notificationSound = new Audio('/assets/sounds/notification.wav');
    notificationSound.volume = 0.7;
}

function playNotificationSound() {
    try {
        if (!notificationSound) {
            initNotificationSound();
        }
        notificationSound.currentTime = 0;
        notificationSound.play().catch(function() {});
    } catch (e) {}
}

let lastUnreadCount = 0;
let firstCheck = true;

function checkNewNotifications() {
    fetch('/api/notifications/unread-count')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            console.log('📋 الإشعارات:', data.unread, '| السابقة:', lastUnreadCount);
            
            // ✅ أول فحص - لا تشغل صوت
            if (firstCheck) {
                lastUnreadCount = data.unread;
                firstCheck = false;
                return;
            }
            
            // ✅ إذا زاد العدد - شغل الصوت
            if (data.unread > lastUnreadCount) {
                playNotificationSound();
                console.log('🔔 إشعار جديد!');
            }
            
            lastUnreadCount = data.unread;
        })
        .catch(function(e) {
            console.log('❌ خطأ:', e);
        });
}

setInterval(checkNewNotifications, 3000);

// ==================== أزرار المتابعة الفورية ====================
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.follow-action-btn, .unfollow-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var userId = this.getAttribute('data-user-id');
            var self = this;
            
            fetch('/follow/' + userId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    if (data.isFollowing) {
                        self.textContent = '✓ متابَع';
                        self.style.background = '#374151';
                        self.style.color = 'white';
                    } else {
                        self.textContent = '+ متابعة';
                        self.style.background = '#2563eb';
                        self.style.color = 'white';
                    }
                    showToast2026(data.isFollowing ? 'تم المتابعة ✅' : 'تم إلغاء المتابعة', 'success');
                }
            });
        });
    });
});
// ==================== المحافظ الإلكترونية ====================

function selectWallet(element, radioInput) {
    document.querySelectorAll('.wallet-card, .wallet-select-item').forEach(function(item) {
        item.classList.remove('selected');
    });
    if (element) element.classList.add('selected');
    if (radioInput) radioInput.checked = true;
}

function initWalletCards() {
    document.querySelectorAll('.wallet-card').forEach(function(card) {
        card.addEventListener('click', function() {
            var radio = card.querySelector('input[type="radio"]');
            if (radio) selectWallet(card, radio);
        });
    });
    document.querySelectorAll('.wallet-select-item').forEach(function(item) {
        item.addEventListener('click', function() {
            var radio = item.querySelector('input[type="radio"]');
            if (radio) selectWallet(item, radio);
        });
    });
    document.querySelectorAll('input[name="wallet_name"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var parent = radio.closest('.wallet-card, .wallet-select-item');
            if (parent) selectWallet(parent, radio);
        });
    });
}

// التحقق من الاسم الرباعي
function validateFullName(input) {
    var value = input.value.trim();
    var words = value.split(/\s+/).filter(function(word) { return word.length > 1; });
    var errorMsg = input.closest('.sender-input-group').querySelector('.sender-error-msg');
    if (words.length < 3) {
        input.classList.add('error');
        if (errorMsg) {
            errorMsg.textContent = '⚠️ يرجى كتابة الاسم الرباعي كاملاً (3 كلمات على الأقل)';
            errorMsg.classList.add('show');
        }
        return false;
    } else {
        input.classList.remove('error');
        if (errorMsg) errorMsg.classList.remove('show');
        return true;
    }
}

// التحقق من رقم الجوال
function validatePhone(input) {
    var value = input.value.trim();
    var errorMsg = input.closest('.sender-input-group').querySelector('.sender-error-msg');
    if (value.length < 9 || !/^[0-9+\s]+$/.test(value)) {
        input.classList.add('error');
        if (errorMsg) {
            errorMsg.textContent = '⚠️ يرجى كتابة رقم الجوال صحيح';
            errorMsg.classList.add('show');
        }
        return false;
    } else {
        input.classList.remove('error');
        if (errorMsg) errorMsg.classList.remove('show');
        return true;
    }
}

// التحقق قبل الإرسال
function validatePaymentForm(form) {
    var nameInput = form.querySelector('input[name="sender_name"]');
    var phoneInput = form.querySelector('input[name="sender_phone"]');
    if (!nameInput || !phoneInput) return true;
    var validName = validateFullName(nameInput);
    var validPhone = validatePhone(phoneInput);
    return validName && validPhone;
}

document.addEventListener('DOMContentLoaded', function() {
    initWalletCards();
    
    // ربط التحقق بالنماذج
    document.querySelectorAll('input[name="sender_name"]').forEach(function(input) {
        input.addEventListener('blur', function() { validateFullName(input); });
        input.addEventListener('input', function() {
            if (input.classList.contains('error')) validateFullName(input);
        });
    });
    
    document.querySelectorAll('input[name="sender_phone"]').forEach(function(input) {
        input.addEventListener('blur', function() { validatePhone(input); });
        input.addEventListener('input', function() {
            if (input.classList.contains('error')) validatePhone(input);
        });
    });
});