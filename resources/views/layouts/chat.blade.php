<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'الدردشة')</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        :root {
            --primary: #2563eb;
            --bg: #f0f2f5;
            --bg-card: #ffffff;
            --text: #1c1e21;
            --text-secondary: #65676b;
            --border: #e5e7eb;
            --shadow: 0 1px 2px rgba(0,0,0,0.08);
            --radius: 12px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Tajawal', sans-serif; background: var(--bg); color: var(--text); direction: rtl; }
        .btn { display: inline-block; padding: 0.6rem 1.5rem; border-radius: 2rem; font-weight: 600; font-size: 0.95rem; text-decoration: none; cursor: pointer; border: none; font-family: 'Tajawal', sans-serif; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { background: var(--bg); color: var(--text); border: 1px solid var(--border); }
        .btn-sm { padding: 0.35rem 1rem; font-size: 0.8rem; }
        .card { background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 1.25rem; margin-bottom: 1rem; border: 1px solid var(--border); }
        .input-group { margin-bottom: 1.25rem; }
        .input-group input { width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--border); border-radius: 0.75rem; font-size: 1rem; font-family: 'Tajawal', sans-serif; background: var(--bg); color: var(--text); outline: none; }
    </style>
</head>
<body>
    @yield('content')
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>