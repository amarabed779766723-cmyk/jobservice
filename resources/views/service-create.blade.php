@extends('layouts.app')
@section('title', __('Create Service') . ' - Job Service')
@section('content')
<div style="max-width: 650px; margin: 0 auto; padding: 1.5rem 1rem;">
    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Home') }}
    </a>
    <h2>🛠️ {{ __('Create Service') }}</h2>

    @if(session('success'))<div class="success-msg">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="error-msg">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('service.store') }}" enctype="multipart/form-data" class="card">
        @csrf
        <div class="input-group">
            <label>{{ __('Service Title') }}</label>
            <input type="text" name="title" required>
        </div>
        <div class="input-group">
            <label>{{ __('Service Description') }}</label>
            <textarea name="description" rows="4"></textarea>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="input-group">
                <label>{{ __('Price') }} (ر.س)</label>
                <input type="number" name="price" step="0.01" min="1" required>
            </div>
            <div class="input-group">
                <label>{{ __('Duration') }}</label>
                <input type="text" name="duration" placeholder="{{ __('e.g. 3 days') }}">
            </div>
        </div>
        <div class="input-group">
            <label>{{ __('Service Image') }}</label>
            <input type="file" name="image">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">{{ __('Publish Service') }}</button>
    </form>
</div>
</div>
@endsection