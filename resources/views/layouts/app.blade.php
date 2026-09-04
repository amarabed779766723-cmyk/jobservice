@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Job Service')</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="theme-color" content="#2563EB">
</head>
<body>

    {{-- ========== هيدر ========== --}}
    <header class="topbar-2026">
        <div class="topbar-left" style="display:flex;align-items:center;gap:0.75rem;">
            @auth
            <div style="position:relative;">
                <img src="{{ asset('uploads/avatars/' . (auth()->user()->avatar ?? 'default-avatar.png')) }}" 
                     style="width:40px;height:40px;border-radius:50%;object-fit:cover;cursor:pointer;" 
                     onclick="toggleProfileMenu()">
                <div class="user-status-dot"></div>
            </div>
            <div onclick="toggleProfileMenu()" style="cursor:pointer;">
                <div style="font-weight:600;font-size:0.9rem;">{{ auth()->user()->name }}</div>
                <div style="font-size:0.7rem;color:var(--text-secondary);">{{ auth()->user()->user_type === 'provider' ? __('Provider') : __('Client') }}</div>
            </div>
            @endauth
        </div>

        {{-- بحث --}}
        <div class="search-advanced">
            <span class="search-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </span>
           <input type="text" id="globalSearch" placeholder="{{ __('Search...') }}" autocomplete="off" onkeyup="globalSearch(this.value)">
           <div id="searchResults" class="search-results-advanced"></div>
        </div>

        <div class="topbar-right" style="display:flex;align-items:center;gap:0.5rem;">
  
               <x-logo type="full" class="logo-header" />
            </a>

            {{-- ===== إشعارات ===== --}}
            @auth
            <div style="position:relative;">
                <button onclick="toggleNotifications()" style="background:none;border:none;cursor:pointer;position:relative;padding:0.5rem;color:var(--text);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    @php $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', 0)->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="nav-badge" style="position:absolute;top:-2px;right:-4px;width:18px;height:18px;background:#ef4444;color:white;border-radius:50%;font-size:0.6rem;display:flex;align-items:center;justify-content:center;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>
                <div id="notifDropdown" class="notif-dropdown-2026">
                    <div style="padding:0.75rem 1rem;font-weight:700;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span>{{ __('Notifications') }}</span>
                        <a href="{{ route('notifications') }}" style="font-size:0.8rem;color:var(--primary);text-decoration:none;">{{ __('View All') }}</a>
                    </div>
                    <div id="notifList"></div>
                </div>
            </div>
            @endauth

            {{-- ===== زر تحميل التطبيق ===== --}}
            <button id="installPwaBtn2026" onclick="installPwa()" style="background:linear-gradient(135deg, #2563EB, #1D4ED8);color:white;border:none;padding:0.4rem 0.8rem;border-radius:2rem;cursor:pointer;font-family:'Tajawal',sans-serif;font-size:0.7rem;font-weight:600;display:flex;align-items:center;gap:0.3rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                {{ __('Download App') }}
            </button>

            @auth
            {{-- ===== قائمة الملف الشخصي ===== --}}
            <div class="profile-menu-wrapper" style="position:relative;text-align:center;">
                <img src="{{ asset('uploads/avatars/' . (auth()->user()->avatar ?? 'default-avatar.png')) }}" 
                     style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--primary);cursor:pointer;" 
                     onclick="toggleProfileMenu()">
                <div id="profileDropdown" class="profile-dropdown" style="display:none;">
                    <div class="dropdown-header">
                        <img src="{{ asset('uploads/avatars/' . (auth()->user()->avatar ?? 'default-avatar.png')) }}" style="width:40px;height:40px;border-radius:50%;">
                        <div><strong>{{ auth()->user()->name }}</strong><small style="color:var(--text-secondary);display:block;">{{ auth()->user()->user_type === 'provider' ? __('Provider') : __('Client') }}</small></div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('profile') }}" class="dropdown-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{ __('My Profile') }}
                    </a>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        {{ __('Edit Profile') }}
                    </a>
                    <a href="{{ route('stories.index') }}" class="dropdown-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        {{ __('Ads') }}
                    </a>
                    <a href="{{ route('bookings.index') }}" class="dropdown-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ __('My Bookings') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    @if(auth()->user()->user_type === 'provider')
                    <a href="{{ route('service.create') }}" class="dropdown-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        {{ __('Create Service') }}
                    </a>
                    @else
                    <a href="{{ route('request.create') }}" class="dropdown-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                        {{ __('Create Request') }}
                    </a>
                    @endif
                    <a href="{{ route('settings') }}" class="dropdown-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        {{ __('Settings') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('switch.locale', $locale === 'ar' ? 'en' : 'ar') }}" class="dropdown-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        {{ $locale === 'ar' ? 'English' : 'العربية' }}
                    </a>
                    <a href="#" onclick="toggleDarkModeAdvanced()" class="dropdown-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                        {{ __('Dark Mode') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item" style="width:100%;border:none;background:none;cursor:pointer;font-family:'Tajawal',sans-serif;font-size:0.95rem;color:#EF4444;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        {{ __('Logout') }}
                    </button></form>
                </div>
            </div>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">{{ __('Register') }}</a>
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm">{{ __('Login') }}</a>
            @endauth
        </div>
    </header>

    @yield('content')

    {{-- ===== زر الدردشة ===== --}}
    @auth
    <a href="{{ route('chats.index') }}" class="chat-fab" title="{{ __('Messages') }}">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        @php $unreadChats = \App\Models\Chat::whereHas('participants', function($q) { $q->where('user_id', Auth::id()); })->whereHas('messages', function($q) { $q->where('sender_id', '!=', Auth::id())->where('is_read', 0); })->count(); @endphp
        @if($unreadChats > 0)<span class="chat-fab-badge">{{ $unreadChats > 9 ? '9+' : $unreadChats }}</span>@endif
    </a>
    @endauth

    {{-- ===== القائمة السفلية ===== --}}
    <nav class="bottom-nav-2026">
        <a href="{{ route('home') }}" class="nav-item-2026 {{ request()->routeIs('home') ? 'active' : '' }}">
            <span class="nav-icon-2026">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </span>
            <span>{{ __('Home') }}</span>
        </a>
        <a href="{{ route('requests.index') }}" class="nav-item-2026 {{ request()->routeIs('requests.*') ? 'active' : '' }}">
            <span class="nav-icon-2026">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
            </span>
            <span>{{ __('Requests') }}</span>
        </a>
        <a href="{{ route('services.index') }}" class="nav-item-2026 {{ request()->routeIs('services.*') ? 'active' : '' }}">
            <span class="nav-icon-2026">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
            </span>
            <span>{{ __('Services') }}</span>
        </a>
        <a href="#" onclick="toggleCreateMenu2026()" class="nav-item-2026">
            <span class="nav-icon-2026" style="background:linear-gradient(135deg, #2563EB, #1D4ED8);color:white;border-radius:50%;width:48px;height:48px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 15px rgba(37,99,235,0.3);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
            </span>
            <span>{{ __('Create') }}</span>
        </a>
        <a href="{{ route('contact') }}" class="nav-item-2026 {{ request()->routeIs('contact') ? 'active' : '' }}">
            <span class="nav-icon-2026">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.574 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
            </span>
            <span>{{ __('Contact Us') }}</span>
        </a>
    </nav>

    {{-- ===== قائمة إنشاء المحتوى ===== --}}
    <div id="createMenu2026" class="create-menu-2026">
        @auth
            @if(auth()->user()->user_type === 'provider')
                <a href="{{ route('service.create') }}" class="create-item">
                    <span class="create-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    </span>
                    <span>{{ __('New Service') }}</span>
                </a>
            @endif
            <a href="{{ route('request.create') }}" class="create-item">
                <span class="create-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                </span>
                <span>{{ __('New Request') }}</span>
            </a>
            <a href="{{ route('post.create') }}" class="create-item">
                <span class="create-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </span>
                <span>{{ __('Posts') }}</span>
            </a>
            <a href="{{ route('stories.index') }}" class="create-item">
                <span class="create-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </span>
                <span>{{ __('Stories & Offers') }}</span>
            </a>
        @endauth
    </div>

    <script src="{{ asset('assets/js/main.js') }}"></script>
    @include('report-modal')
    @include('story-viewer')
    
    @yield('scripts')

    <script>
    function toggleProfileMenu(){var m=document.getElementById('profileDropdown');m.style.display=(m.style.display==='none'||m.style.display==='')?'block':'none';}
    function toggleCreateMenu2026(){var m=document.getElementById('createMenu2026');if(m){m.style.display=(m.style.display==='none'||m.style.display==='')?'block':'none';}}
    function toggleDarkModeAdvanced(){var d=document.documentElement.getAttribute('data-theme')==='dark';applyTheme(d?'light':'dark');document.getElementById('profileDropdown').style.display='none';}
    function toggleNotifications(){var m=document.getElementById('notifDropdown');if(m){m.style.display=(m.style.display==='none'||m.style.display==='')?'block':'none';}}
    
    document.addEventListener('DOMContentLoaded', function() {
        var notifBtn = document.querySelector('[onclick="toggleNotifications()"]');
        if (notifBtn) {
            notifBtn.addEventListener('click', function() {
                fetch('/api/notifications')
                    .then(res => res.json())
                    .then(data => {
                        var container = document.getElementById('notifList');
                        if (container && data.success) {
                            var html = '';
                            if (data.data.length === 0) {
                                html = '<div style="padding:1rem;text-align:center;color:var(--text-secondary);">{{ __("No Notifications") }}</div>';
                            } else {
                                data.data.forEach(n => {
                                    html += `
                                        <div class="notif-item-2026 ${n.is_read ? '' : 'unread'}" style="padding:0.75rem 1rem;border-bottom:1px solid var(--border);display:flex;gap:0.75rem;align-items:flex-start;">
                                            <span style="font-size:1.2rem;">${n.icon || '🔔'}</span>
                                            <div class="notif-content">
                                                <div class="notif-text">${n.message}</div>
                                                <div class="notif-time">${n.time_ago || '{{ __("now") }}'}</div>
                                            </div>
                                        </div>
                                    `;
                                });
                            }
                            container.innerHTML = html;
                        }
                    });
            });
        }
    });

    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => { e.preventDefault(); deferredPrompt = e; });
    function installPwa() {
        if (deferredPrompt) { deferredPrompt.prompt(); deferredPrompt.userChoice.then((result) => { deferredPrompt = null; }); }
        else { window.open('{{ route("home") }}', 'JobService', 'width=414,height=896,menubar=no,toolbar=no,location=no,status=no'); }
    }
    </script>

    {{-- ✅ تحديد الموقع الحقيقي تلقائياً --}}
    <script>
    function getUserRealLocation() {
        if (!navigator.geolocation) {
            console.log('⚠️ متصفحك لا يدعم تحديد الموقع');
            return;
        }
        
        var options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };
        
        navigator.geolocation.getCurrentPosition(function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            var accuracy = pos.coords.accuracy;
            
            console.log('✅ الموقع الحقيقي:', lat, lng);
            console.log('📏 الدقة:', accuracy + ' متر');
            
            // حفظ الموقع
            fetch('{{ route("save.location") }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                },
                body: JSON.stringify({ 
                    latitude: lat, 
                    longitude: lng 
                })
            })
            .then(function(res) { return res.json(); })
            .then(function() {
                // جلب اسم المدينة والعنوان
                return fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&accept-language=ar');
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.display_name) {
                    var city = '';
                    if (data.address) {
                        city = data.address.city || data.address.town || data.address.state || data.address.county || '';
                    }
                    
                    fetch('{{ route("save.location") }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                        },
                        body: JSON.stringify({ 
                            latitude: lat, 
                            longitude: lng,
                            address: data.display_name,
                            city: city
                        })
                    })
                    .then(function() {
                        console.log('✅ تم حفظ الموقع الكامل: ' + city);
                    });
                }
            })
            .catch(function(error) {
                console.log('❌ خطأ:', error);
            });
            
        }, function(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    console.log('❌ تم رفض إذن الموقع');
                    break;
                case error.POSITION_UNAVAILABLE:
                    console.log('❌ معلومات الموقع غير متاحة');
                    break;
                case error.TIMEOUT:
                    console.log('❌ انتهت مهلة تحديد الموقع');
                    break;
                default:
                    console.log('❌ خطأ غير معروف');
                    break;
            }
        }, options);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            getUserRealLocation();
        }, 1000);
    });
    </script>

</body>
</html>