<aside class="admin-sidebar">
    <div class="admin-sidebar-logo" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 1.5rem; border-bottom: 1px solid var(--border);">
        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        </div>
        <span style="font-size: 1.3rem; font-weight: 900; color: var(--primary);">Job Service</span>
    </div>

    <div style="padding: 0.75rem; overflow-y: auto; height: calc(100vh - 180px);">
        {{-- الرئيسية --}}
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}" style="border-right: 3px solid #2563EB; {{ request()->is('admin/dashboard') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #EFF6FF;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </div>
            الرئيسية
        </a>

        {{-- المستخدمون --}}
        <a href="{{ route('admin.users') }}" class="{{ request()->is('admin/users') ? 'active' : '' }}" style="border-right: 3px solid #8B5CF6; {{ request()->is('admin/users') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #F5F3FF;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8B5CF6" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            المستخدمون
        </a>

        {{-- الخدمات --}}
        <a href="{{ route('admin.services') }}" class="{{ request()->is('admin/services') ? 'active' : '' }}" style="border-right: 3px solid #10B981; {{ request()->is('admin/services') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #ECFDF5;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            </div>
            الخدمات
        </a>

        {{-- البلاغات --}}
        <a href="{{ route('admin.reports') }}" class="{{ request()->is('admin/reports') ? 'active' : '' }}" style="border-right: 3px solid #EF4444; {{ request()->is('admin/reports') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #FEF2F2;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
            </div>
            البلاغات
        </a>

        {{-- رقابة المنشورات --}}
        <a href="{{ route('admin.posts') }}" style="border-right: 3px solid #0EA5E9; border-right-color: transparent;">
            <div class="sidebar-icon-box" style="background: #F0F9FF;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0EA5E9" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            رقابة المنشورات
        </a>

        {{-- سجل النشاطات --}}
        <a href="{{ route('admin.activity-logs') }}" class="{{ request()->is('admin/activity-logs') ? 'active' : '' }}" style="border-right: 3px solid #EC4899; {{ request()->is('admin/activity-logs') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #FDF2F8;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EC4899" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            سجل النشاطات
        </a>

        {{-- التقارير --}}
        <a href="{{ route('admin.reports.page') }}" class="{{ request()->is('admin/reports-page') ? 'active' : '' }}" style="border-right: 3px solid #6366F1; {{ request()->is('admin/reports-page') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #EEF2FF;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            التقارير
        </a>

        {{-- ملاحظات المحظورين --}}
        <a href="{{ route('admin.notes') }}" class="{{ request()->is('admin/notes') ? 'active' : '' }}" style="border-right: 3px solid #F97316; {{ request()->is('admin/notes') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #FFF7ED;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F97316" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            ملاحظات المحظورين
        </a>

        {{-- نسخ احتياطي --}}
        <a href="{{ route('admin.backup') }}" class="{{ request()->is('admin/backup') ? 'active' : '' }}" style="border-right: 3px solid #22C55E; {{ request()->is('admin/backup') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #F0FDF4;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
            </div>
            نسخ احتياطي
        </a>

        {{-- الإعلانات --}}
        <a href="{{ route('admin.ads') }}" class="{{ request()->is('admin/ads') ? 'active' : '' }}" style="border-right: 3px solid #EAB308; {{ request()->is('admin/ads') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #FEFCE8;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EAB308" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            الإعلانات
        </a>
    </div>

    <div style="padding: 0.75rem; border-top: 1px solid var(--border);">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fca5a5" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                تسجيل خروج
            </button>
        </form>
    </div>
</aside>