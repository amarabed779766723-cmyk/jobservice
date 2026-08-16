@extends('layouts.auth')
@section('title', 'نسيت كلمة المرور - Job Service')
@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f0f2f5; padding: 1rem;">
    <div style="background: white; border-radius: 1.5rem; padding: 2.5rem; width: 100%; max-width: 440px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); text-align: center;">
        
        <div style="font-size: 3rem; margin-bottom: 1rem;">🔑</div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #1c1e21; margin-bottom: 0.5rem;">نسيت كلمة المرور؟</h2>
        <p style="color: #65676b; margin-bottom: 1.5rem;">أدخل بريدك الإلكتروني وسنرسل لك كود إعادة التعيين.</p>

        @if(session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">✅ {{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">❌ {{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.send.code') }}">
            @csrf
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">البريد الإلكتروني</label>
                <input type="email" name="email" placeholder="example@email.com" required
                       style="width: 100%; padding: 0.85rem 1rem; border: 1px solid #d1d5db; border-radius: 0.75rem; font-size: 1rem; font-family: 'Tajawal', sans-serif;">
            </div>
            <button type="submit" style="width: 100%; padding: 0.85rem; background: #2563eb; color: white; border: none; border-radius: 0.75rem; font-size: 1rem; font-weight: 600; cursor: pointer; font-family: 'Tajawal', sans-serif;">📧 إرسال الكود</button>
        </form>

        <div style="margin-top: 1.5rem;">
            <a href="{{ route('login') }}" style="color: #2563eb; text-decoration: none; font-size: 0.9rem;">← العودة إلى تسجيل الدخول</a>
        </div>
    </div>
</div>
@endsection