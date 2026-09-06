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
        $wallets = \App\Models\WalletSetting::getActive();
    @endphp

    {{-- ✅ بطاقات المحافظ - صورة كخلفية --}}
    @if($wallets->count() > 0)
    <h3 style="text-align:center; margin-bottom:1rem;">💳 {{ __('Wallets') }}</h3>
    <div class="wallets-container">
        @foreach($wallets as $wallet)
        @php
            $walletImage = '';
            $walletExt = '.png';
            
            if (str_contains(strtolower($wallet->wallet_name), 'جيب') || str_contains(strtolower($wallet->wallet_name), 'jawib')) {
                $walletImage = 'jawib';
                $walletExt = '.jpeg';
            } elseif (str_contains(strtolower($wallet->wallet_name), 'كاش') || str_contains(strtolower($wallet->wallet_name), 'cash')) {
                $walletImage = 'cash';
                $walletExt = '.png';
            } elseif (str_contains(strtolower($wallet->wallet_name), 'جوالي') || str_contains(strtolower($wallet->wallet_name), 'jawali')) {
                $walletImage = 'jawali';
                $walletExt = '.jpeg';
            }
        @endphp
        <div class="wallet-card" style="background-image: url('{{ asset('images/wallets/' . $walletImage . $walletExt) }}');">
            <div class="wallet-card-content">
                <h4 class="wallet-name">{{ $wallet->wallet_name }}</h4>
                <p class="wallet-number">{{ $wallet->wallet_number }}</p>
                <p class="wallet-owner">{{ $wallet->wallet_owner }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
        @foreach($packages as $pkg)
        <div class="card" style="text-align: center; {{ $currentPackage && $currentPackage->package_id == $pkg->id ? 'border: 2px solid #10b981; background: #f0fdf4;' : '' }}">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">{{ $pkg->badge ?? '📦' }}</div>
            <h3 style="margin-bottom: 0.25rem;">{{ $pkg->name }}</h3>
            <p style="font-size: 2rem; font-weight: 900; color: var(--primary);">{{ $pkg->price > 0 ? number_format($pkg->price) . ' ر.ي' : __('Free') }}</p>
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

{{-- ✅ نافذة الدفع --}}
<div id="paymentModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:white; border-radius:16px; padding:2rem; max-width:500px; width:100%; max-height:90vh; overflow-y:auto;">
        <h3 style="text-align:center; margin-bottom:1rem;">💳 {{ __('Complete Payment') }}</h3>
        
        <div style="background:#f0fdf4; padding:1rem; border-radius:12px; margin-bottom:1rem; text-align:center;">
            <p style="font-size:0.9rem; color:#166534;">{{ __('Package') }}: <strong id="modalPackageName"></strong></p>
            <p style="font-size:1.5rem; font-weight:900; color:#065f46;" id="modalPackagePrice"></p>
        </div>

        <form method="POST" action="{{ route('packages.activate') }}" enctype="multipart/form-data" onsubmit="return validatePaymentForm(this)">
            @csrf
            <input type="hidden" name="package_id" id="modalPackageId">
            
            {{-- ✅ اختيار المحفظة --}}
            <div style="margin-bottom:1rem;">
                <p style="font-weight:700; margin-bottom:0.5rem; text-align:center;">💳 {{ __('Choose Wallet') }}:</p>
                <div class="wallet-select-grid">
                    @foreach($wallets as $wallet)
                    @php
                        $walletImage = '';
                        $walletExt = '.png';
                        
                        if (str_contains(strtolower($wallet->wallet_name), 'جيب') || str_contains(strtolower($wallet->wallet_name), 'jawib')) {
                            $walletImage = 'jawib';
                            $walletExt = '.jpeg';
                        } elseif (str_contains(strtolower($wallet->wallet_name), 'كاش') || str_contains(strtolower($wallet->wallet_name), 'cash')) {
                            $walletImage = 'cash';
                            $walletExt = '.png';
                        } elseif (str_contains(strtolower($wallet->wallet_name), 'جوالي') || str_contains(strtolower($wallet->wallet_name), 'jawali')) {
                            $walletImage = 'jawali';
                            $walletExt = '.jpeg';
                        }
                    @endphp
                    <label class="wallet-select-item" style="background-image: url('{{ asset('images/wallets/' . $walletImage . $walletExt) }}');">
                        <input type="radio" name="wallet_name" value="{{ $wallet->wallet_name }}" required style="display:none;">
                        <strong class="wallet-name">{{ $wallet->wallet_name }}</strong>
                        <small class="wallet-number">{{ $wallet->wallet_number }}</small>
                    </label>
                    @endforeach
                </div>
            </div>
            
            {{-- ✅ حقول فاضية إجبارية --}}
            <div class="sender-input-group">
                <label>👤 {{ __('Full Name (as in your ID)') }} <span class="required-star">*</span></label>
                <input type="text" name="sender_name" placeholder="{{ __('Write your full name (4 names)') }}" required>
                <p class="sender-error-msg"></p>
            </div>
            
            <div class="sender-input-group">
                <label>📱 {{ __('Phone Number (as in wallet)') }} <span class="required-star">*</span></label>
                <input type="text" name="sender_phone" placeholder="+967 7XX XXX XXX" required>
                <p class="sender-error-msg"></p>
            </div>
            
            <div class="input-group">
                <label>📷 {{ __('Upload Receipt') }} <span class="required-star">*</span></label>
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