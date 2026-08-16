@extends('layouts.app')
@section('title', $serviceRequest->title . ' - Job Service')
@section('content')
<div style="max-width: 700px; margin: 0 auto; padding: 1.5rem 1rem;">
    <a href="{{ route('requests.index') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Requests') }}
    </a>

    <div class="card" style="margin-top: 1rem;">
        <div class="card-header">
            <img src="{{ asset('uploads/avatars/' . ($serviceRequest->user->avatar ?? 'default-avatar.png')) }}" class="card-avatar" alt="">
            <div>
                <div class="card-user">{{ $serviceRequest->user->name ?? __('Client') }}</div>
                <div class="card-meta">{{ \Carbon\Carbon::parse($serviceRequest->created_at)->format('Y/m/d') }}</div>
            </div>
        </div>
        <h2>{{ $serviceRequest->title }}</h2>
        <p>{{ $serviceRequest->description ?? __('No Description') }}</p>
        <div class="card-footer">
            <span class="card-price">{{ __('Budget') }}: {{ number_format($serviceRequest->budget, 2) }} ر.س</span>
            <span>{{ __('Status') }}: 
                {{ $serviceRequest->status === 'open' ? '🟢 '.__('Pending') : ($serviceRequest->status === 'in_progress' ? '🟡 '.__('Approved') : '🟢 '.__('Completed')) }}
            </span>
        </div>
    </div>

    @if($offers->count() > 0)
        <h3 style="margin-top: 1.5rem;">📝 {{ __('Offers') }} ({{ $offers->count() }})</h3>
        @foreach($offers as $offer)
            <div class="card" style="margin-bottom: 0.75rem; background: {{ $offer->status === 'accepted' ? '#d1fae5' : 'var(--bg-card)' }}; border: {{ $offer->status === 'accepted' ? '2px solid #10b981' : '1px solid var(--border)' }};">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <img src="{{ asset('uploads/avatars/' . ($offer->provider->avatar ?? 'default-avatar.png')) }}" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div style="flex: 1;">
                        <strong>{{ $offer->provider->name }}</strong>
                        <span style="color: var(--primary); font-weight: 700; margin-right: 0.5rem;">{{ number_format($offer->price, 2) }} ر.س</span>
                        @if($offer->status === 'accepted')
                            <span style="color: #10b981; font-weight: 700;">✅ {{ __('Approved') }}</span>
                        @endif
                    </div>
                </div>
                @if($offer->message)
                    <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">{{ $offer->message }}</p>
                @endif

                @auth
                    @if($isOwner && $serviceRequest->status === 'open' && $offer->status === 'pending')
                        <form method="POST" action="{{ route('offer.accept', $offer->id) }}" style="margin-top: 0.5rem;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('{{ __("Cancel booking?") }}')">✅ {{ __('Approve') }}</button>
                        </form>
                    @endif
                @endauth
            </div>
        @endforeach
    @endif

    @auth
        @if($serviceRequest->status === 'open' && auth()->user()->user_type === 'provider' && auth()->id() != $serviceRequest->user_id)
            <div class="card" style="margin-top: 1rem;">
                <h3>📝 {{ __('Submit Offer') }}</h3>
                <form method="POST" action="{{ route('offer.submit', $serviceRequest->id) }}">
                    @csrf
                    <div class="input-group">
                        <label>{{ __('Price') }} (ر.س)</label>
                        <input type="number" name="price" step="0.01" required>
                    </div>
                    <div class="input-group">
                        <label>{{ __('Message') }}</label>
                        <textarea name="message" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Submit Offer') }}</button>
                </form>
            </div>
        @endif
    @endauth

    @auth
        @if($isOwner && $acceptedOffer && $serviceRequest->status === 'in_progress')
            <div class="card" style="margin-top: 1rem;">
                <h3>⭐ {{ __('Complete Request') }}</h3>
                <form method="POST" action="{{ route('request.complete', $serviceRequest->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="width:100%;">✅ {{ __('Complete') }}</button>
                </form>
            </div>
        @endif
    @endauth
</div>
@endsection