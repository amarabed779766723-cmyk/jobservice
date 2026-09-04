@extends('layouts.admin')
@section('title', 'توثيق المستخدمين')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <h2>🪪 توثيق المستخدمين</h2>

        <div class="card" style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>النوع</th>
                        <th>نوع المستند</th>
                        <th>الصورة</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($verifications as $verification)
                    <tr>
                        <td>{{ $verification->id }}</td>
                        <td>
                            <strong>{{ $verification->user->name ?? 'مستخدم' }}</strong><br>
                            <small>{{ $verification->user->email ?? '' }}</small>
                        </td>
                        <td>
                            @if($verification->user && $verification->user->user_type == 'provider')
                                ⚡ مقدم خدمة
                            @else
                                🔍 باحث
                            @endif
                        </td>
                        <td>
                            @if($verification->verification_type == 'national_id')
                                🪪 بطاقة شخصية
                            @elseif($verification->verification_type == 'university_certificate')
                                🎓 شهادة جامعية
                            @else
                                📜 شهادة مهنية
                            @endif
                        </td>
                        <td>
                            <a href="{{ asset('uploads/verifications/' . $verification->document_image) }}" target="_blank">
                                <img src="{{ asset('uploads/verifications/' . $verification->document_image) }}" style="width:60px; height:60px; object-fit:cover; border-radius:8px; cursor:pointer;">
                            </a>
                        </td>
                        <td>
                            @if($verification->status == 'pending')
                                <span style="background:#fef3c7; color:#92400e; padding:0.25rem 0.5rem; border-radius:2rem; font-size:0.75rem;">⏳ معلق</span>
                            @elseif($verification->status == 'approved')
                                <span style="background:#d1fae5; color:#065f46; padding:0.25rem 0.5rem; border-radius:2rem; font-size:0.75rem;">✅ موثق</span>
                            @else
                                <span style="background:#fee2e2; color:#991b1b; padding:0.25rem 0.5rem; border-radius:2rem; font-size:0.75rem;">❌ مرفوض</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($verification->created_at)->format('Y/m/d H:i') }}</td>
                        <td>
                            @if($verification->status == 'pending')
                                <div style="display:flex; gap:0.5rem;">
                                    <form method="POST" action="{{ route('admin.verifications.approve', $verification->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" style="background:#10b981; color:white; border:none; padding:0.3rem 0.75rem; border-radius:0.5rem; cursor:pointer;">
                                            ✅ قبول
                                        </button>
                                    </form>
                                    <button onclick="showRejectForm({{ $verification->id }})" class="btn btn-danger btn-sm" style="background:#ef4444; color:white; border:none; padding:0.3rem 0.75rem; border-radius:0.5rem; cursor:pointer;">
                                        ❌ رفض
                                    </button>
                                </div>
                                <form id="rejectForm-{{ $verification->id }}" method="POST" action="{{ route('admin.verifications.reject', $verification->id) }}" style="display:none; margin-top:0.5rem;">
                                    @csrf
                                    <input type="text" name="note" placeholder="سبب الرفض" style="width:100%; padding:0.3rem; border:1px solid var(--border); border-radius:0.5rem; margin-bottom:0.3rem;">
                                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;">تأكيد الرفض</button>
                                </form>
                            @else
                                <small style="color:var(--text-secondary);">
                                    @if($verification->admin_note)
                                        {{ $verification->admin_note }}
                                    @else
                                        تمت المعالجة
                                    @endif
                                </small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
function showRejectForm(id) {
    var form = document.getElementById('rejectForm-' + id);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>
@endsection