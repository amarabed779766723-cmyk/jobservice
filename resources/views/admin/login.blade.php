<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>دخول الأدمن - Job Service</title>
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
            padding: 3rem 2.5rem; width: 100%; max-width: 420px;
            text-align: center; animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .login-logo {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem; font-size: 2rem; color: white; box-shadow: 0 8px 25px rgba(37,99,235,0.3);
        }
        .login-title { font-size: 1.6rem; font-weight: 900; color: #1e293b; margin-bottom: 0.25rem; }
        .login-subtitle { font-size: 0.9rem; color: #64748b; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.25rem; text-align: right; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }
        .form-group input {
            width: 100%; padding: 0.9rem 1rem; border: 2px solid #e5e7eb;
            border-radius: 12px; font-size: 1rem; font-family: 'Tajawal', sans-serif;
            transition: 0.3s; outline: none; background: #f9fafb;
        }
        .form-group input:focus { border-color: #2563eb; background: white; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .btn-login {
            width: 100%; padding: 0.9rem; border: none;
            border-radius: 12px; font-size: 1.1rem; font-weight: 700;
            cursor: pointer; font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white; transition: 0.3s; margin-top: 0.5rem;
            box-shadow: 0 4px 15px rgba(37,99,235,0.3);
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,99,235,0.4); }
        .forgot-link {
            display: block; text-align: center; margin-top: 1.5rem;
            color: #2563eb; text-decoration: none; font-size: 0.9rem; font-weight: 500;
            cursor: pointer;
        }
        .forgot-link:hover { text-decoration: underline; }
        .error-msg { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.75rem; border-radius: 10px; margin-bottom: 1rem; font-size: 0.9rem; }
        .success-msg { background: #d1fae5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.75rem; border-radius: 10px; margin-bottom: 1rem; font-size: 0.9rem; }
        
        .code-inputs { display: flex; gap: 10px; justify-content: center; margin-bottom: 1.5rem; direction: ltr; }
        .code-inputs input {
            width: 50px; height: 60px; text-align: center;
            font-size: 1.5rem; font-weight: 700;
            border: 2px solid #e5e7eb; border-radius: 12px;
            font-family: 'Tajawal', sans-serif;
        }
        .code-inputs input:focus { border-color: #2563eb; outline: none; }
        .msg-box { padding: 0.75rem; border-radius: 10px; margin-bottom: 1rem; font-size: 0.9rem; display: none; }
    </style>
</head>
<body>

    <div id="msgBox" class="msg-box"></div>

    {{-- صفحة الدخول --}}
    <div class="login-container" id="loginBox">
        <div class="login-logo">🛡️</div>
        <div class="login-title">لوحة التحكم</div>
        <div class="login-subtitle">تسجيل دخول المدير</div>

        @if(session('success'))
            <div class="success-msg">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        {{-- ✅ تم إضافة @csrf هنا --}}
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" placeholder="admin@example.com" required>
            </div>
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">🚀 تسجيل الدخول</button>
        </form>

        <a class="forgot-link" onclick="showForgot()">نسيت كلمة المرور؟</a>
    </div>

    {{-- صفحة استعادة كلمة المرور --}}
    <div class="login-container" id="forgotBox" style="display:none;">
        <div class="login-logo">🔑</div>
        <div class="login-title">استعادة كلمة المرور</div>
        <div class="login-subtitle">أدخل بريدك الإلكتروني وسنرسل لك كود التحقق</div>

        <form onsubmit="sendResetCode(event)">
            @csrf
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" id="resetEmail" placeholder="admin@example.com" required>
            </div>
            <button type="submit" class="btn-login">📧 إرسال الكود</button>
        </form>

        <a class="forgot-link" onclick="showLogin()">← العودة لتسجيل الدخول</a>
    </div>

    {{-- صفحة إدخال الكود --}}
    <div class="login-container" id="verifyBox" style="display:none;">
        <div class="login-logo">🔢</div>
        <div class="login-title">التحقق من الكود</div>
        <div class="login-subtitle">أدخل الكود المكون من 6 أرقام</div>

        <form onsubmit="verifyCode(event)">
            @csrf
            <div class="code-inputs">
                <input type="text" maxlength="1" class="code-digit" id="digit1">
                <input type="text" maxlength="1" class="code-digit" id="digit2">
                <input type="text" maxlength="1" class="code-digit" id="digit3">
                <input type="text" maxlength="1" class="code-digit" id="digit4">
                <input type="text" maxlength="1" class="code-digit" id="digit5">
                <input type="text" maxlength="1" class="code-digit" id="digit6">
            </div>
            <input type="hidden" name="code" id="fullCode">
            <button type="submit" class="btn-login">✅ تحقق</button>
        </form>

        <a class="forgot-link" onclick="showForgot()">← إعادة إرسال الكود</a>
    </div>

    {{-- صفحة كلمة المرور الجديدة --}}
    <div class="login-container" id="newPasswordBox" style="display:none;">
        <div class="login-logo">🔐</div>
        <div class="login-title">كلمة مرور جديدة</div>
        <div class="login-subtitle">أدخل كلمة المرور الجديدة</div>

        <form onsubmit="saveNewPassword(event)">
            @csrf
            <div class="form-group">
                <label>كلمة المرور الجديدة</label>
                <input type="password" name="password" id="newPassword" placeholder="••••••••" required minlength="8">
            </div>
            <div class="form-group">
                <label>تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" id="newPasswordConfirm" placeholder="••••••••" required minlength="8">
            </div>
            <button type="submit" class="btn-login">💾 حفظ</button>
        </form>
    </div>

    <script>
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var resetEmail = '';

    function showMsg(msg, type) {
        var box = document.getElementById('msgBox');
        box.textContent = msg;
        box.style.display = 'block';
        box.className = 'msg-box ' + (type === 'success' ? 'success-msg' : 'error-msg');
        setTimeout(function() { box.style.display = 'none'; }, 6000);
    }

    function showForgot() {
        document.getElementById('loginBox').style.display = 'none';
        document.getElementById('forgotBox').style.display = 'block';
        document.getElementById('verifyBox').style.display = 'none';
        document.getElementById('newPasswordBox').style.display = 'none';
    }

    function showLogin() {
        document.getElementById('loginBox').style.display = 'block';
        document.getElementById('forgotBox').style.display = 'none';
        document.getElementById('verifyBox').style.display = 'none';
        document.getElementById('newPasswordBox').style.display = 'none';
    }

    function showVerify() {
        document.getElementById('loginBox').style.display = 'none';
        document.getElementById('forgotBox').style.display = 'none';
        document.getElementById('verifyBox').style.display = 'block';
        document.getElementById('newPasswordBox').style.display = 'none';
    }

    function showNewPassword() {
        document.getElementById('loginBox').style.display = 'none';
        document.getElementById('forgotBox').style.display = 'none';
        document.getElementById('verifyBox').style.display = 'none';
        document.getElementById('newPasswordBox').style.display = 'block';
    }

    function sendResetCode(e) {
        e.preventDefault();
        resetEmail = document.getElementById('resetEmail').value;
        
        var formData = new FormData();
        formData.append('email', resetEmail);
        formData.append('_token', csrf);

        fetch('{{ route("admin.password.email") }}', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showMsg('✅ الكود: ' + data.code, 'success');
                showVerify();
            } else {
                showMsg(data.message || 'خطأ', 'error');
            }
        });
    }

    function verifyCode(e) {
        e.preventDefault();
        var code = '';
        for (var i = 1; i <= 6; i++) {
            code += document.getElementById('digit' + i).value;
        }

        var formData = new FormData();
        formData.append('email', resetEmail);
        formData.append('code', code);
        formData.append('_token', csrf);

        fetch('{{ route("admin.password.verify") }}', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showNewPassword();
            } else {
                showMsg('كود غير صحيح', 'error');
            }
        });
    }

    function saveNewPassword(e) {
        e.preventDefault();
        var pass = document.getElementById('newPassword').value;
        var confirm = document.getElementById('newPasswordConfirm').value;

        if (pass !== confirm) {
            showMsg('كلمة المرور غير متطابقة', 'error');
            return;
        }

        var formData = new FormData();
        formData.append('email', resetEmail);
        formData.append('password', pass);
        formData.append('password_confirmation', confirm);
        formData.append('_token', csrf);

        fetch('{{ route("admin.password.new") }}', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showMsg('تم تغيير كلمة المرور بنجاح', 'success');
                setTimeout(function() { showLogin(); }, 40000);
            } else {
                showMsg('خطأ', 'error');
            }
        });
    }
    </script>
</body>
</html>