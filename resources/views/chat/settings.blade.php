@extends('layouts.chat')
@section('title', 'إعدادات الدردشة')
@section('content')

<div style="max-width:600px; margin:0 auto; padding:1.5rem;">
    <a href="{{ route('chat.show', $chat->id) }}" class="back-link">← العودة للمحادثة</a>
    <h2>⚙️ إعدادات الدردشة</h2>

    @php $userSettings = json_decode(\Illuminate\Support\Facades\DB::table('users')->where('id', auth()->id())->value('settings') ?? '{}', true); @endphp

    {{-- 🎨 خلفيات جاهزة --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:1rem;">🎨 اختر خلفية</h3>
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:0.75rem;">
            @php
            $wallpapers = [
                ['name' => 'افتراضي', 'color' => '#e5ddd5'],
                ['name' => 'أخضر', 'color' => '#dcf8c6'],
                ['name' => 'أزرق', 'color' => '#dbeafe'],
                ['name' => 'وردي', 'color' => '#fce7f3'],
                ['name' => 'بنفسجي', 'color' => '#ede9fe'],
                ['name' => 'برتقالي', 'color' => '#ffedd5'],
                ['name' => 'سماوي', 'color' => '#cffafe'],
                ['name' => 'رمادي', 'color' => '#f3f4f6'],
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
        <h3 style="margin-bottom:0.5rem;">🖼️ رفع خلفية مخصصة</h3>
        <form method="POST" action="{{ route('chat.settings.save', $chat->id) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="image">
            <input type="file" name="wallpaper_image" accept="image/*" style="margin-bottom:0.5rem;">
            <button type="submit" class="btn btn-primary btn-sm">رفع</button>
        </form>
    </div>

    {{-- 🔤 حجم الخط --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:0.5rem;">🔤 حجم الخط</h3>
        <form method="POST" action="{{ route('chat.settings.save', $chat->id) }}">
            @csrf
            <input type="hidden" name="type" value="font_size">
            @php $currentFont = $userSettings['font_size'] ?? 'medium'; @endphp
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" name="value" value="small" class="btn btn-sm {{ $currentFont == 'small' ? 'btn-primary' : 'btn-outline' }}">صغير</button>
                <button type="submit" name="value" value="medium" class="btn btn-sm {{ $currentFont == 'medium' ? 'btn-primary' : 'btn-outline' }}">متوسط</button>
                <button type="submit" name="value" value="large" class="btn btn-sm {{ $currentFont == 'large' ? 'btn-primary' : 'btn-outline' }}">كبير</button>
            </div>
        </form>
    </div>

    {{-- 🗑️ حذف المحادثة --}}
    <div class="card" style="border:1px solid #fecaca;">
        <h3 style="color:#dc2626; margin-bottom:0.5rem;">🗑️ حذف المحادثة</h3>
        <p style="color:var(--text-secondary); margin-bottom:1rem; font-size:0.9rem;">سيتم حذف جميع الرسائل نهائياً.</p>
        <form method="POST" action="{{ route('chat.settings.save', $chat->id) }}" onsubmit="return confirm('حذف جميع الرسائل؟')">
            @csrf
            <input type="hidden" name="type" value="delete_chat">
            <button type="submit" class="btn btn-danger btn-sm">حذف المحادثة</button>
        </form>
    </div>
</div>
@endsection