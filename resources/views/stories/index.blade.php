@extends('layouts.app')
@section('title', __('Ads') . ' - Job Service')
@section('content')
<div style="max-width: 700px; margin: 0 auto; padding: 1.5rem 1rem;">
    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Home') }}
    </a>

    {{-- ========== رفع إعلان جديد ========== --}}
    <div class="card" style="margin-bottom: 1.5rem; margin-top: 1rem;">
        <h3 style="margin-bottom: 1rem;">📢 {{ __('New Ad') }}</h3>

        @if(session('success'))<div class="success-msg">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="error-msg">{{ session('error') }}</div>@endif

        <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="input-group"><label>📷 {{ __('Ad Image') }}</label><input type="file" name="image" accept="image/*,video/*" required></div>
            <div class="input-group"><label>🔗 {{ __('Link (Optional)') }}</label><input type="text" name="link" placeholder="https://..."></div>
            <div class="input-group"><label>✍️ {{ __('Ad Text') }}</label><input type="text" name="caption" placeholder="{{ __('Write ad text...') }}"></div>

            <h4 style="margin: 1rem 0 0.5rem;">💰 {{ __('Choose Package') }}</h4>
            @php $packages = \App\Models\AdPackage::all(); @endphp
            @foreach($packages as $pkg)
            <label style="display:flex; justify-content:space-between; align-items:center; padding:0.75rem 1rem; border:1px solid var(--border); border-radius:8px; margin-bottom:0.5rem; cursor:pointer;">
                <div>
                    <strong>{{ $pkg->name }}</strong>
                    <span style="color:var(--text-secondary); font-size:0.85rem; display:block;">{{ $pkg->duration_days }} {{ __('Days') }}</span>
                </div>
                <span style="color:var(--primary); font-weight:700;">{{ number_format($pkg->price) }} ر.س</span>
                <input type="radio" name="package_id" value="{{ $pkg->id }}" required>
            </label>
            @endforeach

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