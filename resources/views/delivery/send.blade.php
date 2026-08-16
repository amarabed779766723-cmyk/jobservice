@extends('layouts.app')
@section('title', __('Delivery'))
@section('content')
<div style="max-width:600px; margin:0 auto; padding:1rem;">
    <a href="{{ route('bookings.index') }}" class="back-link">← {{ __('My Bookings') }}</a>
    <h2 style="margin-top:1rem;">📦 {{ __('Delivery') }}</h2>
    
    <div class="card">
        <h4>{{ $booking->service->title }}</h4>
        <p style="color:var(--text-secondary);">{{ __('Client Name') }}: {{ $booking->client->name }}</p>
        
        <form method="POST" action="{{ route('delivery.send', $booking->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="input-group">
                <label>{{ __('Work Description (Optional)') }}</label>
                <textarea name="description" rows="3" placeholder="{{ __('Brief description of work...') }}"></textarea>
            </div>
            <div class="input-group">
                <label>{{ __('Work File (Optional)') }}</label>
                <input type="file" name="file">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">📤 {{ __('Send Work') }}</button>
        </form>
    </div>
</div>
@endsection