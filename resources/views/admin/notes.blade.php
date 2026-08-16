@extends('layouts.admin')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <h2>📩 ملاحظات الحسابات المحظورة</h2>
        <table class="admin-table">
            <thead>
                <tr><th>ID</th><th>المستخدم</th><th>الرسالة</th><th>التاريخ</th><th>الحالة</th><th>إجراء</th></tr>
            </thead>
            <tbody>
                @foreach($notes as $note)
                <tr>
                    <td>{{ $note->id }}</td>
                    <td>{{ $note->user->name ?? '—' }}</td>
                    <td>{{ $note->message }}</td>
                    <td>{{ \Carbon\Carbon::parse($note->created_at)->format('Y/m/d H:i') }}</td>
                    <td>{{ $note->is_read ? '✅ مقروءة' : '📩 جديدة' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.notes.read', $note->id) }}">
                            @csrf
                            <button class="btn btn-outline btn-sm">تمت القراءة</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</div>
@endsection