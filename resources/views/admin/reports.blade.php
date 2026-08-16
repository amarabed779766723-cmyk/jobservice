@extends('layouts.admin')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <h2>🚩 إدارة البلاغات</h2>
        <table class="admin-table">
            <thead><tr><th>ID</th><th>المُبلِّغ</th><th>المُبلَّغ عنه</th><th>الخدمة</th><th>السبب</th><th>الحالة</th><th>إجراء</th></tr></thead>
            <tbody>
                @foreach($reports as $rep)
                <tr>
                    <td>{{ $rep->id }}</td>
                    <td>{{ $rep->reporter->name ?? '—' }}</td>
                    <td>{{ $rep->reported->name ?? '—' }}</td>
                    <td>{{ $rep->service->title ?? '—' }}</td>
                    <td>{{ $rep->reason }}</td>
                    <td>{{ $rep->status }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.reports') }}" style="display:flex;gap:4px;">
                            @csrf
                            <input type="hidden" name="report_id" value="{{ $rep->id }}">
                            @if($rep->status === 'pending')
                                <button name="action" value="reviewed" class="btn btn-outline btn-sm">مراجعة</button>
                                @if($rep->service_id)
                                    <input type="hidden" name="service_id" value="{{ $rep->service_id }}">
                                    <button name="action" value="delete_content" class="btn btn-danger btn-sm">حذف المحتوى</button>
                                @endif
                            @endif
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</div>
@endsection