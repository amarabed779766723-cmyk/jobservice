@extends('layouts.app')
@section('title', __('Notifications'))
@section('content')
<div style="max-width:600px; margin:0 auto; padding:1.5rem 1rem;">
    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Home') }}
    </a>
    <h2>🔔 {{ __('Notifications') }}</h2>

    @php
        $notifications = \App\Models\Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    @endphp

    @if($notifications->isEmpty())
        <div class="card"><p>{{ __('No Notifications') }}</p></div>
    @else
        @foreach($notifications as $notif)
            <a href="{{ $notif->link ?? '#' }}" style="text-decoration:none; color:inherit;">
                <div style="background: var(--bg-card); border-radius:12px; padding:0.75rem 1rem; margin-bottom:0.5rem; border:1px solid var(--border); {{ $notif->is_read ? '' : 'border-right:4px solid var(--primary);' }}">
                    <p style="margin-bottom:0.25rem;">{{ $notif->message }}</p>
                    <small style="color: var(--text-secondary);">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</small>
                </div>
            </a>
        @endforeach

        @php
            \App\Models\Notification::where('user_id', Auth::id())->update(['is_read' => 1]);
        @endphp
    @endif
</div>
@endsection