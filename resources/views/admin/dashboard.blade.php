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

        {{-- بطاقات الإيرادات --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: linear-gradient(135deg, #dbeafe, #eff6ff); border-radius: 16px; padding: 1.5rem; border: 2px solid #93c5fd; text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">💰</div>
                <div style="font-size: 1.8rem; font-weight: 900; color: #1e40af;" id="totalRevenue">{{ number_format($totalRevenue ?? 0, 0) }} ر.س</div>
                <div style="color: #64748b; font-size: 0.85rem;">إجمالي الإيرادات</div>
            </div>
            <div style="background: linear-gradient(135deg, #d1fae5, #ecfdf5); border-radius: 16px; padding: 1.5rem; border: 2px solid #6ee7b7; text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📅</div>
                <div style="font-size: 1.8rem; font-weight: 900; color: #065f46;" id="todayRevenue">{{ number_format($todayRevenue ?? 0, 0) }} ر.س</div>
                <div style="color: #64748b; font-size: 0.85rem;">إيرادات اليوم</div>
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

        {{-- أحدث المستخدمين والنشاطات --}}
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-top:1.5rem;">
            <div class="card" style="background:white; border-radius:16px; padding:1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom:1rem;">🆕 أحدث المستخدمين</h3>
                <table class="admin-table">
                    <thead><tr><th>الاسم</th><th>النوع</th><th>التاريخ</th></tr></thead>
                    <tbody>
                        @foreach($latestUsers as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->user_type === 'provider' ? '⚡ مقدم' : '🔍 باحث' }}</td>
                            <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y/m/d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card" style="background:white; border-radius:16px; padding:1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom:1rem;">📋 آخر النشاطات</h3>
                <div style="max-height:300px; overflow-y:auto;">
                    @foreach($latestActivities as $activity)
                    <div style="padding:0.5rem 0; border-bottom:1px solid var(--border); font-size:0.85rem;">
                        <strong>{{ $activity->user->name ?? 'زائر' }}</strong>
                        <span style="color:var(--text-secondary);"> - {{ $activity->description }}</span>
                        <br><small style="color:var(--text-secondary);">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.activity-logs') }}" style="display:block; text-align:center; margin-top:0.5rem; color:var(--primary);">عرض الكل ←</a>
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

// تحديث الإيرادات تلقائياً
function updateRevenue() {
    fetch('{{ route("admin.dashboard") }}').then(r => r.text()).then(html => {
        let doc = new DOMParser().parseFromString(html, 'text/html');
        let totalEl = doc.getElementById('totalRevenue');
        let todayEl = doc.getElementById('todayRevenue');
        if (totalEl) document.getElementById('totalRevenue').textContent = totalEl.textContent;
        if (todayEl) document.getElementById('todayRevenue').textContent = todayEl.textContent;
        let newCards = doc.querySelectorAll('.stat-card-value');
        let currentCards = document.querySelectorAll('.stat-card-value');
        newCards.forEach((card, i) => { if (currentCards[i]) currentCards[i].textContent = card.textContent; });
    });
}
setInterval(updateRevenue, 5000);

// إشعارات الأدمن
function loadAdminNotifs(){fetch('{{ route("admin.notifications") }}').then(r=>r.json()).then(data=>{let html='';if(data.notifications.length===0){html='<div class="notif-item">لا توجد إشعارات</div>';}else{data.notifications.forEach(n=>{html+=`<div class="notif-item ${n.is_read?'':'unread'}" onclick="markNotifRead(${n.id})">${n.message}<br><small>${new Date(n.created_at).toLocaleString('ar')}</small></div>`;});}document.getElementById('adminNotifDropdown').innerHTML=html;let badge=document.getElementById('adminNotifCount');if(data.unread>0){badge.style.display='inline';badge.textContent=data.unread>9?'9+':data.unread;}else{badge.style.display='none';}});}
function toggleAdminNotifs(){let d=document.getElementById('adminNotifDropdown');d.style.display=d.style.display==='none'||d.style.display===''?'block':'none';if(d.style.display==='block')loadAdminNotifs();}
function markNotifRead(id){fetch(`/admin/notifications/${id}/read`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')}}).then(()=>loadAdminNotifs());}
document.addEventListener('click',function(e){if(!e.target.closest('button[onclick="toggleAdminNotifs()"]')&&!e.target.closest('#adminNotifDropdown'))document.getElementById('adminNotifDropdown').style.display='none';});
setInterval(loadAdminNotifs,30000);loadAdminNotifs();
</script>
@endsection