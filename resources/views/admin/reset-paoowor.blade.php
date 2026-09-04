<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعيين كلمة مرور جديدة - Job Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
    <div class="reset-password-page">
        <div class="reset-password-container">
            <div class="reset-password-logo">🔐</div>
            <div class="reset-password-title">تعيين كلمة مرور جديدة</div>
            <div class="reset-password-subtitle">أدخل كلمة المرور الجديدة</div>

            @if($errors->any())
                <div class="error-msg">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.password.new') }}" class="reset-password-form">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? '' }}">
                
                <div class="form-group">
                    <label>كلمة المرور الجديدة</label>
                    <input type="password" name="password" placeholder="••••••••" required minlength="8">
                </div>
                <div class="form-group">
                    <label>تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••" required minlength="8">
                </div>
                <button type="submit" class="reset-password-btn">💾 حفظ كلمة المرور</button>
            </form>
        </div>
    </div>
</body>
</html>