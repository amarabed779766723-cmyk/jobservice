@extends('layouts.admin')

@section('title', 'سجل النشاطات - لوحة التحكم')

@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <div class="admin-header">
            <h2>📋 سجل النشاطات</h2>
            <form action="{{ route('admin.activity-logs.clear') }}" method="POST" onsubmit="return confirm('مسح جميع السجلات؟')" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">🗑️ مسح الكل</button>
            </form>
        </div>

        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>الإجراء</th>
                        <th>الوصف</th>
                        <th>IP</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->user->name ?? 'زائر' }}</td>
                        <td><span class="badge">{{ $log->action }}</span></td>
                        <td>{{ $log->description ?? '—' }}</td>
                        <td style="direction:ltr; text-align:left;">{{ $log->ip_address }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y/m/d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $logs->links() }}
        </div>
    </main>
</div>
@endsection