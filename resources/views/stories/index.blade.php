@extends('layouts.app')
@section('title', __('Ads') . ' - Job Service')
@section('content')
<div style="max-width: 700px; margin: 0 auto; padding: 1.5rem 1rem;">
    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Home') }}
    </a>

    <div class="card" style="margin-bottom: 1.5rem; margin-top: 1rem;">
        <h3 style="margin-bottom: 1rem;">📢 {{ __('New Ad') }}</h3>

        @if(session('success'))<div class="success-msg">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="error-msg">{{ session('error') }}</div>@endif

        @php 
            $activePackage = \App\Models\UserPackage::where('user_id', Auth::id())
                ->where('status', 'active')
                ->first();
            
            $hasFreeAds = false;
            if ($activePackage && !\Carbon\Carbon::parse($activePackage->end_date)->isPast()) {
                $userPackageData = \App\Models\Package::find($activePackage->package_id);
                $currentAds = \App\Models\Story::where('user_id', Auth::id())
                    ->where('is_ad', 1)
                    ->where('created_at', '>=', $activePackage->start_date)
                    ->count();
                
                if ($userPackageData && $userPackageData->max_ads !== null && $currentAds < $userPackageData->max_ads) {
                    $hasFreeAds = true;
                }
            }
            
            $adPackages = \App\Models\AdPackage::all(); 
            $wallets = \App\Models\WalletSetting::getActive();
        @endphp

        <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="input-group"><label>📷 {{ __('Ad Image') }}</label><input type="file" name="image" accept="image/*,video/*" required></div>
            <div class="input-group"><label>🔗 {{ __('Link (Optional)') }}</label><input type="text" name="link" placeholder="https://..."></div>
            <div class="input-group"><label>✍️ {{ __('Ad Text') }}</label><input type="text" name="caption" placeholder="{{ __('Write ad text...') }}"></div>

            @if($hasFreeAds)
                {{-- ✅ عنده إعلان متبقي من الباقة --}}
                <div style="background:#d1fae5; padding:1rem; border-radius:12px; margin-top:1rem; text-align:center;">
                    <p style="font-size:1.1rem; font-weight:700; color:#065f46;">✅ {{ __('You have a free ad remaining') }}</p>
                    <p style="font-size:0.85rem; color:#065f46;">{{ __('Your ad will be published after admin approval') }}</p>
                </div>
            @else
                {{-- ✅ لازم يدفع --}}
                <h4 style="margin: 1rem 0 0.5rem;">💰 {{ __('Choose Ad Package') }}</h4>
                
                {{-- عرض المحافظ --}}
                @if($wallets->count() > 0)
                <h4 style="text-align:center; margin:1rem 0;">💳 {{ __('Wallets') }}</h4>
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
                
                {{-- باقات الإعلان --}}
                @foreach($adPackages as $pkg)
                <label style="display:flex; justify-content:space-between; align-items:center; padding:0.75rem 1rem; border:1px solid var(--border); border-radius:8px; margin-bottom:0.5rem; cursor:pointer;">
                    <div>
                        <strong>{{ $pkg->name }}</strong>
                        <span style="color:var(--text-secondary); font-size:0.85rem; display:block;">{{ $pkg->duration_days }} {{ __('Days') }}</span>
                    </div>
                    <span style="color:var(--primary); font-weight:700;">{{ number_format($pkg->price) }} ر.ي</span>
                    <input type="radio" name="package_id" value="{{ $pkg->id }}" required>
                </label>
                @endforeach

                {{-- اختيار المحفظة + الحقول --}}
                <div style="background:#f0fdf4; padding:1rem; border-radius:12px; margin-top:1rem;">
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
                </div>
            @endif

            <div style="background:#fef3c7; padding:0.75rem; border-radius:8px; margin-top:1rem; font-size:0.85rem; color:#92400e;">
                ⚠️ {{ __('After submitting, your ad will be published after admin approval.') }}
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:1rem;">📢 {{ __('Submit Ad') }}</button>
        </form>
    </div>

    {{-- ========== إعلاناتي ========== --}}
    <h3 style="margin-bottom: 1rem;">📋 {{ __('My Ads') }}</h3>
    @if($myStories->isEmpty())
        <div class="card" style="text-align:center; padding:2rem;">
            <p style="font-size:3rem;">📭</p>
            <p>{{ __('No Ads Currently') }}</p>
        </div>
    @else
        @foreach($myStories as $ad)
            <div class="card" style="margin-bottom:0.75rem;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:60px; height:60px; border-radius:12px; overflow:hidden; background:#000;">
                        <img src="{{ asset('uploads/stories/' . $ad->image) }}" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div style="flex:1;">
                        <strong>{{ $ad->caption ?? __('No Text') }}</strong>
                        <p style="color:var(--text-secondary); font-size:0.8rem;">
                            {{ \Carbon\Carbon::parse($ad->created_at)->diffForHumans() }}
                            ·
                            @if($ad->status == 'pending') <span style="color:#f59e0b;">⏳ {{ __('Pending') }}</span>
                            @elseif($ad->status == 'approved') <span style="color:#10b981;">✅ {{ __('Approved') }}</span>
                            @else <span style="color:#ef4444;">❌ {{ __('Rejected') }}</span>
                            @endif
                            · 👁️ {{ $ad->views()->count() }} {{ __('Views') }}
                        </p>
                        @if($ad->ad_price > 0)
                        <p style="color:var(--primary); font-size:0.8rem; font-weight:700;">
                            💰 {{ number_format($ad->ad_price) }} ر.ي
                        </p>
                        @endif
                    </div>
                    <form action="{{ route('stories.delete', $ad->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:1.2rem;" onclick="return confirm('{{ __('Delete Ad?') }}')">🗑️</button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection