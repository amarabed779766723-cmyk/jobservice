@extends('layouts.admin')
@section('title', 'لوحة التحكم')
@section('content')
<div class="admin-body">
    @include('admin.sidebar')
    <main class="admin-main">

        <div class="admin-header" style="background: linear-gradient(135deg, #1e293b, #1e40af); color: white; border: none; border-radius: 16px;">
            <h1 style="color: white; margin: 0;">👋 أهلاً، {{ Auth::guard('admin')->user()->name ?? 'أدمن' }}</h1>
            <p style="color: rgba(255,255,255,0.7); margin: 0.25rem 0 0;">آخر تحديث: {{ date('Y/m/d h:i A') }}</p>
        </div>

        {{-- ✅ بطاقات المعاملات --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: linear-gradient(135deg, #fef3c7, #fffbeb); border-radius: 16px; padding: 1.5rem; border: 2px solid #fde047; text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">⏳</div>
                <div style="font-size: 1.8rem; font-weight: 900; color: #92400e;">
                    {{ \App\Models\Transaction::where('status', 'pending')->count() }}
                </div>
                <div style="color: #64748b; font-size: 0.85rem;">معاملات معلقة</div>
            </div>
            <div style="background: linear-gradient(135deg, #d1fae5, #ecfdf5); border-radius: 16px; padding: 1.5rem; border: 2px solid #6ee7b7; text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">✅</div>
                <div style="font-size: 1.8rem; font-weight: 900; color: #065f46;">
                    {{ \App\Models\Transaction::where('status', 'confirmed')->count() }}
                </div>
                <div style="color: #64748b; font-size: 0.85rem;">معاملات مؤكدة</div>
            </div>
            <div style="background: linear-gradient(135deg, #dbeafe, #eff6ff); border-radius: 16px; padding: 1.5rem; border: 2px solid #93c5fd; text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">💰</div>
                <div style="font-size: 1.5rem; font-weight: 900; color: #1e40af;">
                    {{ number_format(\App\Models\Transaction::where('status', 'confirmed')->sum('amount'), 0) }} ر.س
                </div>
                <div style="color: #64748b; font-size: 0.85rem;">إجمالي المؤكد</div>
            </div>
        </div>

        {{-- الصف الأول من الإحصائيات --}}
        <div class="stat-cards">
            <div class="stat-card" style="border-left: 4px solid #2563eb;">
                <div class="stat-card-value">{{ $usersCount }}</div>
                <div class="stat-card-label">👥 مستخدم</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #10b981;">
                <div class="stat-card-value">{{ $servicesCount }}</div>
                <div class="stat-card-label">🛠️ خدمة</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #ef4444;">
                <div class="stat-card-value">{{ $reportsCount }}</div>
                <div class="stat-card-label">🚩 بلاغ جديد</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #f59e0b;">
                <div class="stat-card-value">{{ $postsCount }}</div>
                <div class="stat-card-label">📢 منشور</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
                <div class="stat-card-value">{{ $chatsCount }}</div>
                <div class="stat-card-label">💬 محادثة</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #ec4899;">
                <div class="stat-card-value">{{ $bannedCount }}</div>
                <div class="stat-card-label">🚫 محظور</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #3b82f6;">
                <div class="stat-card-value">{{ $providersCount }}</div>
                <div class="stat-card-label">⚡ مقدم خدمة</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #14b8a6;">
                <div class="stat-card-value">{{ $clientsCount }}</div>
                <div class="stat-card-label">🔍 باحث</div>
            </div>
        </div>

        {{-- الصف الثاني من الإحصائيات --}}
        <div class="stat-cards" style="margin-top:1rem;">
            <div class="stat-card" style="border-left: 4px solid #6366f1; background:#eff6ff;">
                <div class="stat-card-value">{{ $todayUsers }}</div>
                <div class="stat-card-label">🆕 مستخدمين اليوم</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #f59e0b; background:#fefce8;">
                <div class="stat-card-value">{{ $totalRequests }}</div>
                <div class="stat-card-label">📋 طلبات الخدمة</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #10b981; background:#ecfdf5;">
                <div class="stat-card-value">{{ $totalOffers }}</div>
                <div class="stat-card-label">💵 العروض</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #f43f5e; background:#fff1f2;">
                <div class="stat-card-value">{{ $totalRatings }}</div>
                <div class="stat-card-label">⭐ التقييمات</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #8b5cf6; background:#f5f3ff;">
                <div class="stat-card-value">{{ $totalStories }}</div>
                <div class="stat-card-label">📖 الإعلانات</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #0ea5e9; background:#f0f9ff;">
                <div class="stat-card-value">{{ $totalActivityLogs }}</div>
                <div class="stat-card-label">📋 نشاط مسجل</div>
            </div>
        </div>

        {{-- ✅ أحدث المعاملات --}}
        <div class="card" style="margin-top:1.5rem; overflow-x:auto;">
            <h3 style="margin-bottom:1rem;">💳 أحدث المعاملات</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>الباقة</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @php $latestTransactions = \App\Models\Transaction::with(['user', 'package'])->latest()->take(5)->get(); @endphp
                    @foreach($latestTransactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->user->name ?? 'مستخدم' }}</td>
                        <td>{{ $transaction->package->name ?? '-' }}</td>
                        <td><strong>{{ number_format($transaction->amount, 2) }} ر.س</strong></td>
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
                            <a href="{{ route('admin.transactions') }}" class="btn btn-outline btn-sm">عرض الكل</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- الرسوم البيانية --}}
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-top:1.5rem;">
            <div class="card" style="background:white; border-radius:16px; padding:1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom:1rem;">📊 المستخدمين الجدد (آخر 7 أيام)</h3>
                <canvas id="usersChart" height="200"></canvas>
            </div>
            <div class="card" style="background:white; border-radius:16px; padding:1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom:1rem;">📊 المنشورات (آخر 7 أيام)</h3>
                <canvas id="postsChart" height="200"></canvas>
            </div>
        </div>

    </main>
