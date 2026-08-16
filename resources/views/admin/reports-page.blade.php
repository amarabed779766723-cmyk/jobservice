@extends('layouts.admin')
@section('title', 'التقارير - لوحة التحكم')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <div class="admin-header"><h1>📄 التقارير</h1></div>
        <div style="display:flex; gap:1rem; margin-bottom:1rem;">
    <a href="{{ route('admin.reports.export') }}" class="btn btn-success">📥 تصدير Excel</a>
</div>

        {{-- تقرير شهري --}}
        <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="margin-bottom:1rem;">📅 التقرير الشهري</h3>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>الشهر</th>
                            <th>👥 مستخدمين جدد</th>
                            <th>🛠️ خدمات</th>
                            <th>📋 طلبات</th>
                            <th>📢 منشورات</th>
                            <th>💰 الإيرادات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyStats as $month => $stats)
                        <tr>
                            <td><strong>{{ $month }}</strong></td>
                            <td>{{ $stats['users'] }}</td>
                            <td>{{ $stats['services'] }}</td>
                            <td>{{ $stats['requests'] }}</td>
                            <td>{{ $stats['posts'] }}</td>
                            <td>{{ number_format($stats['revenue'], 2) }} ر.س</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- أكثر المستخدمين نشاطاً --}}
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
            <div class="card">
                <h3 style="margin-bottom:1rem;">🏆 أكثر المستخدمين نشاطاً (الشهر الحالي)</h3>
                <table class="admin-table">
                    <thead><tr><th>#</th><th>المستخدم</th><th>النشاطات</th></tr></thead>
                    <tbody>
                        @foreach($topUsers as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->activity_logs_count }} نشاط</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- أكثر الخدمات طلباً --}}
            <div class="card">
                <h3 style="margin-bottom:1rem;">🔥 أكثر الخدمات طلباً</h3>
                <table class="admin-table">
                    <thead><tr><th>#</th><th>الخدمة</th><th>العروض</th></tr></thead>
                    <tbody>
                        @foreach($topServices as $index => $service)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $service->title }}</td>
                            <td>{{ $service->offers_count }} عرض</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection