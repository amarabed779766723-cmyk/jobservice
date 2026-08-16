// assets/js/settings.js

document.addEventListener('DOMContentLoaded', function() {

    // ========== تطبيق الوضع الليلي الفوري ==========
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', function() {
            const isDark = this.checked;
            // تغيير الوضع فوراً
            document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
            // حفظ الإعداد عبر AJAX
            saveSetting('dark_mode', isDark);
            // تأثير بصري
            playToggleEffect(this);
        });
    }

    // ========== صوت الإشعارات ==========
    const soundToggle = document.getElementById('soundToggle');
    if (soundToggle) {
        soundToggle.addEventListener('change', function() {
            saveSetting('notification_sound', this.checked);
            playToggleEffect(this);
        });
    }

    // ========== الخصوصية ==========
    const privacySelect = document.getElementById('privacySelect');
    if (privacySelect) {
        privacySelect.addEventListener('change', function() {
            saveSetting('privacy', this.value);
            showToast('تم تحديث الخصوصية');
        });
    }

    // ========== تطبيق الوضع المحفوظ عند تحميل الصفحة ==========
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        if (darkModeToggle) darkModeToggle.checked = true;
    }

    // ========== دالة حفظ الإعداد عبر AJAX ==========
    function saveSetting(key, value) {
        fetch('settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=ajax_preference&key=' + encodeURIComponent(key) + '&value=' + encodeURIComponent(value)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('تم حفظ الإعداد:', key);
                // حفظ الوضع محلياً أيضاً للاستمرارية
                if (key === 'dark_mode') {
                    localStorage.setItem('theme', value ? 'dark' : 'light');
                }
            }
        })
        .catch(err => console.error('خطأ في الحفظ:', err));
    }

    // ========== تأثير بصري عند التبديل ==========
    function playToggleEffect(toggle) {
        const span = toggle.nextElementSibling;
        if (span) {
            span.style.transform = 'scale(1.1)';
            setTimeout(() => { span.style.transform = 'scale(1)'; }, 150);
        }
    }

    // ========== رسالة تنبيه صغيرة (Toast) ==========
    function showToast(message) {
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.style.cssText = 'position:fixed; bottom:2rem; left:50%; transform:translateX(-50%); background:#333; color:white; padding:0.75rem 1.5rem; border-radius:2rem; z-index:9999; transition:0.3s; opacity:0;';
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        toast.style.opacity = '1';
        setTimeout(() => { toast.style.opacity = '0'; }, 2500);
    }

});