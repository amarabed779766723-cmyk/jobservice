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
        <div class="topbar-title" style="display:flex; align-items:center;">
            <a href="{{ route('admin.dashboard') }}" style="display:flex; align-items:center; text-decoration:none;">
              
                    
            </a>
        </div>
        <div class="topbar-actions">
            <span style="font-size:0.8rem;opacity:0.7;">{{ date('Y/m/d') }}</span>
            
            {{-- المعاملات المالية --}}
            <a href="{{ route('admin.transactions') }}" class="topbar-btn" style="text-decoration:none;display:flex;align-items:center;gap:6px;color:var(--text);" title="المعاملات المالية">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C9A24B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                <span style="font-weight:700;color:#C9A24B;">💳 المعاملات</span>
            </a>
            
            <button class="topbar-btn" onclick="toggleAdminNotifs()" style="position:relative;background:var(--bg);border:none;color:var(--text);width:36px;height:36px;border-radius:8px;cursor:pointer;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span class="topbar-badge" id="adminNotifCount" style="display:none;">0</span>
                <div id="adminNotifDropdown" class="notif-dropdown"></div>
            </button>
            
            <img src="{{ asset('uploads/avatars/' . (Auth::guard('admin')->user()->avatar ?? 'default-avatar.png')) }}" style="width:34px;height:34px;border-radius:50%;border:2px solid #C9A24B;">
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
    </script>
</body>
</html>