@extends('layouts.admin')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <h2>👥 جميع المستخدمين</h2>
        <table class="admin-table">
            <thead><tr><th>ID</th><th>الاسم</th><th>البريد</th><th>النوع</th><th>الحالة</th><th>إجراء</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>{{ $u->id }}</td><td>{{ $u->name }}</td><td>{{ $u->email }}</td>
                    <td>{{ $u->user_type === 'provider' ? 'مقدم' : 'باحث' }}</td>
                    <td>{{ ($u->status ?? 'active') === 'banned' ? '🚫 محظور' : '✅ نشط' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.users') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $u->id }}">
                            @if(($u->status ?? 'active') === 'banned')
                                <button name="action" value="unban" class="btn btn-success btn-sm">فك الحظر</button>
                            @else
                                <button name="action" value="ban" class="btn btn-danger btn-sm">حظر</button>
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