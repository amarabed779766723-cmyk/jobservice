@extends('layouts.app')
@section('title', __('My Bookings'))
@section('content')

<div style="max-width:800px; margin:0 auto; padding:1rem;">

    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Home') }}
    </a>

    <h2>📅 {{ __('My Bookings (as Client)') }}</h2>
    @foreach($myBookings as $b)
    <div class="card" style="margin-bottom:1rem; {{ $b->status === 'approved' ? 'border-right:4px solid #10b981;' : '' }}{{ $b->status === 'rejected' ? 'border-right:4px solid #ef4444;' : '' }}{{ $b->status === 'pending' ? 'border-right:4px solid #f59e0b;' : '' }}">
        <strong>{{ $b->service->title ?? '—' }}</strong>
        <p>📅 {{ $b->booking_date }} - ⏰ {{ $b->booking_time }}</p>
        <p>{{ __('Status') }}: 
            @if($b->status == 'pending')<span style="color:#f59e0b;">⏳ {{ __('Pending') }}</span>
            @elseif($b->status == 'approved')<span style="color:#10b981;">✅ {{ __('Approved') }}</span>
            @elseif($b->status == 'rejected')<span style="color:#ef4444;">❌ {{ __('Rejected') }}</span>
            @else<span style="color:#6b7280;">🚫 {{ __('Cancelled') }}</span>@endif
        </p>
        @if($b->provider_reply && $b->status != 'approved')
            <p style="background:#f9fafb; padding:0.5rem; border-radius:8px;">💬 {{ $b->provider_reply }}</p>
        @endif
        @if($b->suggested_date)
            <p style="color:#2563eb;">📅 {{ __('Alternative Suggestion') }}: {{ $b->suggested_date }} {{ $b->suggested_time }}</p>
        @endif
        
        @php $deliveries = \App\Models\WorkDelivery::where('booking_id', $b->id)->get(); @endphp
        @foreach($deliveries as $delivery)
        <div style="background:#f0fdf4; border:1px solid #86efac; padding:0.75rem; border-radius:8px; margin-top:0.5rem;">
            <strong>📦 {{ __('Delivery from') }}: {{ $delivery->sender->name }}</strong>
            @if($delivery->description)
                <p style="font-size:0.9rem;">{{ $delivery->description }}</p>
            @endif
            @if($delivery->file_attachment)
                <a href="{{ asset('storage/'.$delivery->file_attachment) }}" download class="btn btn-outline btn-sm">📎 {{ $delivery->file_name ?? __('Download File') }}</a>
            @endif
            
            @if(!$delivery->client_confirmed)
                <form method="POST" action="{{ route('delivery.confirm', $delivery->id) }}" style="margin-top:0.5rem;">
                    @csrf
                    <button class="btn btn-success btn-sm">✅ {{ __('Confirm Receipt') }}</button>
                </form>
            @else
                <span style="color:#10b981; font-weight:600;">✅ {{ __('Received') }}</span>
                
                @if($delivery->status != 'rated')
                <form method="POST" action="{{ route('delivery.rate', $delivery->id) }}" style="margin-top:0.5rem; background:#fff; padding:0.75rem; border-radius:8px;">
                    @csrf
                    <div class="star-rating" style="font-size:1.5rem; margin-bottom:0.5rem;">
                        <span data-value="1">☆</span><span data-value="2">☆</span><span data-value="3">☆</span><span data-value="4">☆</span><span data-value="5">☆</span>
                    </div>
                    <input type="hidden" name="score" id="scoreInput" value="0">
                    <textarea name="review" rows="2" placeholder="{{ __('Your comment...') }}" style="width:100%; padding:0.5rem; border-radius:8px; border:1px solid var(--border); font-family:'Tajawal',sans-serif; margin-bottom:0.5rem;"></textarea>
                    <button class="btn btn-primary btn-sm">⭐ {{ __('Rate') }}</button>
                </form>
                @else
                    <span style="color:#fbbf24;">⭐ {{ __('Rated') }}</span>
                @endif
            @endif
        </div>
        @endforeach
        
        @if($b->status == 'pending' || $b->status == 'approved')
            <a href="{{ route('booking.cancel', $b->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Cancel booking?') }}')" style="margin-top:0.5rem;">❌ {{ __('Cancel') }}</a>
        @endif
    </div>
    @endforeach

    @if(Auth::user()->user_type === 'provider')
    <h2 style="margin-top:2rem;">📥 {{ __('Booking Requests (as Provider)') }}</h2>
    @foreach($providerBookings as $b)
    <div class="card" style="margin-bottom:1rem;">
        <strong>{{ $b->service->title ?? '—' }}</strong>
        <p>👤 {{ __('Client') }}: {{ $b->client->name }}</p>
        <p>📅 {{ $b->booking_date }} - ⏰ {{ $b->booking_time }}</p>
        <p>{{ $b->notes ? '📝 ' . $b->notes : '' }}</p>
        <p>{{ __('Status') }}: 
            @if($b->status == 'pending')<span style="color:#f59e0b;">⏳ {{ __('Pending') }}</span>
            @elseif($b->status == 'approved')<span style="color:#10b981;">✅ {{ __('Approved') }}</span>
            @elseif($b->status == 'rejected')<span style="color:#ef4444;">❌ {{ __('Rejected') }}</span>
            @else<span style="color:#6b7280;">🚫 {{ __('Cancelled') }}</span>@endif
        </p>
        
        @if($b->status == 'pending')
        <div style="display:flex; gap:0.5rem; margin-top:0.5rem;">
            <form method="POST" action="{{ route('booking.approve', $b->id) }}" style="display:inline;">
                @csrf
                <button class="btn btn-success btn-sm">✅ {{ __('Approve') }}</button>
            </form>
            
            <form method="POST" action="{{ route('booking.reject', $b->id) }}" style="display:inline;">
                @csrf
                <input type="text" name="reply" placeholder="{{ __('Rejection reason...') }}" style="padding:0.3rem; border-radius:6px; border:1px solid #ddd;">
                <input type="date" name="suggested_date" style="padding:0.3rem; border-radius:6px; border:1px solid #ddd;" title="{{ __('Alternative date') }}">
                <input type="time" name="suggested_time" style="padding:0.3rem; border-radius:6px; border:1px solid #ddd;" title="{{ __('Alternative time') }}">
                <button class="btn btn-danger btn-sm">❌ {{ __('Reject') }}</button>
            </form>
        </div>
        @endif
        
        @if($b->status == 'approved')
            <a href="{{ route('delivery.form', $b->id) }}" class="btn btn-primary btn-sm" style="margin-top:0.5rem;">📤 {{ __('Send Work') }}</a>
        @endif
    </div>
    @endforeach
    @endif

</div>
@endsection