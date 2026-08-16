@extends('layouts.app')
@section('title', __('Settings') . ' - Job Service')
@section('content')
<div style="max-width:650px;margin:0 auto;padding:1.5rem 1rem;">
    <a href="{{ route('profile') }}" class="back-link">← {{ __('Return to Profile') }}</a>
    <h2>⚙️ {{ __('Settings') }}</h2>

    @if(session('success'))<div class="success-msg">✅ {{ session('success') }}</div>@endif
    @if($errors->any())<div class="error-msg">{{ $errors->first() }}</div>@endif

    <!-- حالة الحساب -->
    <div class="card" style="margin-bottom:1rem; border:1px solid {{ auth()->user()->is_active ? '#10b981' : '#f59e0b' }};">
        <h3>📊 {{ __('Account Status') }}</h3>
        @if(auth()->user()->is_active)
            <span style="color:#10b981; font-weight:700;">✅ {{ __('Active') }}</span>
        @else
            <span style="color:#f59e0b; font-weight:700;">⛔ {{ __('Suspended') }}</span>
        @endif
    </div>

    <!-- كلمة المرور -->
    <div class="card" style="margin-bottom:1rem;">
        <h3>🔒 {{ __('Change Password') }}</h3>
        <form method="POST" action="{{ route('settings.password') }}">
            @csrf
            <div class="input-group"><label>{{ __('Current Password') }}</label><input type="password" name="current_password" required></div>
            <div class="input-group"><label>{{ __('New Password') }}</label><input type="password" name="new_password" required></div>
            <div class="input-group"><label>{{ __('Confirm New Password') }}</label><input type="password" name="new_password_confirmation" required></div>
            <button type="submit" class="btn btn-primary">💾 {{ __('Change Password') }}</button>
        </form>
    </div>

    <!-- المظهر -->
    <div class="card" style="margin-bottom:1rem;">
        <h3>🎨 {{ __('Appearance') }}</h3>
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <span>🌙 {{ __('Night Mode') }}</span>
            <input type="checkbox" class="switch" id="darkModeToggle" onchange="toggleDarkMode(this)">
        </div>
    </div>

    <!-- إيقاف / تفعيل الحساب -->
    <div class="card" style="border:1px solid {{ auth()->user()->is_active ? '#f59e0b' : '#10b981' }};">
        @if(auth()->user()->is_active)
            <h3 style="color:#f59e0b;">⏸️ {{ __('Suspend Account') }}</h3>
            <form method="POST" action="{{ route('account.suspend') }}" onsubmit="return confirm('{{ __('Cancel booking?') }}')">
                @csrf
                <button type="submit" class="btn btn-warning">⏸️ {{ __('Suspend Account') }}</button>
            </form>
        @else
            <h3 style="color:#10b981;">🔄 {{ __('Activate Account') }}</h3>
            <form method="POST" action="{{ route('account.activate') }}">
                @csrf
                <button type="submit" class="btn btn-success">✅ {{ __('Activate Account') }}</button>
            </form>
        @endif
    </div>
</div>


@endsection