@extends('layouts.admin')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <h2>📢 رقابة المنشورات</h2>
        
        @if(session('success'))
            <div style="background:#d1fae5; color:#065f46; padding:1rem; border-radius:8px; margin-bottom:1rem;">{{ session('success') }}</div>
        @endif
        
        <table class="admin-table">
            <thead><tr><th>ID</th><th>الناشر</th><th>المحتوى</th><th>صورة</th><th>تاريخ</th><th>حالة</th><th>إجراء</th></tr></thead>
            <tbody>
                @foreach($posts as $post)
                <tr>
                    <td>{{ $post->id }}</td>
                    <td>{{ $post->user->name }}</td>
                    <td>{{ Str::limit($post->content, 50) }}</td>
                    <td>{{ $post->image ? '✅' : '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($post->created_at)->format('Y/m/d') }}</td>
                    <td>
                        @if($post->is_approved)
                            <span style="color:#10b981;">✅ مقبول</span>
                        @else
                            <span style="color:#f59e0b;">⏳ قيد الانتظار</span>
                        @endif
                    </td>
                    <td style="display:flex; gap:0.25rem; flex-wrap:wrap;">
                        {{-- ✅ زر العرض --}}
                        <button onclick='showPost({
                            id: {{ $post->id }},
                            user: "{{ $post->user->name }}",
                            content: "{{ Str::limit($post->content, 200) }}",
                            image: "{{ $post->image ? asset('uploads/posts/'.$post->image) : '' }}",
                            video: "{{ $post->video ? asset('uploads/videos/'.$post->video) : '' }}",
                            file: "{{ $post->file_attachment ? asset('uploads/posts/'.$post->file_attachment) : '' }}",
                            fileName: "{{ $post->file_name ?? '' }}",
                            isApproved: {{ $post->is_approved ? 'true' : 'false' }}
                        })' class="btn btn-outline btn-sm">👁️ عرض</button>
                        
                        @if(!$post->is_approved)
                        <form method="POST" action="/admin/posts/approve" style="display:inline;">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                            <button type="submit" class="btn btn-success btn-sm">✅ قبول</button>
                        </form>
                        @endif
                        <form method="POST" action="/admin/posts/delete" style="display:inline;">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('حذف هذا المنشور؟')">🗑️ حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</div>

{{-- ✅ نافذة عرض المنشور --}}
<div id="postModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--bg-card); border-radius:16px; padding:1.5rem; width:90%; max-width:550px; max-height:85vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h3 id="modalTitle" style="margin:0;">📢 معاينة المنشور</h3>
            <button onclick="closePostModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text);">✕</button>
        </div>
        
        <div id="modalContent"></div>
    </div>
</div>

<script>
function showPost(post) {
    var html = '';
    
    html += '<p style="margin-bottom:0.5rem;"><strong>الناشر:</strong> ' + post.user + '</p>';
    html += '<p style="margin-bottom:0.5rem;"><strong>المحتوى:</strong> ' + (post.content || 'لا يوجد نص') + '</p>';
    
    if (post.image) {
        html += '<div style="margin-bottom:0.5rem;"><strong>الصورة:</strong><br><img src="' + post.image + '" style="max-width:100%; max-height:300px; border-radius:8px; margin-top:0.5rem;"></div>';
    }
    
    if (post.video) {
        html += '<div style="margin-bottom:0.5rem;"><strong>الفيديو:</strong><br><video src="' + post.video + '" controls style="max-width:100%; max-height:300px; border-radius:8px; margin-top:0.5rem;"></video></div>';
    }
    
    if (post.file) {
        html += '<div style="margin-bottom:0.5rem;"><strong>الملف:</strong> <a href="' + post.file + '" download style="color:var(--primary);">📎 ' + (post.fileName || 'تحميل الملف') + '</a></div>';
    }
    
    html += '<p style="margin-bottom:0.5rem;"><strong>الحالة:</strong> ' + (post.isApproved ? '<span style="color:#10b981;">✅ مقبول</span>' : '<span style="color:#f59e0b;">⏳ قيد الانتظار</span>') + '</p>';
    
    document.getElementById('modalContent').innerHTML = html;
    document.getElementById('postModal').style.display = 'flex';
}

function closePostModal() {
    document.getElementById('postModal').style.display = 'none';
}

// إغلاق النافذة عند الضغط خارجها
document.getElementById('postModal').addEventListener('click', function(e) {
    if (e.target === this) closePostModal();
});
</script>
@endsection