</div>

<script src="{{ asset('assets/js/chart.min.js') }}"></script>
<script>
const days = [];
const usersData = [];
const postsData = [];

@for($i = 6; $i >= 0; $i--)
    @php
        $date = now()->subDays($i)->format('Y-m-d');
        $dayUsers = \App\Models\User::whereDate('created_at', $date)->count();
        $dayPosts = \App\Models\Post::whereDate('created_at', $date)->count();
    @endphp
    days.push('{{ now()->subDays($i)->format("m/d") }}');
    usersData.push({{ $dayUsers }});
    postsData.push({{ $dayPosts }});
@endfor

new Chart(document.getElementById('usersChart'), {
    type: 'line', data: { labels: days, datasets: [{ label: 'مستخدمين جدد', data: usersData, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.1)', fill: true, tension: 0.3 }] },
    options: { responsive: true, plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('postsChart'), {
    type: 'bar', data: { labels: days, datasets: [{ label: 'منشورات', data: postsData, backgroundColor: '#10b981' }] },
    options: { responsive: true, plugins: { legend: { display: false } } }
});

// إشعارات الأدمن
function loadAdminNotifs(){fetch('{{ route("admin.notifications") }}').then(r=>r.json()).then(data=>{let html='';if(data.notifications.length===0){html='<div class="notif-item">لا توجد إشعارات</div>';}else{data.notifications.forEach(n=>{html+=`<div class="notif-item ${n.is_read?'':'unread'}" onclick="markNotifRead(${n.id})">${n.message}<br><small>${new Date(n.created_at).toLocaleString('ar')}</small></div>`;});}document.getElementById('adminNotifDropdown').innerHTML=html;let badge=document.getElementById('adminNotifCount');if(data.unread>0){badge.style.display='inline';badge.textContent=data.unread>9?'9+':data.unread;}else{badge.style.display='none';}});}
function toggleAdminNotifs(){let d=document.getElementById('adminNotifDropdown');d.style.display=d.style.display==='none'||d.style.display===''?'block':'none';if(d.style.display==='block')loadAdminNotifs();}
function markNotifRead(id){fetch(`/admin/notifications/${id}/read`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')}}).then(()=>loadAdminNotifs());}
document.addEventListener('click',function(e){if(!e.target.closest('button[onclick="toggleAdminNotifs()"]')&&!e.target.closest('#adminNotifDropdown'))document.getElementById('adminNotifDropdown').style.display='none';});
setInterval(loadAdminNotifs,30000);loadAdminNotifs();
</script>
@endsection