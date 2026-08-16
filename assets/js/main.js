// ==================== الأدوات المساعدة ====================
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

// 1. اختيار نوع الحساب
function setUserType(type) {
    const input = document.getElementById('userType');
    if (input) input.value = type;
    document.getElementById('clientBtn')?.classList.toggle('active', type === 'client');
    document.getElementById('providerBtn')?.classList.toggle('active', type === 'provider');
}

// 2. نجوم التقييم التفاعلية
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

// 3. الوضع الليلي (تطبيق فوري على كل الصفحات)
function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
}
function toggleDarkMode(checkbox) {
    const isDark = checkbox.checked;
    applyTheme(isDark ? 'dark' : 'light');
    // حفظ بالإعدادات عبر AJAX
    fetch('settings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=ajax_preference&key=dark_mode&value=' + isDark
    });
}
// تطبيق الوضع المحفوظ فور تحميل الصفحة
(function() {
    var saved = localStorage.getItem('theme');
    if (saved === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
    }
})();

// 4. تأكيد الحذف
function confirmDelete(message = 'هل أنت متأكد؟') {
    return confirm(message);
}

// 5. التحقق المباشر من الحقول (register.php)
function initFieldValidation() {
    const nameInput = document.querySelector('input[name="name"]');
    const emailInput = document.querySelector('input[name="email"]');
    const passInput = document.querySelector('input[name="password"]');
    if (nameInput) {
        nameInput.addEventListener('input', () => {
            const valid = /^[\p{Arabic}a-zA-Z\s]+$/u.test(nameInput.value);
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

document.addEventListener('DOMContentLoaded', () => {
    initStarRating();
    initFieldValidation();
});