<aside class="admin-sidebar">
    <div class="admin-sidebar-logo" style="display: flex; align-items: center; justify-content: center; padding: 1.5rem; border-bottom: 1px solid var(--border);">
        <a href="{{ route('admin.dashboard') }}" style="display:flex; align-items:center; gap:0.5rem; text-decoration:none;">
           <img src="{{ asset('assets/branding/logo-full.png') }}" alt="JOB SERVICE" class="branding-logo sidebar">
                
    </div>

    <div style="padding: 0.5rem; overflow-y: auto; height: calc(100vh - 270px);">
        {{-- الرئيسية --}}
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}" style="border-right: 3px solid #1E2A4A; {{ request()->is('admin/dashboard') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #E8ECF4;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1E2A4A" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </div>
            الرئيسية
        </a>

        {{-- المعاملات المالية --}}
        <a href="{{ route('admin.transactions') }}" class="{{ request()->is('admin/transactions') ? 'active' : '' }}" style="border-right: 3px solid #C9A24B; {{ request()->is('admin/transactions') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #FDF6E9;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C9A24B" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            المعاملات المالية
        </a>

        {{-- توثيق المستخدمين --}}
        <a href="{{ route('admin.verifications') }}" class="{{ request()->is('admin/verifications') ? 'active' : '' }}" style="border-right: 3px solid #14B8A6; {{ request()->is('admin/verifications') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #F0FDFA;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#14B8A6" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
            </div>
            توثيق المستخدمين
        </a>

        {{-- المستخدمون --}}
        <a href="{{ route('admin.users') }}" class="{{ request()->is('admin/users') ? 'active' : '' }}" style="border-right: 3px solid #5A6B8A; {{ request()->is('admin/users') ? '' : 'border-right-color: transparent;' }}">
            <div class="sidebar-icon-box" style="background: #F5F7FA;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#5A6B8A" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
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

        {{-- تسجيل الخروج --}}
        <form method="POST" action="{{ route('admin.logout') }}" style="margin-top: 0.5rem;">
            @csrf
            <button type="submit" class="sidebar-logout-btn">
                🚪 تسجيل خروج
            </button>
        </form>
    </div>
</aside>