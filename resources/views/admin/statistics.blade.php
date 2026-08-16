@extends('layouts.admin')
@section('title', 'الإحصائيات - لوحة التحكم')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <div class="admin-header"><h1>📊 الإحصائيات </h1></div>

        <div class="stat-cards">
            <div class="stat-card"><div class="stat-card-value">{{ $totalUsers }}</div><div class="stat-card-label">👥 إجمالي المستخدمين</div></div>
            <div class="stat-card"><div class="stat-card-value">{{ $totalServices }}</div><div class="stat-card-label">🛠️ الخدمات</div></div>
            <div class="stat-card"><div class="stat-card-value">{{ $totalRequests }}</div><div class="stat-card-label">📋 الطلبات</div></div>
            <div class="stat-card"><div class="stat-card-value">{{ $totalOffers }}</div><div class="stat-card-label">💵 العروض</div></div>
            <div class="stat-card"><div class="stat-card-value">{{ $totalRatings }}</div><div class="stat-card-label">⭐ التقييمات</div></div>
            <div class="stat-card"><div class="stat-card-value">{{ $avgRating }}</div><div class="stat-card-label">📊 متوسط التقييم</div></div>
            <div class="stat-card"><div class="stat-card-value">{{ number_format($totalRevenue, 2) }}</div><div class="stat-card-label">💰 إجمالي الإيرادات</div></div>
            <div class="stat-card"><div class="stat-card-value">{{ $totalBanned }}</div><div class="stat-card-label">🚫 محظورين</div></div>
        </div>

        <h3 style="margin-top:1.5rem; margin-bottom:1rem;">📅 الشهر الحالي</h3>
        <div class="stat-cards">
            <div class="stat-card" style="background:#eff6ff; border:1px solid #bfdbfe;">
                <div class="stat-card-value">{{ $monthUsers }}</div><div class="stat-card-label">👥 مستخدمين جدد</div>
            </div>
            <div class="stat-card" style="background:#d1fae5; border:1px solid #a7f3d0;">
                <div class="stat-card-value">{{ $monthServices }}</div><div class="stat-card-label">🛠️ خدمات جديدة</div>
            </div>
            <div class="stat-card" style="background:#fef3c7; border:1px solid #fde68a;">
                <div class="stat-card-value">{{ $monthRequests }}</div><div class="stat-card-label">📋 طلبات جديدة</div>
            </div>
            <div class="stat-card" style="background:#fce7f3; border:1px solid #fbcfe8;">
                <div class="stat-card-value">{{ $monthPosts }}</div><div class="stat-card-label">📢 منشورات</div>
            </div>
            <div class="stat-card" style="background:#e0e7ff; border:1px solid #c7d2fe;">
                <div class="stat-card-value">{{ number_format($monthRevenue, 2) }}</div><div class="stat-card-label">💰 إيرادات الشهر</div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-top:1.5rem;">
            <div class="card">
                <h3 style="margin-bottom:1rem;">👥 توزيع المستخدمين</h3>
                <table class="admin-table">
                    <thead><tr><th>النوع</th><th>العدد</th></tr></thead>
                    <tbody>
                        @foreach($usersByType as $type => $count)
                        <tr><td>{{ $type }}</td><td>{{ $count }}</td></tr>
                        @endforeach
                        <tr style="font-weight:bold; background:#f8fafc;"><td>المجموع</td><td>{{ $totalUsers }}</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3 style="margin-bottom:1rem;">📋 حالة الطلبات</h3>
                <table class="admin-table">
                    <thead><tr><th>الحالة</th><th>العدد</th></tr></thead>
                    <tbody>
                        @foreach($requestsByStatus as $status => $count)
                        <tr><td>{{ $status }}</td><td>{{ $count }}</td></tr>
                        @endforeach
                        <tr style="font-weight:bold; background:#f8fafc;"><td>المجموع</td><td>{{ $totalRequests }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" style="margin-top:1.5rem;">
            <h3 style="margin-bottom:1rem;">📋 ملخص عام</h3>
            <table class="admin-table">
                <thead><tr><th>العنصر</th><th>العدد</th><th>العنصر</th><th>العدد</th></tr></thead>
                <tbody>
                    <tr><td>👥 مستخدمين</td><td>{{ $totalUsers }}</td><td>🛠️ خدمات</td><td>{{ $totalServices }}</td></tr>
                    <tr><td>📋 طلبات</td><td>{{ $totalRequests }}</td><td>💵 عروض</td><td>{{ $totalOffers }}</td></tr>
                    <tr><td>⭐ تقييمات</td><td>{{ $totalRatings }}</td><td>📢 منشورات</td><td>{{ $totalPosts }}</td></tr>
                    <tr><td>📖 قصص</td><td>{{ $totalStories }}</td><td>💬 محادثات</td><td>{{ $totalChats }}</td></tr>
                    <tr><td>📅 حجوزات</td><td>{{ $totalBookings }}</td><td>🚩 بلاغات</td><td>{{ $totalReports }}</td></tr>
                    <tr style="font-weight:bold; background:#f8fafc;"><td>💰 الإيرادات</td><td colspan="3">{{ number_format($totalRevenue, 2) }} ر.س</td></tr>
                </tbody>
            </table>
        </div>

    </main>
</div>
@endsection