@extends('layouts.auth')

@section('title', 'إعادة تعيين كلمة المرور - Job Service')

@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f0f2f5; padding: 1rem;">
    <div style="background: white; border-radius: 1.5rem; padding: 2.5rem; width: 100%; max-width: 440px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
        
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="font-size: 2.5rem;">🔄</div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1c1e21; margin-bottom: 0.5rem;">إعادة تعيين كلمة المرور</h2>
            <p style="color: #65676b; font-size: 0.9rem;">
                أدخل كلمة مرور جديدة لحسابك.
            </p>
        </div>

        @if(session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email ?? session('reset_verified_email') }}">

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">
                    كلمة المرور الجديدة
                </label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="********"
                    style="width: 100%; padding: 0.85rem 1rem; border: 1px solid #d1d5db; border-radius: 0.75rem; font-size: 1rem; font-family: 'Tajawal', sans-serif;"
                    required
                >
                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.3rem;">
                    يجب أن تحتوي على 8 خانات على الأقل + حرف كبير + حرف صغير + رقم
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.4rem; color: #374151;">
                    تأكيد كلمة المرور
                </label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="********"
                    style="width: 100%; padding: 0.85rem 1rem; border: 1px solid #d1d5db; border-radius: 0.75rem; font-size: 1rem; font-family: 'Tajawal', sans-serif;"
                    required
                >
            </div>

            <button type="submit" style="width: 100%; padding: 0.85rem; background: #2563eb; color: white; border: none; border-radius: 0.75rem; font-size: 1rem; font-weight: 600; cursor: pointer; font-family: 'Tajawal', sans-serif;">
                ✅ تحديث كلمة المرور
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="{{ route('login') }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                ← العودة إلى تسجيل الدخول
            </a>
        </div>
    </div>
</div>
@endsection