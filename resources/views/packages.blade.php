@extends('layouts.app')
@section('title', 'الباقات - Job Service')
@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 1.5rem 1rem;">
    <h2 style="text-align: center; margin-bottom: 0.5rem;">💎 الباقات</h2>
    <p style="text-align: center; color: var(--text-secondary); margin-bottom: 2rem;">اختر الباقة المناسبة لك</p>

    @php
        $packages = \App\Models\Package::all();
        $currentPackage = \App\Models\UserPackage::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with('package')
            ->first();
    @endphp

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
        @foreach($packages as $pkg)
        <div class="card" style="text-align: center; {{ $currentPackage && $currentPackage->package_id == $pkg->id ? 'border: 2px solid #10b981; background: #f0fdf4;' : '' }}">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">{{ $pkg->badge ?? '📦' }}</div>
            <h3 style="margin-bottom: 0.25rem;">{{ $pkg->name }}</h3>
            <p style="font-size: 2rem; font-weight: 900; color: var(--primary);">{{ $pkg->price > 0 ? number_format($pkg->price) . ' ر.س' : 'مجاني' }}</p>
            <p style="color: var(--text-secondary); font-size: 0.85rem;">{{ $pkg->duration_days }} يوم</p>
            <hr style="margin: 0.75rem 0;">
            <p style="font-size: 0.85rem;">🛠️ خدمات: {{ $pkg->max_services ?? '∞' }}</p>
            <p style="font-size: 0.85rem;">📋 طلبات: {{ $pkg->max_requests ?? '∞' }}</p>
            <p style="font-size: 0.85rem;">📢 إعلانات: {{ $pkg->max_ads ?? '∞' }}</p>

            @if($currentPackage && $currentPackage->package_id == $pkg->id)
                <button class="btn btn-outline btn-sm" style="width:100%; margin-top:0.75rem;" disabled>✅ الحالية</button>
            @elseif($pkg->price == 0)
                <form method="POST" action="{{ route('packages.activate') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                    <button class="btn btn-primary btn-sm" style="width:100%; margin-top:0.75rem;">تفعيل</button>
                </form>
            @else
                <form method="POST" action="{{ route('packages.activate') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                    <button class="btn btn-primary btn-sm" style="width:100%; margin-top:0.75rem;">ترقية - {{ number_format($pkg->price) }} ر.س</button>
                </form>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection