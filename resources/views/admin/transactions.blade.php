@extends('layouts.admin')
@section('title', 'المعاملات المالية')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <h2>💳 المعاملات المالية</h2>

        {{-- إحصائيات --}}
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem; margin-bottom:2rem;">
            <div class="card" style="background:#fefce8; border:2px solid #fde047; text-align:center;">
                <h3 style="color:#854d0e;">⏳ معلقة</h3>
                <strong style="font-size:2rem; color:#a16207;">{{ $pendingCount }}</strong>
            </div>
            <div class="card" style="background:#f0fdf4; border:2px solid #86efac; text-align:center;">
                <h3 style="color:#166534;">✅ مؤكدة</h3>
                <strong style="font-size:2rem; color:#15803d;">{{ $confirmedCount }}</strong>
            </div>
            <div class="card" style="background:#fef2f2; border:2px solid #fca5a5; text-align:center;">
                <h3 style="color:#991b1b;">❌ مرفوضة</h3>
                <strong style="font-size:2rem; color:#dc2626;">{{ $rejectedCount }}</strong>
            </div>
            <div class="card" style="background:#eff6ff; border:2px solid #93c5fd; text-align:center;">
                <h3 style="color:#1e40af;">💰 المؤكد</h3>
                <strong style="font-size:1.5rem; color:#1d4ed8;">{{ number_format($totalConfirmed, 2) }} ر.س</strong>
            </div>
        </div>

        {{-- جدول المعاملات --}}
        <div class="card" style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>الباقة</th>
                        <th>المبلغ</th>
                        <th>اسم الدافع</th>
                        <th>رقم الجوال</th>
                        <th>صورة التحويل</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>
                            <strong>{{ $transaction->user->name ?? 'مستخدم' }}</strong><br>
                            <small>{{ $transaction->user->email ?? '' }}</small>
                        </td>
                        <td>{{ $transaction->package->name ?? '-' }}</td>
                        <td><strong>{{ number_format($transaction->amount, 2) }} ر.س</strong></td>
                        <td>{{ $transaction->sender_name ?? '-' }}</td>
                        <td>{{ $transaction->sender_phone ?? '-' }}</td>
                        <td>
                            @if($transaction->receipt_image)
                                <a href="{{ asset('uploads/receipts/' . $transaction->receipt_image) }}" target="_blank">
                                    <img src="{{ asset('uploads/receipts/' . $transaction->receipt_image) }}" style="width:50px; height:50px; object-fit:cover; border-radius:8px; cursor:pointer;">
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($transaction->status === 'pending')
                                <span style="background:#fef3c7; color:#92400e; padding:0.25rem 0.5rem; border-radius:2rem; font-size:0.75rem;">⏳ معلقة</span>
                            @elseif($transaction->status === 'confirmed')
                                <span style="background:#d1fae5; color:#065f46; padding:0.25rem 0.5rem; border-radius:2rem; font-size:0.75rem;">✅ مؤكدة</span>
                            @else
                                <span style="background:#fee2e2; color:#991b1b; padding:0.25rem 0.5rem; border-radius:2rem; font-size:0.75rem;">❌ مرفوضة</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y/m/d H:i') }}</td>
                        <td>
                            @if($transaction->status === 'pending')
                                <div style="display:flex; gap:0.5rem;">
                                    <form method="POST" action="{{ route('admin.transactions.confirm', $transaction->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" style="background:#10b981; color:white; border:none; padding:0.3rem 0.75rem; border-radius:0.5rem; cursor:pointer;">
                                            ✅ تأكيد
                                        </button>
                                    </form>
                                    <button onclick="showRejectForm({{ $transaction->id }})" class="btn btn-danger btn-sm" style="background:#ef4444; color:white; border:none; padding:0.3rem 0.75rem; border-radius:0.5rem; cursor:pointer;">
                                        ❌ رفض
                                    </button>
                                </div>
                                <form id="rejectForm-{{ $transaction->id }}" method="POST" action="{{ route('admin.transactions.reject', $transaction->id) }}" style="display:none; margin-top:0.5rem;">
                                    @csrf
                                    <input type="text" name="note" placeholder="سبب الرفض" style="width:100%; padding:0.3rem; border:1px solid var(--border); border-radius:0.5rem; margin-bottom:0.3rem;">
                                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;">تأكيد الرفض</button>
                                </form>
                            @else
                                <small style="color:var(--text-secondary);">
                                    @if($transaction->admin_note)
                                        {{ $transaction->admin_note }}
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