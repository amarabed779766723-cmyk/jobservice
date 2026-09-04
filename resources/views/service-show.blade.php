@extends('layouts.app')
@section('title', $service->title . ' - Job Service')
@section('content')
<div style="max-width: 700px; margin: 0 auto; padding: 1.5rem 1rem;">
    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Home') }}
    </a>

    <div class="card" style="margin-top: 1rem;">
        <h2>{{ $service->title }}</h2>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin: 0.75rem 0;">
            <a href="{{ route('profile', $service->provider_id) }}">
                <img src="{{ asset('uploads/avatars/' . ($service->provider->avatar ?? 'default-avatar.png')) }}" style="width: 32px; height: 32px; border-radius: 50%;">
            </a>
            <span>{{ $service->provider->name ?? __('Provider') }}</span>
            <span class="provider-badge">⚡ {{ __('Provider') }}</span>
            @auth
                @if(auth()->id() != $service->provider_id)
                    <button onclick="reportService({{ $service->id }}, {{ $service->provider_id }})" style="background:none; border:none; cursor:pointer; font-size:0.9rem;" title="{{ __('Report') }}">🚩</button>
                @endif
            @endauth
            @auth
                @if(auth()->id() != $service->provider_id)
                    <a href="{{ route('chat.start', $service->provider_id) }}" class="btn btn-outline btn-sm" style="margin-right: auto;">💬 {{ __('Message') }}</a>
                @endif
            @endauth
        </div>
        <p>{{ $service->description ?? __('No Description') }}</p>
        <div style="display: flex; gap: 2rem; background: var(--bg); padding: 1rem; border-radius: 0.75rem; margin: 1rem 0;">
            <div><strong>{{ number_format($service->price, 2) }} ر.س</strong></div>
            @if($service->duration)
                <div>⏱️ {{ $service->duration }}</div>
            @endif
        </div>
        @auth
            <a href="{{ route('booking.create', $service->id) }}" class="btn btn-primary">📅 {{ __('Book') }}</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline">{{ __('Login') }}</a>
        @endauth
    </div>

    {{-- ===== قسم الخريطة (جديد) ===== --}}
    @if($service->latitude && $service->longitude)
    <div class="card" style="margin-top: 1rem;">
        <h3 style="margin-bottom: 0.75rem;">📍 {{ __('Service Location') }}</h3>
        
        @if($service->address)
            <p style="margin-bottom: 0.5rem; color: var(--text-secondary);">
                📍 {{ $service->address }}
            </p>
        @endif
        
        <div id="map" style="height: 300px; border-radius: 8px;"></div>
    </div>
    @endif
</div>
@endsection

@if($service->latitude && $service->longitude)
@section('scripts')
<script>
function initMap() {
    const location = { 
        lat: {{ $service->latitude }}, 
        lng: {{ $service->longitude }} 
    };
    
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14,
        center: location,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true
    });
    
    new google.maps.Marker({
        position: location,
        map: map,
        animation: google.maps.Animation.DROP,
        title: '{{ $service->title }}'
    });
}
</script>
<script async defer 
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap">
</script>
@endsection
@endif