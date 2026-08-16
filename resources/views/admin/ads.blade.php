@extends('layouts.admin')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <h2>📢 إدارة الإعلانات</h2>
        
        @if(session('success'))
            <div style="background:#d1fae5; color:#065f46; padding:1rem; border-radius:8px; margin-bottom:1rem;">{{ session('success') }}</div>
        @endif
        
        <table class="admin-table">
            <thead><tr><th>ID</th><th>المعلن</th><th>الصورة</th><th>النص</th><th>الباقة</th><th>تاريخ</th><th>حالة</th><th>إجراء</th></tr></thead>
            <tbody>
                @foreach($ads as $ad)
                <tr>
                    <td>{{ $ad->id }}</td>
                    <td>{{ $ad->user->name }}</td>
                    <td><img src="{{ asset('uploads/stories/' . $ad->image) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;"></td>
                    <td>{{ Str::limit($ad->caption, 30) }}</td>
                    <td>{{ optional(\App\Models\AdPackage::find($ad->package_id))->name ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($ad->created_at)->format('Y/m/d') }}</td>
                    <td>
                        @if($ad->status == 'pending')<span style="color:#f59e0b;">⏳ قيد الانتظار</span>
                        @elseif($ad->status == 'approved')<span style="color:#10b981;">✅ مقبول</span>
                        @else<span style="color:#ef4444;">❌ مرفوض</span>@endif
                    </td>
                    <td style="display:flex; gap:0.25rem; flex-wrap:wrap;">
                        {{-- ✅ زر العرض --}}
                        <button onclick='showAd({
                            id: {{ $ad->id }},
                            user: "{{ $ad->user->name }}",
                            caption: "{{ $ad->caption ?? '' }}",
                            image: "{{ asset('uploads/stories/' . $ad->image) }}",
                            isVideo: {{ $ad->is_video ? 'true' : 'false' }},
                            package: "{{ optional(\App\Models\AdPackage::find($ad->package_id))->name ?? '—' }}",
                            status: "{{ $ad->status }}"
                        })' class="btn btn-outline btn-sm">👁️ عرض</button>
                        
                        @if($ad->status == 'pending')
                        <form method="POST" action="/admin/ads/approve/{{ $ad->id }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">✅ قبول</button>
                        </form>
                        <form method="POST" action="/admin/ads/reject/{{ $ad->id }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">❌ رفض</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</div>

{{-- ✅ نافذة عرض الإعلان --}}
<div id="adModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--bg-card); border-radius:16px; padding:1.5rem; width:90%; max-width:500px; max-height:85vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h3 id="adModalTitle" style="margin:0;">📢 معاينة الإعلان</h3>
            <button onclick="closeAdModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text);">✕</button>
        </div>
        
        <div id="adModalContent"></div>
    </div>
</div>

<script>
function showAd(ad) {
    var html = '';
    
    html += '<p style="margin-bottom:0.5rem;"><strong>المعلن:</strong> ' + ad.user + '</p>';
    html += '<p style="margin-bottom:0.5rem;"><strong>النص:</strong> ' + (ad.caption || 'لا يوجد') + '</p>';
    html += '<p style="margin-bottom:0.5rem;"><strong>الباقة:</strong> ' + ad.package + '</p>';
    
    if (ad.isVideo) {
        html += '<div style="margin-bottom:0.5rem;"><strong>الفيديو:</strong><br><video src="' + ad.image + '" controls style="max-width:100%; max-height:300px; border-radius:8px; margin-top:0.5rem;"></video></div>';
    } else {
        html += '<div style="margin-bottom:0.5rem;"><strong>الصورة:</strong><br><img src="' + ad.image + '" style="max-width:100%; max-height:300px; border-radius:8px; margin-top:0.5rem;"></div>';
    }
    
    var statusText = '';
    if (ad.status === 'pending') statusText = '<span style="color:#f59e0b;">⏳ قيد الانتظار</span>';
    else if (ad.status === 'approved') statusText = '<span style="color:#10b981;">✅ مقبول</span>';
    else statusText = '<span style="color:#ef4444;">❌ مرفوض</span>';
    
    html += '<p style="margin-bottom:0.5rem;"><strong>الحالة:</strong> ' + statusText + '</p>';
    
    document.getElementById('adModalContent').innerHTML = html;
    document.getElementById('adModal').style.display = 'flex';
}

function closeAdModal() {
    document.getElementById('adModal').style.display = 'none';
}

document.getElementById('adModal').addEventListener('click', function(e) {
    if (e.target === this) closeAdModal();
});
</script>
@endsection