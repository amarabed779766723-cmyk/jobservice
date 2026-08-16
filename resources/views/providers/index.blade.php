@extends('layouts.app')

@section('title', 'أفضل مقدمين الخدمة')

@section('content')

<div style="max-width:1200px;margin:0 auto;padding:1.5rem;">
    <div class="section-header-new">
        <h2>
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:0.5rem;">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
                <path d="M16 11l2 2 4-4"/>
            </svg>
            أفضل مقدمين الخدمة
        </h2>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(250px, 1fr));gap:1.25rem;">
        @forelse($providers as $provider)
            <a href="{{ route('profile', $provider->id) }}" style="text-decoration:none;color:inherit;">
                <div style="background:var(--bg-card);border-radius:16px;padding:1.5rem;border:1px solid var(--border);text-align:center;transition:0.3s;cursor:pointer;">
                    <img src="{{ asset('uploads/avatars/' . ($provider->avatar ?? 'default-avatar.png')) }}" 
                         style="width:70px;height:70px;border-radius:50%;object-fit:cover;margin-bottom:0.75rem;border:3px solid var(--primary-light);">
                    <h4 style="font-weight:700;font-size:0.95rem;margin-bottom:0.25rem;">{{ $provider->name }}</h4>
                    @if($provider->services->first())
                        <p style="color:var(--text-secondary);font-size:0.8rem;margin-bottom:0.5rem;">🛠️ {{ $provider->services->first()->title }}</p>
                    @endif
                    <div>
                        <span style="color:#F59E0B;font-weight:700;">⭐ {{ number_format($provider->average_rating, 1) }}</span>
                        <span style="color:var(--text-secondary);font-size:0.75rem;">({{ $provider->ratings_count }} تقييم)</span>
                    </div>
                </div>
            </a>
        @empty
            <p style="text-align:center;color:var(--text-secondary);grid-column:1/-1;">لا يوجد متخصصون مؤهلون حالياً</p>
        @endforelse
    </div>

    <div style="margin-top:2rem;">
        {{ $providers->links() }}
    </div>
</div>

@endsection