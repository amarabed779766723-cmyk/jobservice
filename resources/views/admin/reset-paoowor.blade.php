<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعيين كلمة مرور جديدة - Job Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #1e40af 50%, #1e293b 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            direction: rtl; padding: 1rem;
        }
        .login-container {
            background: white; border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            padding: 3rem 2.5rem; width: 100%; max-width: 420px; text-align: center;
        }
        .login-logo {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem; font-size: 2rem; color: white;
        }
        .login-title { font-size: 1.5rem; font-weight: 900; color: #1e293b; margin-bottom: 0.5rem; }
        .login-subtitle { font-size: 0.9rem; color: #64748b; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.25rem; text-align: right; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }
        .form-group input {
            width: 100%; padding: 0.9rem 1rem; border: 2px solid #e5e7eb;
            border-radius: 12px; font-size: 1rem; font-family: 'Tajawal', sans-serif;
            transition: 0.3s; outline: none; background: #f9fafb;
        }
        .form-group input:focus { border-color: #2563eb; background: white; }
        .btn-login {
            width: 100%; padding: 0.9rem; border: none;
            border-radius: 12px; font-size: 1.1rem; font-weight: 700;
            cursor: pointer; font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white; margin-top: 0.5rem;
        }
        .error-msg { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.75rem; border-radius: 10px; margin-bottom: 1rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">🔐</div>
        <div class="login-title">تعيين كلمة مرور جديدة</div>
        <div class="login-subtitle">أدخل كلمة المرور الجديدة</div>

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <div class="form-group">
                <label>كلمة المرور الجديدة</label>
                <input type="password" name="password" placeholder="••••••••" required minlength="8">
            </div>
            <div class="form-group">
                <label>تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" placeholder="••••••••" required minlength="8">
            </div>
            <button type="submit" class="btn-login">💾 حفظ كلمة المرور</button>
        </form>
    </div>
</body>
</html>