@extends('layouts.app')
@section('title', __('Followers of') . ' ' . $profileUser->name)
@section('content')

<div class="container" style="max-width:600px; margin:2rem auto; padding:0 1rem;">
    
    <a href="{{ route('profile', $profileUser->id) }}" class="back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Return to Profile') }}
    </a>
    
    <h2 style="margin-bottom:1.5rem; font-size:1.3rem;">👥 {{ __('Followers of') }} {{ $profileUser->name }}</h2>
    
    @if($followers->count() > 0)
        @foreach($followers as $follow)
            @php
                $followerUser = $follow->follower;
                $isFollowingBack = $follow->is_followed_by_me ?? false;
            @endphp
            <div style="display:flex; align-items:center; justify-content:space-between; padding:0.8rem 0; border-bottom:1px solid var(--border);">
                <a href="{{ route('profile', $followerUser->id) }}" style="display:flex; align-items:center; gap:1rem; text-decoration:none; color:var(--text); flex:1;">
                    <img src="{{ asset('uploads/avatars/' . ($followerUser->avatar ?? 'default-avatar.png')) }}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
                    <div>
                        <strong>{{ $followerUser->name }}</strong>
                        <p style="color:var(--text-secondary);font-size:0.8rem;margin:0;">{{ $followerUser->user_type === 'provider' ? __('Provider') : __('Client') }}</p>
                    </div>
                </a>
                <button class="btn btn-sm follow-action-btn" data-user-id="{{ $followerUser->id }}"
                        style="border-radius:8px; font-weight:600; padding:0.4rem 1rem; min-width:120px; transition:all 0.3s;
                        {{ $isFollowingBack ? 'background:#374151; color:white; border:none;' : 'background:#2563eb; color:white; border:none;' }}">
                    {{ $isFollowingBack ? '✓ '.__('Followed') : '+ '.__('Follow Back') }}
                </button>
            </div>
        @endforeach
        {{ $followers->links() }}
    @else
        <p style="text-align:center; color:var(--text-secondary); padding:2rem;">{{ __('No Followers Yet') }}</p>
    @endif

</div>


@endsection