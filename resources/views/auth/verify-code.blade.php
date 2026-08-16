@extends('layouts.auth')

@section('title', 'تفعيل الحساب - Job Service')

@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f0f2f5; padding: 1rem;">
    <div style="background: white; border-radius: 1.5rem; padding: 2.5rem; width: 100%; max-width: 440px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); text-align: center; position: relative;">

        @if(session('verification_code'))
            <div style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; background: #1f2937; color: white; padding: 1rem 2rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); text-align: center; max-width: 90%; animation: slideDown 0.5s ease;">
                <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">🔑</div>
                <div style="font-size: 1.1rem; font-weight: 600;">كود التفعيل الخاص بك هو:</div>
                <div style="font-size: 2rem; font-weight: 900; color: #60a5fa; letter-spacing: 4px; margin-top: 0.25rem; direction: ltr;">
                    {{ session('verification_code') }}
                </div>
                <div style="font-size: 0.8rem; color: #9ca3af; margin-top: 0.25rem;">
                    ⏳ صالح لمدة 10 دقائق
                </div>
            </div>
        @endif

        <style>
            @keyframes slideDown {
                from { opacity: 0; transform: translateX(-50%) translateY(-20px); }
                to { opacity: 1; transform: translateX(-50%) translateY(0); }
            }
        </style>

        <div style="font-size: 3rem; margin-bottom: 1rem;">📧</div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #1c1e21; margin-bottom: 0.5rem;">تفعيل حسابك</h2>
        <p style="color: #65676b; margin-bottom: 0.5rem;">
            تم إرسال كود التفعيل إلى بريدك الإلكتروني:
        </p>
        <p style="color: #2563eb; font-weight: 600; margin-bottom: 1.5rem;">
            {{ $email ?? session('verification_email') }}
        </p>

        @if(session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                ❌ {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('verify.code') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email ?? session('verification_email') }}">
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">
                    كود التفعيل
                </label>
                <input 
                    type="text" 
                    name="code" 
                    placeholder="أدخل الكود المكون من 6 أرقام"
                    maxlength="6"
                    style="width: 100%; padding: 0.85rem 1rem; border: 1px solid #d1d5db; border-radius: 0.75rem; font-size: 1.2rem; text-align: center; letter-spacing: 4px; font-family: 'Tajawal', sans-serif;"
                    required
                >
                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.3rem;">
                    الكود صالح لمدة 10 دقائق
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 0.85rem; background: #2563eb; color: white; border: none; border-radius: 0.75rem; font-size: 1rem; font-weight: 600; cursor: pointer; font-family: 'Tajawal', sans-serif;">
                ✅ تفعيل الحساب
            </button>
        </form>

        <div style="margin-top: 1.5rem;">
            <form method="POST" action="{{ route('verify.resend') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? session('verification_email') }}">
                <button type="submit" style="background: transparent; border: none; color: #2563eb; font-size: 0.9rem; cursor: pointer; font-family: 'Tajawal', sans-serif;">
                    🔄 لم يصلك الكود؟ إعادة إرسال
                </button>
            </form>
        </div>

        <div style="margin-top: 1rem;">
            <a href="{{ route('login') }}" style="color: #9ca3af; text-decoration: none; font-size: 0.85rem;">
                ← العودة إلى تسجيل الدخول
            </a>
        </div>
    </div>
</div>
@endsection