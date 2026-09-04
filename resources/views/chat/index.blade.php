@extends('layouts.chat')
@section('title', __('Chats'))
@section('content')
<div style="max-width:600px; margin:0 auto; padding:1.5rem 1rem;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
        <div style="display:flex; align-items:center; gap:1rem;">
            <a href="{{ route('home') }}" style="color:var(--primary); text-decoration:none;">← {{ __('Home') }}</a>
            <h2>💬 {{ __('Chats') }}</h2>
        </div>
    </div>

    @if($chats->isEmpty())
        <div class="card"><p>{{ __('No chats yet') }}</p></div>
    @endif

    @foreach($chats as $chat)
        @php 
            $other = $chat->participants->where('id', '!=', Auth::id())->first();
        @endphp
        @if($other)
        <a href="{{ route('chat.show', $chat->id) }}" style="text-decoration:none; color:inherit;">
            <div class="card" style="display:flex; align-items:center; gap:1rem;">
                <img src="{{ asset('uploads/avatars/' . ($other->avatar ?? 'default-avatar.png')) }}" style="width:48px;height:48px;border-radius:50%;">
                <div style="flex:1;">
                    <strong>{{ $other->name }}</strong>
                    <p style="color:var(--text-secondary); margin:0;">{{ $chat->messages->first()->message_text ?? __('No messages') }}</p>
                </div>
                @if($chat->messages->first())
                <small style="color:var(--text-secondary);">{{ \Carbon\Carbon::parse($chat->messages->first()->created_at)->format('H:i') }}</small>
                @endif
            </div>
        </a>
        @endif
    @endforeach
</div>

{{-- نافذة إعدادات الدردشة --}}
<div id="chatSettingsModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:var(--bg-card); border-radius:12px; padding:1.5rem; width:90%; max-width:400px;">
        <h3 style="margin-bottom:1rem;">⚙️ {{ __('Chat Settings') }}</h3>
        
        <div style="margin-bottom:1rem;">
            <label style="font-weight:500; display:block; margin-bottom:0.5rem;">🎨 {{ __('Chat Wallpaper') }}</label>
            <form method="POST" action="{{ route('settings.chat_wallpaper') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="chat_wallpaper" accept="image/*">
                <button type="submit" class="btn btn-primary btn-sm" style="margin-top:0.5rem;">{{ __('Change') }}</button>
            </form>
        </div>

        <button onclick="toggleChatSettings()" class="btn btn-outline btn-sm" style="width:100%;">{{ __('Close') }}</button>
    </div>
</div>

<script>
function toggleChatSettings() {
    var modal = document.getElementById('chatSettingsModal');
    modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
}
</script>
@endsection