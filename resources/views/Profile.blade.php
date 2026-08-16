@extends('layouts.app')
@section('title', $profileUser->name . ' - Job Service')
@section('content')

<div class="profile-container">

    {{-- الهيدر --}}
    <div class="profile-header">
        <div class="profile-cover" style="background-image: url('{{ asset('uploads/covers/' . ($profileUser->cover ?? 'default-cover.jpg')) }}');"></div>
        <div class="profile-avatar-wrapper">
            <img src="{{ asset('uploads/avatars/' . ($profileUser->avatar ?? 'default-avatar.png')) }}" class="profile-avatar">
        </div>
        <h2 class="profile-name">
            {{ $profileUser->name }}
            @if($profileUser->user_type === 'provider')
                <span class="provider-badge">⚡ {{ __('Provider') }}</span>
            @else
                <span class="client-badge">🔍 {{ __('Client') }}</span>
            @endif
        </h2>
        @if($profileUser->bio)
            <p class="profile-bio">{{ $profileUser->bio }}</p>
        @endif
        @if($profileUser->city || $profileUser->website)
            <p class="profile-meta">
                @if($profileUser->city)📍 {{ $profileUser->city }} @endif
                @if($profileUser->website) · 🔗 <a href="{{ $profileUser->website }}" target="_blank">{{ $profileUser->website }}</a> @endif
            </p>
        @endif

        {{-- أزرار المتابعة والمراسلة --}}
        <div class="profile-actions" style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 1rem;">
            @auth
                @if(auth()->id() == $profileUser->id)
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline btn-sm" style="display:flex; align-items:center; gap:6px; border-radius:8px; font-weight:600; padding:0.5rem 1.2rem; border:2px solid #2563eb; color:#2563eb;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        {{ __('Edit Profile') }}
                    </a>
                    <a href="{{ route('settings') }}" class="btn btn-outline btn-sm" style="display:flex; align-items:center; gap:6px; border-radius:8px; font-weight:600; padding:0.5rem 1.2rem; border:2px solid #6b7280; color:#6b7280;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        {{ __('Settings') }}
                    </a>
                @else
                    <button class="btn follow-btn btn-sm" data-user-id="{{ $profileUser->id }}"
                            style="display: flex; align-items: center; gap: 6px; border-radius: 8px; font-weight: 600; padding: 0.5rem 1.5rem; transition: all 0.3s ease; min-width:140px; justify-content:center;
                            {{ $isFollowing ? 'background: #374151; color: white; border: none;' : 'background: #2563eb; color: white; border: none;' }}">
                        <span class="follow-icon">
                            @if(!$isFollowing)
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/>
                                    <line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                                </svg>
                            @else
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/>
                                    <polyline points="17 11 19 13 23 9"/>
                                </svg>
                            @endif
                        </span>
                        <span class="follow-text">{{ $isFollowing ? __('Following') : __('Follow') }}</span>
                    </button>

                    <a href="{{ route('chat.start', $profileUser->id) }}" class="btn btn-outline btn-sm" style="display: flex; align-items: center; gap: 6px; border-radius: 8px; border:2px solid #10b981; color:#10b981; font-weight:600; padding:0.5rem 1.2rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        {{ __('Message') }}
                    </a>
                @endif
            @endauth
        </div>
    </div>

    {{-- الباقة الحالية --}}
    @php
        $activePackage = \App\Models\UserPackage::where('user_id', $profileUser->id)
            ->where('status', 'active')
            ->with('package')
            ->first();
    @endphp
    @if($activePackage)
    <div class="card" style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 2px solid #93c5fd; margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 2rem;">{{ $activePackage->package->badge ?? '📦' }}</span>
            <div>
                <strong style="font-size: 1.1rem;">{{ $activePackage->package->name }}</strong>
                <p style="color: var(--text-secondary); font-size: 0.85rem;">
                    {{ __('Ends') }}: {{ \Carbon\Carbon::parse($activePackage->end_date)->format('Y/m/d') }}
                    ({{ \Carbon\Carbon::parse($activePackage->end_date)->diffForHumans() }})
                </p>
            </div>
            @if(auth()->id() == $profileUser->id)
            <a href="{{ route('packages') }}" class="btn btn-primary btn-sm" style="margin-right: auto;">🔄 {{ __('Upgrade') }}</a>
            @endif
        </div>
    </div>
    @endif

    {{-- الإحصائيات --}}
    <div class="profile-stats">
        <a href="{{ route('profile.followers', $profileUser->id) }}" class="stat-item">
            <strong class="followers-count">{{ $followersCount }}</strong>
            <small>{{ __('Followers') }}</small>
        </a>
        <a href="{{ route('profile.following', $profileUser->id) }}" class="stat-item">
            <strong>{{ $followingCount }}</strong>
            <small>{{ __('Following') }}</small>
        </a>
        <div class="stat-item">
            <strong>{{ $myServices->count() }}</strong>
            <small>{{ __('Services') }}</small>
        </div>
        <div class="stat-item" onclick="openListModal('ratings')">
            <strong>{{ $avgRating ?: '—' }}</strong>
            <small>⭐ {{ __('Rating') }} ({{ $ratingsCount }})</small>
        </div>
    </div>

    {{-- المنشورات --}}
    @if($posts->count() > 0)
        <h3 class="section-title">📢 {{ __('Posts') }}</h3>
        <div class="posts-grid">
            @foreach($posts as $post)
                <a href="{{ route('post.show', $post->id) }}" class="post-item">
                    @if($post->image)<img src="{{ asset('uploads/posts/' . $post->image) }}">
                    @elseif($post->video)<video src="{{ asset('uploads/videos/' . $post->video) }}"></video>
                    @else<div class="post-text">{{ Str::limit($post->content, 50) }}</div>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    {{-- الخدمات --}}
    @if($profileUser->user_type === 'provider' && $myServices->count() > 0)
        <h3 class="section-title">🛠️ {{ __('Services') }}</h3>
        @foreach($myServices as $service)
            <a href="{{ route('service.show', $service->id) }}" class="service-item">
                <div><strong>{{ $service->title }}</strong>
                @if($service->description)<p>{{ Str::limit($service->description, 60) }}</p>@endif</div>
                <span class="service-price">{{ number_format($service->price, 2) }} ر.س</span>
            </a>
        @endforeach
    @endif

    {{-- ✅ معرض الأعمال المسلمة --}}
    @php
        $deliveredWorks = \App\Models\WorkDelivery::where('sender_id', $profileUser->id)
            ->where('status', '!=', 'sent')
            ->latest()
            ->take(9)
            ->get();
    @endphp
    @if($deliveredWorks->count() > 0)
        <h3 class="section-title">📁 {{ __('Portfolio') }}</h3>
        <div class="posts-grid">
            @foreach($deliveredWorks as $work)
                <div class="post-item" style="position:relative;">
                    @if($work->file_attachment)
                        @php $ext = pathinfo($work->file_name, PATHINFO_EXTENSION); @endphp
                        @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                            <img src="{{ asset('storage/'.$work->file_attachment) }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div class="post-text">{{ $work->title ?? __('Delivery') }}</div>
                        @endif
                    @else
                        <div class="post-text">{{ Str::limit($work->description ?? $work->title ?? __('Delivery'), 50) }}</div>
                    @endif
                    <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.6); color:white; padding:0.25rem; font-size:0.7rem; text-align:center;">
                        {{ $work->title ?? __('Delivery') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

{{-- نافذة التقييمات --}}
<div id="listModal" class="list-modal" onclick="if(event.target===this)closeListModal()">
    <div class="list-modal-content">
        <div class="list-modal-header"><h3 id="listModalTitle"></h3><button onclick="closeListModal()">✕</button></div>
        <div id="listModalContent"></div>
    </div>
</div>



@endsection