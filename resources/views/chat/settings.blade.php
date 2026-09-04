@extends('layouts.chat')
@section('title', __('Chat Settings'))
@section('content')

<div style="max-width:600px; margin:0 auto; padding:1.5rem;">
    <a href="{{ route('chat.show', $chat->id) }}" class="back-link">← {{ __('Back to Chat') }}</a>
    <h2>⚙️ {{ __('Chat Settings') }}</h2>

    @php $userSettings = json_decode(\Illuminate\Support\Facades\DB::table('users')->where('id', auth()->id())->value('settings') ?? '{}', true); @endphp

    {{-- 🎨 خلفيات جاهزة --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:1rem;">🎨 {{ __('Choose Wallpaper') }}</h3>
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:0.75rem;">
            @php
            $wallpapers = [
                ['name' => __('Default'), 'color' => '#e5ddd5'],
                ['name' => __('Green'), 'color' => '#dcf8c6'],
                ['name' => __('Blue'), 'color' => '#dbeafe'],
                ['name' => __('Pink'), 'color' => '#fce7f3'],
                ['name' => __('Purple'), 'color' => '#ede9fe'],
                ['name' => __('Orange'), 'color' => '#ffedd5'],
                ['name' => __('Cyan'), 'color' => '#cffafe'],
                ['name' => __('Gray'), 'color' => '#f3f4f6'],
            ];
            @endphp
            @foreach($wallpapers as $wp)
            <form method="POST" action="{{ route('chat.settings.save', $chat->id) }}">
                @csrf
                <input type="hidden" name="type" value="color">
                <input type="hidden" name="value" value="{{ $wp['color'] }}">
                <button type="submit" style="width:100%; aspect-ratio:1; background:{{ $wp['color'] }}; border:3px solid {{ ($userSettings['chat_wallpaper'] ?? '') == $wp['color'] ? '#2563eb' : '#e5e7eb' }}; border-radius:12px; cursor:pointer;" title="{{ $wp['name'] }}"></button>
                <p style="text-align:center; font-size:0.75rem; margin-top:0.25rem;">{{ $wp['name'] }}</p>
            </form>
            @endforeach
        </div>
    </div>

    {{-- 🖼️ رفع صورة خلفية --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:0.5rem;">🖼️ {{ __('Upload Custom Wallpaper') }}</h3>
        <form method="POST" action="{{ route('chat.settings.save', $chat->id) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="image">
            <input type="file" name="wallpaper_image" accept="image/*" style="margin-bottom:0.5rem;">
            <button type="submit" class="btn btn-primary btn-sm">{{ __('Upload') }}</button>
        </form>
    </div>

    {{-- 🔤 حجم الخط --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:0.5rem;">🔤 {{ __('Font Size') }}</h3>
        <form method="POST" action="{{ route('chat.settings.save', $chat->id) }}">
            @csrf
            <input type="hidden" name="type" value="font_size">
            @php $currentFont = $userSettings['font_size'] ?? 'medium'; @endphp
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" name="value" value="small" class="btn btn-sm {{ $currentFont == 'small' ? 'btn-primary' : 'btn-outline' }}">{{ __('Small') }}</button>
                <button type="submit" name="value" value="medium" class="btn btn-sm {{ $currentFont == 'medium' ? 'btn-primary' : 'btn-outline' }}">{{ __('Medium') }}</button>
                <button type="submit" name="value" value="large" class="btn btn-sm {{ $currentFont == 'large' ? 'btn-primary' : 'btn-outline' }}">{{ __('Large') }}</button>
            </div>
        </form>
    </div>

    {{-- 🗑️ حذف المحادثة --}}
    <div class="card" style="border:1px solid #fecaca;">
        <h3 style="color:#dc2626; margin-bottom:0.5rem;">🗑️ {{ __('Delete Chat') }}</h3>
        <p style="color:var(--text-secondary); margin-bottom:1rem; font-size:0.9rem;">{{ __('All messages will be deleted permanently.') }}</p>
        <form method="POST" action="{{ route('chat.settings.save', $chat->id) }}" onsubmit="return confirm('{{ __('Delete all messages?') }}')">
            @csrf
            <input type="hidden" name="type" value="delete_chat">
            <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete Chat') }}</button>
        </form>
    </div>
</div>
@endsection