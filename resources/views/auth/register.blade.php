@extends('layouts.auth')

@section('title', 'إنشاء حساب - Job Service')

@section('content')
<div class="auth-page">
    <div class="auth-box">
        <div class="brand-logo">Job Service</div>
        <div class="brand-slogan">أنشئ حسابك المهني</div>

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf
            <div class="input-group">
                <label>الاسم الكامل</label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                       placeholder="مثال: عمار عابد"
                       pattern="^[\p{Arabic}\p{Latin}\s]+$"
                       title="الاسم يجب أن يحتوي على حروف فقط">
                <small style="color:var(--text-secondary);">حروف عربية أو إنجليزية فقط</small>
            </div>
            <div class="input-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                       placeholder="example@domain.com"
                       title="أدخل بريد إلكتروني صحيح">
            </div>
            <div class="input-group">
                <label>رقم الجوال</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required 
                       placeholder="+967xxxxxxxxx"
                       pattern="^\+967\d{9}$"
                       minlength="13" maxlength="13"
                       title="رقم الجوال يجب أن يبدأ بـ +967 ويتكون من 9 أرقام بعده">
                <small style="color:var(--text-secondary);">مثال: +967771234567</small>
            </div>
            <div class="input-group">
                <label>تاريخ الميلاد</label>
                <input type="date" name="birthdate" value="{{ old('birthdate') }}" required 
                       max="{{ date('Y-m-d', strtotime('-16 years')) }}"
                       title="يجب أن يكون عمرك 16 سنة فأكثر">
                <small style="color:var(--text-secondary);">العمر المسموح: 16 سنة فأكثر</small>
            </div>
            <div class="input-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" id="password" required 
                       minlength="8"
                       pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$"
                       title="حرف كبير + حرف صغير + رقم، 8 خانات على الأقل">
                <small style="color:var(--text-secondary);">8 خانات: حرف كبير + حرف صغير + رقم</small>
                <div id="passwordStrength" style="height:3px; margin-top:0.25rem; border-radius:3px; transition:0.3s;"></div>
            </div>
            <div class="input-group">
                <label>تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>

            <label style="font-weight:500; display:block; margin-bottom:0.5rem;">نوع الحساب</label>
            <div class="type-selector">
                <input type="radio" id="clientRadio" name="user_type" value="client" checked hidden>
                <label for="clientRadio" class="type-btn active" onclick="selectType('client')">باحث عن خدمات</label>

                <input type="radio" id="providerRadio" name="user_type" value="provider" hidden>
                <label for="providerRadio" class="type-btn" onclick="selectType('provider')">مقدم خدمات</label>
            </div>

            <button type="submit" class="btn-primary" style="width:100%; margin-top:1rem;">إنشاء حساب</button>
        </form>

        <div class="auth-link">
            لديك حساب؟ <a href="{{ route('login') }}">سجل دخولك</a>
        </div>
    </div>
</div>

<script>
function selectType(type) {
    document.getElementById(type === 'client' ? 'clientRadio' : 'providerRadio').checked = true;
    document.getElementById('clientRadio').parentElement.querySelector('.type-btn').classList.toggle('active', type === 'client');
    document.getElementById('providerRadio').parentElement.querySelector('.type-btn').classList.toggle('active', type === 'provider');
}

// قوة كلمة المرور
document.getElementById('password').addEventListener('input', function() {
    const pass = this.value;
    const bar = document.getElementById('passwordStrength');
    let strength = 0;
    if (pass.length >= 8) strength++;
    if (/[a-z]/.test(pass)) strength++;
    if (/[A-Z]/.test(pass)) strength++;
    if (/\d/.test(pass)) strength++;
    
    const colors = ['#ef4444', '#f97316', '#eab308', '#10b981'];
    const widths = ['25%', '50%', '75%', '100%'];
    bar.style.width = widths[strength-1] || '0%';
    bar.style.background = colors[strength-1] || 'transparent';
});

// تأكيد تطابق كلمة المرور
document.getElementById('password_confirmation').addEventListener('input', function() {
    const pass = document.getElementById('password').value;
    this.style.borderColor = this.value === pass ? '#10b981' : '#ef4444';
});

// منع لصق في حقل الاسم
document.querySelector('input[name="name"]').addEventListener('paste', function(e) {
    const pasted = (e.clipboardData || window.clipboardData).getData('text');
    if (/[0-9]/.test(pasted)) {
        e.preventDefault();
        alert('الاسم يجب أن يحتوي على حروف فقط');
    }
});
</script>
@endsection