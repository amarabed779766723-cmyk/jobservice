<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم')</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
    <header class="admin-topbar">
        <div class="topbar-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-left:8px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            لوحة التحكم
        </div>
        <div class="topbar-actions">
            <span style="font-size:0.8rem;opacity:0.7;">{{ date('Y/m/d') }}</span>
            
            <a href="{{ route('admin.revenue') }}" class="topbar-btn" style="text-decoration:none;display:flex;align-items:center;gap:6px;color:var(--text);" title="الإيرادات">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <span style="font-weight:700;color:#10B981;" id="topbarRevenue">
                    {{ number_format(
                        \App\Models\UserPackage::where('price_paid', '>', 0)->sum('price_paid')
                        + \App\Models\Story::where('is_ad', 1)->where('status', 'approved')->sum('ad_price')
                        + \App\Models\Offer::where('status', 'accepted')->sum('price')
                    , 0) }} ر.س
                </span>
            </a>
            
            <button class="topbar-btn" onclick="toggleAdminNotifs()" style="position:relative;background:var(--bg);border:none;color:var(--text);width:36px;height:36px;border-radius:8px;cursor:pointer;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span class="topbar-badge" id="adminNotifCount" style="display:none;">0</span>
                <div id="adminNotifDropdown" class="notif-dropdown"></div>
            </button>
            
            <img src="{{ asset('uploads/avatars/' . (Auth::guard('admin')->user()->avatar ?? 'default-avatar.png')) }}" style="width:34px;height:34px;border-radius:50%;border:2px solid var(--primary);">
            <span style="font-size:0.9rem;">{{ Auth::guard('admin')->user()->name ?? 'أدمن' }}</span>
        </div>
    </header>
    @yield('content')
    <footer class="admin-footer">© 2026 Job Service. جميع الحقوق محفوظة. | v1.0</footer>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
    function loadAdminNotifs(){fetch('{{ route("admin.notifications") }}').then(r=>r.json()).then(data=>{let html='';if(data.notifications.length===0){html='<div class="notif-item">لا توجد إشعارات</div>';}else{data.notifications.forEach(n=>{html+=`<div class="notif-item ${n.is_read?'':'unread'}" onclick="markNotifRead(${n.id})">${n.message}<br><small>${new Date(n.created_at).toLocaleString('ar')}</small></div>`;});}document.getElementById('adminNotifDropdown').innerHTML=html;let badge=document.getElementById('adminNotifCount');if(data.unread>0){badge.style.display='flex';badge.textContent=data.unread>9?'9+':data.unread;}else{badge.style.display='none';}});}
    function toggleAdminNotifs(){let d=document.getElementById('adminNotifDropdown');d.style.display=d.style.display==='none'||d.style.display===''?'block':'none';if(d.style.display==='block')loadAdminNotifs();}
    function markNotifRead(id){fetch(`/admin/notifications/${id}/read`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')}}).then(()=>loadAdminNotifs());}
    document.addEventListener('click',function(e){if(!e.target.closest('.topbar-btn'))document.getElementById('adminNotifDropdown').style.display='none';});
    setInterval(loadAdminNotifs,30000);loadAdminNotifs();

    function updateTopbarRevenue() {
        fetch('{{ route("admin.dashboard") }}').then(r => r.text()).then(html => {
            let doc = new DOMParser().parseFromString(html, 'text/html');
            let totalEl = doc.getElementById('totalRevenue');
            if (totalEl) document.getElementById('topbarRevenue').textContent = totalEl.textContent;
        });
    }
    setInterval(updateTopbarRevenue, 10000);
    </script>
</body>
</html>