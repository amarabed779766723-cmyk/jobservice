@extends('layouts.app')
@section('title', __('Create Request') . ' - Job Service')
@section('content')
<div style="max-width: 650px; margin: 0 auto; padding: 1.5rem 1rem;">
    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Home') }}
    </a>
    <h2>📝 {{ __('Create Request') }}</h2>

    @if(session('error'))<div class="error-msg">⚠️ {!! session('error') !!}</div>@endif
    @if(session('success'))<div class="success-msg">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="error-msg">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('request.store') }}" class="card">
        @csrf
        <div class="input-group">
            <label>{{ __('Request Title') }}</label>
            <input type="text" name="title" required>
        </div>
        <div class="input-group">
            <label>{{ __('Request Description') }}</label>
            <textarea name="description" rows="4"></textarea>
        </div>
        <div class="input-group">
            <label>{{ __('Budget') }} (ر.س)</label>
            <input type="number" name="budget" step="0.01" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">{{ __('Publish Request') }}</button>
    </form>
</div>
@endsection