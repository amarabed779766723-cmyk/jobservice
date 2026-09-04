@extends('layouts.app')
@section('title', __('Packages') . ' - Job Service')
@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 1.5rem 1rem;">
    <a href="{{ route('profile') }}" class="back-link">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    {{ __('Back to Profile') }}
    </a>
    <h2 style="text-align: center; margin-bottom: 0.5rem;">💎 {{ __('Packages') }}</h2>
    <p style="text-align: center; color: var(--text-secondary); margin-bottom: 2rem;">{{ __('Choose Package') }}</p>

    @php
        $packages = \App\Models\Package::all();
        $currentPackage = \App\Models\UserPackage::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with('package')
            ->first();
        $wallet = \App\Models\WalletSetting::getActive();
    @endphp

    {{-- بطاقة المحفظة --}}
    @if($wallet)
    <div class="card" style="background: linear-gradient(135deg, #10b981, #059669); color: white; text-align: center; margin-bottom: 2rem; border: none;">
        <h3 style="color: white;">📱 {{ $wallet->wallet_name }}</h3>
        <p style="font-size: 1.5rem; font-weight: 900; color: #fbbf24;">{{ $wallet->wallet_number }}</p>
        <p style="font-size: 0.9rem; opacity: 0.9;">{{ $wallet->wallet_owner }}</p>
    </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
        @foreach($packages as $pkg)
        <div class="card" style="text-align: center; {{ $currentPackage && $currentPackage->package_id == $pkg->id ? 'border: 2px solid #10b981; background: #f0fdf4;' : '' }}">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">{{ $pkg->badge ?? '📦' }}</div>
            <h3 style="margin-bottom: 0.25rem;">{{ $pkg->name }}</h3>
            <p style="font-size: 2rem; font-weight: 900; color: var(--primary;">{{ $pkg->price > 0 ? number_format($pkg->price) . ' ر.ي' : __('Free') }}</p>
            <p style="color: var(--text-secondary); font-size: 0.85rem;">{{ $pkg->duration_days }} {{ __('Days') }}</p>
            <hr style="margin: 0.75rem 0;">
            <p style="font-size: 0.85rem;">🛠️ {{ __('Services') }}: {{ $pkg->max_services ?? '∞' }}</p>
            <p style="font-size: 0.85rem;">📋 {{ __('Requests') }}: {{ $pkg->max_requests ?? '∞' }}</p>
            <p style="font-size: 0.85rem;">📢 {{ __('Ads') }}: {{ $pkg->max_ads ?? '∞' }}</p>

            @if($currentPackage && $currentPackage->package_id == $pkg->id)
                <button class="btn btn-outline btn-sm" style="width:100%; margin-top:0.75rem;" disabled>✅ {{ __('Current') }}</button>
            @elseif($pkg->price == 0)
                <form method="POST" action="{{ route('packages.activate') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                    <button class="btn btn-primary btn-sm" style="width:100%; margin-top:0.75rem;">{{ __('Activate') }}</button>
                </form>
            @else
                <button type="button" class="btn btn-primary btn-sm" style="width:100%; margin-top:0.75rem;" onclick="openPaymentModal({{ $pkg->id }}, '{{ $pkg->name }}', {{ $pkg->price }})">
                    💳 {{ __('Subscribe Now') }}
                </button>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- نافذة الدفع --}}
<div id="paymentModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:white; border-radius:16px; padding:2rem; max-width:500px; width:100%; max-height:90vh; overflow-y:auto;">
        <h3 style="text-align:center; margin-bottom:1rem;">💳 {{ __('Complete Payment') }}</h3>
        
        <div style="background:#f0fdf4; padding:1rem; border-radius:12px; margin-bottom:1rem; text-align:center;">
            <p style="font-size:0.9rem; color:#166534;">{{ __('Package') }}: <strong id="modalPackageName"></strong></p>
            <p style="font-size:1.5rem; font-weight:900; color:#065f46;" id="modalPackagePrice"></p>
            <hr>
            @if($wallet)
            <p style="font-size:0.85rem;">📱 {{ $wallet->wallet_name }}: <strong>{{ $wallet->wallet_number }}</strong></p>
            <p style="font-size:0.85rem;">👤 {{ $wallet->wallet_owner }}</p>
            @endif
        </div>

        <form method="POST" action="{{ route('packages.activate') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="package_id" id="modalPackageId">
            
            <div class="input-group">
                <label>{{ __('Your Name') }}</label>
                <input type="text" name="sender_name" value="{{ Auth::user()->name }}" required>
            </div>
            
            <div class="input-group">
                <label>{{ __('Your Phone') }}</label>
                <input type="text" name="sender_phone" value="{{ Auth::user()->phone }}" required>
            </div>
            
            <div class="input-group">
                <label>📷 {{ __('Upload Receipt') }}</label>
                <input type="file" name="receipt_image" accept="image/*" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width:100%;">✅ {{ __('Confirm Payment') }}</button>
            <button type="button" class="btn btn-outline" style="width:100%; margin-top:0.5rem;" onclick="closePaymentModal()">❌ {{ __('Cancel') }}</button>
        </form>
    </div>
</div>

<script>
function openPaymentModal(id, name, price) {
    document.getElementById('modalPackageId').value = id;
    document.getElementById('modalPackageName').textContent = name;
    document.getElementById('modalPackagePrice').textContent = price + ' ر.ي';
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}
</script>
@endsection