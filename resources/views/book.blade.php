@extends('layouts.app')
@section('title', __('Book') . ' ' . __('Service'))
@section('content')
<main style="max-width:600px; margin:0 auto; padding:1.5rem 1rem;">
    <a href="{{ route('service.show', $service->id) }}" class="back-link">← {{ __('Back to Service') }}</a>

    <h2 style="margin: 1rem 0;">📅 {{ __('Book') }}: {{ $service->title }}</h2>
    <p>{{ __('Service Provider') }}: <strong>{{ $service->provider->name }}</strong></p>
    <p>{{ __('Price') }}: <strong>{{ number_format($service->price, 2) }} ر.س</strong></p>

    @if($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('booking.store', $service->id) }}" class="card">
        @csrf
        <div class="input-group">
            <label>📅 {{ __('Booking Date') }}</label>
            <input type="date" name="booking_date" min="{{ date('Y-m-d') }}" required>
        </div>
        <div class="input-group">
            <label>⏰ {{ __('Suitable Time') }}</label>
            <select name="booking_time" required>
                <option value="">{{ __('Choose Time') }}</option>
                @for ($h = 8; $h <= 20; $h++)
                    <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00</option>
                    <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:30">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:30</option>
                @endfor
            </select>
        </div>
        <div class="input-group">
            <label>📝 {{ __('Notes (Optional)') }}</label>
            <textarea name="notes" rows="3" placeholder="{{ __('Any additional details...') }}"></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">{{ __('Confirm Booking') }}</button>
    </form>
</main>
@endsection