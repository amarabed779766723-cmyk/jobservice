@extends('layouts.admin')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">
        <h2>🛠️ إدارة الخدمات ({{ count($services) }})</h2>
        @if(count($services) === 0)
            <p>لا توجد خدمات.</p>
        @else
            <table class="admin-table">
                <thead><tr><th>ID</th><th>العنوان</th><th>مقدم</th><th>السعر</th><th>التاريخ</th><th>حذف</th></tr></thead>
                <tbody>
                    @foreach($services as $srv)
                    <tr>
                        <td>{{ $srv->id }}</td><td>{{ $srv->title }}</td><td>{{ $srv->provider->name ?? '' }}</td>
                        <td>{{ number_format($srv->price, 2) }} ر.س</td><td>{{ \Carbon\Carbon::parse($srv->created_at)->format('Y/m/d') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.services') }}" onsubmit="return confirm('متأكد؟')">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $srv->id }}">
                                <button name="delete_service" class="btn btn-danger btn-sm">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </main>
</div>
@endsection