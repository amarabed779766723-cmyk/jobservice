@extends('layouts.admin')
@section('title', 'النسخ الاحتياطي - لوحة التحكم')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <div class="admin-header"><h1>💾 النسخ الاحتياطي والاستعادة</h1></div>

        @if(session('success'))
            <div class="success-msg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error-msg">{{ session('error') }}</div>
        @endif

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
            {{-- إنشاء نسخة --}}
            <div class="card">
                <h3 style="margin-bottom:1rem;">📥 إنشاء نسخة احتياطية</h3>
                <p style="color:var(--text-secondary); margin-bottom:1rem;">تحميل نسخة من قاعدة البيانات كاملة.</p>
                <a href="{{ route('admin.backup.download') }}" class="btn btn-primary">⬇️ تحميل النسخة الاحتياطية</a>
            </div>

            {{-- استعادة نسخة --}}
            <div class="card">
                <h3 style="margin-bottom:1rem;">📤 استعادة نسخة احتياطية</h3>
                <p style="color:var(--text-secondary); margin-bottom:1rem;">رفع ملف SQL لاستعادة البيانات.</p>
                <form method="POST" action="{{ route('admin.backup.restore') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="backup_file" accept=".sql" required>
                    <button type="submit" class="btn btn-success" style="margin-top:0.5rem;" onclick="return confirm('تحذير: سيتم استبدال جميع البيانات الحالية! متأكد؟')">📤 استعادة</button>
                </form>
            </div>
        </div>

        {{-- النسخ السابقة --}}
        @if(count($backups) > 0)
        <div class="card" style="margin-top:1.5rem;">
            <h3 style="margin-bottom:1rem;">📂 النسخ الاحتياطية السابقة</h3>
            <table class="admin-table">
                <thead><tr><th>اسم الملف</th><th>الحجم</th><th>التاريخ</th></tr></thead>
                <tbody>
                    @foreach($backups as $backup)
                    <tr>
                        <td>{{ $backup['name'] }}</td>
                        <td>{{ $backup['size'] }} KB</td>
                        <td>{{ $backup['date'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </main>
</div>
@endsection