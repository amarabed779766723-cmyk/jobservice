@extends('layouts.auth')
@section('title', 'تسجيل الدخول - Job Service')
@section('content')
<div class="auth-page">
    <div class="auth-box" style="max-width: 480px;">
       <img src="{{ asset('assets/branding/logo-full.png') }}" alt="JOB SERVICE" class="branding-logo auth">
        <div class="brand-slogan">أهلاً بعودتك</div>

        {{-- رسالة الحظر --}}
        @if(session('banned_email'))
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">🚫</div>
                <h3 style="color: #dc2626; margin-bottom: 0.5rem;">تم حظر الحساب</h3>
                <p style="color: #4b5563; margin-bottom: 1rem;">{{ session('banned_message') }}</p>
                @if(session('note_sent'))
                    <div class="success-msg">✅ تم إرسال ملاحظتك للإدارة.</div>
                @else
                    <form method="POST" action="{{ route('banned.note') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('banned_email') }}">
                        <textarea name="message" rows="3" placeholder="اكتب ملاحظتك هنا..." required style="width:100%; padding:0.5rem; border:1px solid #d1d5db; border-radius:8px; margin-bottom:0.5rem; font-family:'Tajawal',sans-serif;"></textarea>
                        <button type="submit" class="btn btn-primary" style="width:100%;">إرسال الملاحظة</button>
                    </form>
                @endif
            </div>
        @endif

        {{-- رسالة الإيقاف المؤقت --}}
        @if(session('suspended_email'))
            <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">⏸️</div>
                <h3 style="color: #92400e; margin-bottom: 0.5rem;">حسابك موقّف مؤقتاً</h3>
                <p style="color: #4b5563; margin-bottom: 1rem;">{{ session('suspended_message') }}</p>
                <form method="POST" action="{{ route('account.activate') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('suspended_email') }}">
                    <button type="submit" style="width:100%; padding:0.85rem; background:#2563eb; color:white; border:none; border-radius:0.75rem; font-size:1rem; font-weight:600; cursor:pointer;">✅ تفعيل الحساب الآن</button>
                </form>
            </div>
        @endif

        {{-- أخطاء الدخول --}}
        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="input-group"><label>البريد الإلكتروني</label><input type="email" name="email" value="{{ old('email') }}" required></div>
            <div class="input-group"><label>كلمة المرور</label><input type="password" name="password" required></div>
            <label style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;"><input type="checkbox" name="remember"> تذكرني</label>
            <button type="submit" class="btn-primary" style="width:100%;">تسجيل الدخول</button>
        </form>

        <div style="text-align: center; margin-top: 1rem;">
            <a href="{{ route('password.request') }}" style="color: #2563eb; text-decoration: none; font-size: 0.9rem;">🔑 نسيت كلمة المرور؟</a>
        </div>
        <div class="auth-link">ليس لديك حساب؟ <a href="{{ route('register') }}">أنشئ حساباً</a></div>
    </div>
</div>
@endsection