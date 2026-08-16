@extends('layouts.chat')
@section('title', 'المحادثة')
@php $userSettings = json_decode(\Illuminate\Support\Facades\DB::table('users')->where('id', auth()->id())->value('settings') ?? '{}', true); @endphp
@section('content')

<div class="chat-container">
    <div class="chat-header">
        <a href="{{ route('chats.index') }}">←</a>
        <img src="{{ asset('uploads/avatars/' . ($otherUser->avatar ?? 'default-avatar.png')) }}" style="width:40px;height:40px;border-radius:50%;">
        <strong style="flex:1;">{{ $otherUser->name }}</strong>
        <a href="{{ route('chat.settings', $chat->id) }}" style="color:white; text-decoration:none; font-size:1.1rem; opacity:0.8;">⚙️</a>
    </div>

    <div class="messages-area" id="messagesBox">
        @foreach($messages as $msg)
        <div class="msg-row {{ $msg->sender_id == Auth::id() ? 'me' : 'other' }}" data-id="{{ $msg->id }}" data-text="{{ $msg->message_text ?? '' }}">
            <div class="msg-bubble">
                @if($msg->reply_to)
                    @php $replyMsg = $msg->replyTo; @endphp
                    @if($replyMsg)
                    <div class="reply-preview">{{ $replyMsg->sender->name ?? '' }}: {{ $replyMsg->message_text ?? 'صورة' }}</div>
                    @endif
                @endif
                @if($msg->image)
                    <img src="{{ asset('uploads/chats/'.$msg->image) }}" alt="صورة">
                    <a href="{{ asset('uploads/chats/'.$msg->image) }}" download class="download-btn">⬇️ تحميل</a>
                @endif
                @if($msg->file_attachment)
                    <div>📎 {{ $msg->file_name ?? 'ملف' }} <a href="{{ asset('uploads/chats/'.$msg->file_attachment) }}" download class="download-btn">⬇️</a></div>
                @endif
                {{ $msg->message_text }}
                <div class="msg-time">{{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}</div>
            </div>
            <div class="msg-actions">
                <button onclick="replyTo({{ $msg->id }}, '{{ $msg->sender->name }}', '{{ $msg->message_text ?? 'صورة' }}')" title="رد">↩️</button>
                @if($msg->sender_id == Auth::id())
                <button onclick="editMsg({{ $msg->id }}, '{{ $msg->message_text ?? '' }}')" title="تعديل">✏️</button>
                <button onclick="deleteMsg({{ $msg->id }})" title="حذف">🗑️</button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div id="filesPreview" class="files-preview" style="display:none;">
        <button onclick="cancelAllFiles()" style="background:#ef4444;color:white;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:0.6rem;">✕</button>
        <div id="previewList" style="display:flex;flex-wrap:wrap;gap:0.5rem;"></div>
    </div>

    <div id="replyBar" class="reply-bar" style="display:none;">
        <span id="replyText"></span>
        <button onclick="cancelReply()" style="background:none;border:none;cursor:pointer;">✕</button>
        <input type="hidden" id="replyToInput">
    </div>

    <div class="chat-footer">
        <label>📷<input type="file" id="imageInput" accept="image/*" multiple hidden onchange="addFiles('image')"></label>
        <label>📎<input type="file" id="fileInput" multiple hidden onchange="addFiles('file')"></label>
        <input type="text" id="messageInput" placeholder="اكتب رسالة..." autocomplete="off">
        <button onclick="sendMsg()">➤</button>
    </div>
</div>

<script>
var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var files = [];

function addFiles(type) {
    var input = type === 'image' ? document.getElementById('imageInput') : document.getElementById('fileInput');
    for (var f of input.files) { files.push({file: f, type: type}); }
    updatePreview(); input.value = '';
}
function removeFile(i) { files.splice(i, 1); updatePreview(); }
function cancelAllFiles() { files = []; updatePreview(); }
function updatePreview() {
    var list = document.getElementById('previewList'), preview = document.getElementById('filesPreview');
    list.innerHTML = '';
    files.forEach(function(item, i) {
        var div = document.createElement('div'); div.style.cssText = 'position:relative;';
        if (item.type === 'image') {
            var reader = new FileReader();
            reader.onload = function(e) { div.innerHTML = '<img src="' + e.target.result + '" style="width:50px;height:50px;object-fit:cover;border-radius:4px;"><button onclick="removeFile(' + i + ')" style="position:absolute;top:-5px;right:-5px;background:#ef4444;color:white;border:none;border-radius:50%;width:18px;height:18px;cursor:pointer;font-size:0.6rem;">✕</button>'; };
            reader.readAsDataURL(item.file);
        } else {
            div.innerHTML = '<div style="font-size:0.7rem;background:#e5e7eb;padding:0.25rem;border-radius:4px;">📎 ' + item.file.name + ' <button onclick="removeFile(' + i + ')" style="background:#ef4444;color:white;border:none;border-radius:50%;width:16px;height:16px;cursor:pointer;font-size:0.5rem;">✕</button></div>';
        }
        list.appendChild(div);
    });
    preview.style.display = files.length ? 'flex' : 'none';
}
function sendMsg() {
    var text = document.getElementById('messageInput').value.trim();
    if (!text && files.length === 0) return;
    var formData = new FormData();
    formData.append('message_text', text);
    formData.append('reply_to', document.getElementById('replyToInput').value || '');
    files.forEach(function(f) {
        if (f.type === 'image') formData.append('image', f.file);
        else { formData.append('file_attachment', f.file); formData.append('file_name', f.file.name); }
    });
    fetch('/chat/{{ $chat->id }}/send', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }, body: formData })
    .then(r => r.json()).then(data => { if (data.success) location.reload(); });
}
function replyTo(id, sender, text) {
    document.getElementById('replyToInput').value = id;
    document.getElementById('replyText').innerHTML = '↩️ رد على ' + sender + ': ' + (text || 'صورة').substring(0, 40);
    document.getElementById('replyBar').style.display = 'flex';
    document.getElementById('messageInput').focus();
}
function cancelReply() { document.getElementById('replyToInput').value = ''; document.getElementById('replyBar').style.display = 'none'; }
function editMsg(id, oldText) {
    var newText = prompt('تعديل الرسالة:', oldText);
    if (newText && newText !== oldText) {
        fetch('/chat/message/' + id, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ message_text: newText }) })
        .then(r => r.json()).then(data => { if (data.success) location.reload(); });
    }
}
function deleteMsg(id) {
    if (!confirm('حذف الرسالة؟')) return;
    fetch('/chat/message/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf }})
    .then(r => r.json()).then(data => { if (data.success) document.querySelector('.msg-row[data-id="' + id + '"]').remove(); });
}
document.getElementById('messagesBox').scrollTop = document.getElementById('messagesBox').scrollHeight;
</script>

<style>
.msg-actions { display:flex; gap:0.1rem; margin-top:0.1rem; }
.msg-actions button { background:transparent; border:none; cursor:pointer; font-size:0.55rem; padding:0; border-radius:50%; opacity:0.4; width:18px; height:18px; display:flex; align-items:center; justify-content:center; }
.msg-actions button:hover { opacity:0.8; background:rgba(0,0,0,0.05); }
.msg-row.me .msg-actions { justify-content:flex-end; }
.msg-row.other .msg-actions { justify-content:flex-start; }
</style>
@php
$bg = $userSettings['chat_wallpaper'] ?? '#e5ddd5';
$isImage = $bg && str_contains($bg, '.');
@endphp

<style>
.messages-area {
    @if($isImage)
        background-image: url('{{ asset('uploads/chat_wallpapers/'.$bg) }}');
        background-size: cover;
        background-position: center;
    @else
        background-color: {{ $bg }};
    @endif
}

.msg-bubble {
    font-size: {{ ($userSettings['font_size'] ?? 'medium') == 'small' ? '0.8rem' : (($userSettings['font_size'] ?? 'medium') == 'large' ? '1.1rem' : '0.95rem') }};
}
</style>

@endsection