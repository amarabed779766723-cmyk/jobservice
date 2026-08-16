@extends('layouts.admin')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <h2>💰 صندوق إيرادات المنصة</h2>
        
        {{-- الإجمالي --}}
        <div class="card" style="background:linear-gradient(135deg, #dbeafe, #d1fae5); border:2px solid #2563eb; text-align:center; margin-bottom:2rem;">
            <h2 style="color:#1e40af;">💎 إجمالي إيرادات المنصة</h2>
            <strong style="font-size:2rem; color:#065f46;">{{ number_format($totalRevenue, 2) }} ر.س</strong>
        </div>

        {{-- بطاقات اليوم/أسبوع/شهر/سنة --}}
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:2rem;">
            <div class="card" style="background:#f0fdf4; border:2px solid #86efac; text-align:center;">
                <h3 style="color:#166534;">📅 اليوم</h3>
                <strong style="font-size:1.5rem; color:#15803d;">{{ number_format($todayRevenue, 2) }} ر.س</strong>
            </div>
            <div class="card" style="background:#eff6ff; border:2px solid #93c5fd; text-align:center;">
                <h3 style="color:#1e40af;">📆 الأسبوع</h3>
                <strong style="font-size:1.5rem; color:#1d4ed8;">{{ number_format($weekRevenue, 2) }} ر.س</strong>
            </div>
            <div class="card" style="background:#fefce8; border:2px solid #fde047; text-align:center;">
                <h3 style="color:#854d0e;">📊 الشهر</h3>
                <strong style="font-size:1.5rem; color:#a16207;">{{ number_format($monthRevenue, 2) }} ر.س</strong>
            </div>
            <div class="card" style="background:#fef2f2; border:2px solid #fca5a5; text-align:center;">
                <h3 style="color:#991b1b;">🎯 السنة</h3>
                <strong style="font-size:1.5rem; color:#dc2626;">{{ number_format($yearRevenue, 2) }} ر.س</strong>
            </div>
        </div>
        
        {{-- تفصيل الإيرادات --}}
        <h3 style="margin-bottom:1rem;">📊 تفصيل الإيرادات حسب المصدر</h3>
        <table class="admin-table" style="margin-bottom:2rem;">
            <thead>
                <tr><th>المصدر</th><th>اليوم</th><th>الأسبوع</th><th>الشهر</th><th>السنة</th><th>الإجمالي</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>💳 الباقات</strong></td>
                    <td>{{ number_format($todayPackages, 2) }} ر.س</td>
                    <td>{{ number_format($weekPackages, 2) }} ر.س</td>
                    <td>{{ number_format($monthPackages, 2) }} ر.س</td>
                    <td>{{ number_format($yearPackages, 2) }} ر.س</td>
                    <td><strong>{{ number_format($totalPackages, 2) }} ر.س</strong></td>
                </tr>
                <tr>
                    <td><strong>📢 الإعلانات</strong></td>
                    <td>{{ number_format($todayAds, 2) }} ر.س</td>
                    <td>{{ number_format($weekAds, 2) }} ر.س</td>
                    <td>{{ number_format($monthAds, 2) }} ر.س</td>
                    <td>{{ number_format($yearAds, 2) }} ر.س</td>
                    <td><strong>{{ number_format($totalAds, 2) }} ر.س</strong></td>
                </tr>
                <tr>
                    <td><strong>🛠️ الخدمات</strong></td>
                    <td>{{ number_format($todayServices, 2) }} ر.س</td>
                    <td>{{ number_format($weekServices, 2) }} ر.س</td>
                    <td>{{ number_format($monthServices, 2) }} ر.س</td>
                    <td>{{ number_format($yearServices, 2) }} ر.س</td>
                    <td><strong>{{ number_format($totalServices, 2) }} ر.س</strong></td>
                </tr>
                <tr style="background:#f8fafc; font-weight:700;">
                    <td>💎 المجموع</td>
                    <td>{{ number_format($todayRevenue, 2) }} ر.س</td>
                    <td>{{ number_format($weekRevenue, 2) }} ر.س</td>
                    <td>{{ number_format($monthRevenue, 2) }} ر.س</td>
                    <td>{{ number_format($yearRevenue, 2) }} ر.س</td>
                    <td><strong>{{ number_format($totalRevenue, 2) }} ر.س</strong></td>
                </tr>
            </tbody>
        </table>
    </main>
</div>
@endsection