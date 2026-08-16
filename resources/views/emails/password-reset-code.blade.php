<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور</title>
    <style>
        body { font-family: 'Tajawal', Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 40px 20px; direction: rtl; }
        .container { max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 40px 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
        .logo { font-size: 2rem; font-weight: 900; color: #2563eb; margin-bottom: 0.5rem; }
        h2 { color: #1c1e21; font-size: 1.5rem; margin-bottom: 0.5rem; }
        p { color: #65676b; line-height: 1.8; margin-bottom: 1.5rem; }
        .code-box { background: #f0f2f5; padding: 1rem; border-radius: 12px; font-size: 2.5rem; font-weight: 700; color: #2563eb; letter-spacing: 8px; margin: 1.5rem 0; direction: ltr; }
        .footer { margin-top: 2rem; font-size: 0.8rem; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 1.5rem; }
        .footer a { color: #2563eb; text-decoration: none; }
        .warning { background: #fef3c7; color: #92400e; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem; margin: 1rem 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🔐 Job Service</div>
        <h2>🔑 إعادة تعيين كلمة المرور</h2>
        <p>
            لقد تلقينا طلباً لإعادة تعيين كلمة المرور لحسابك في <strong>Job Service</strong>.
            <br>
            استخدم الكود التالي لإكمال عملية إعادة التعيين:
        </p>

        <div class="code-box">{{ $code }}</div>

        <div class="warning">
            ⏳ هذا الكود صالح لمدة 10 دقائق فقط.
        </div>

        <p style="font-size: 0.85rem; color: #9ca3af;">
            إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا البريد.
        </p>

        <div class="footer">
            © {{ date('Y') }} Job Service - منصة خدمات مهنية يمنية
            <br>
            <a href="{{ url('/') }}">{{ url('/') }}</a>
        </div>
    </div>
</body>
</html>