@extends('layouts.app')

@section('title', 'Job Service - ' . __('home.all_services'))

@section('content')

<!-- ===== 1. هيرو ===== -->
<section class="hero-new-design" style="background: url('{{ asset('uploads/hero/hero-bg.png') }}') center/cover no-repeat;">
    <div class="hero-overlay">
        <div class="hero-container">
            <div class="hero-content">
            <h1 style="color:#FFFFFF; text-shadow:2px 2px 8px rgba(0,0,0,0.5);">{{ __('home.hero_title') }} <br><span style="color:#FCD34D;">{{ __('home.hero_title_span') }}</span></h1>
            <p style="color:#F3F4F6; font-size:1.2rem; text-shadow:1px 1px 4px rgba(0,0,0,0.5);">{{ __('home.hero_desc') }}</p>

                <div class="hero-btns">
                    <a href="{{ route('services.index') }}" class="btn-hero-primary">{{ __('home.browse_services') }}</a>
                    @auth
                        <a href="{{ route('request.create') }}" class="btn-hero-outline">{{ __('home.request_service') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-hero-outline">{{ __('home.request_service') }}</a>
                    @endauth
                </div>
                <div class="hero-providers-stack" id="providersStack"></div>
            </div>
        </div>
    </div>
</section>

<!-- ===== 2. التصنيفات - صور توضيحية فقط ===== -->
<section class="section-new">
    <div class="section-header-new">
        <h2>
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:0.5rem;">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
            </svg>
            {{ __('home.platform_showcase') }}
        </h2>
    </div>
    <div class="categories-grid">
        <div class="category-image-card">
            <img src="{{ asset('uploads/categories/programmer.jpg') }}" alt="{{ __('home.programming') }}">
            <div class="category-overlay"><h4>{{ __('home.programming') }}</h4><span></span></div>
        </div>
        <div class="category-image-card">
            <img src="{{ asset('uploads/categories/designer.jpeg') }}" alt="{{ __('home.design') }}">
            <div class="category-overlay"><h4>{{ __('home.design') }}</h4><span></span></div>
        </div>
        <div class="category-image-card">
            <img src="{{ asset('uploads/categories/engineer.jpg') }}" alt="{{ __('home.engineering') }}">
            <div class="category-overlay"><h4>{{ __('home.engineering') }}</h4><span></span></div>
        </div>
        <div class="category-image-card">
            <img src="{{ asset('uploads/categories/technician.jpeg') }}" alt="{{ __('home.maintenance') }}">
            <div class="category-overlay"><h4>{{ __('home.maintenance') }}</h4><span></span></div>
        </div>
        <div class="category-image-card">
            <img src="{{ asset('uploads/categories/craftsman.jpeg') }}" alt="{{ __('home.crafts') }}">
            <div class="category-overlay"><h4>{{ __('home.crafts') }}</h4><span></span></div>
        </div>
        <div class="category-image-card">
            <img src="{{ asset('uploads/categories/kkk.jpeg') }}" alt="{{ __('home.electrician') }}">
            <div class="category-overlay"><h4>{{ __('home.electrician') }}</h4><span></span></div>
        </div>
        <div class="category-image-card">
            <img src="{{ asset('uploads/categories/ss.jpg') }}" alt="{{ __('home.plumber') }}">
            <div class="category-overlay"><h4>{{ __('home.plumber') }}</h4><span></span></div>
        </div>
    </div>
</section>

<!-- ===== 3. خدمات قريبة منك ===== -->
<section class="section-new" id="nearbySection">
    <div class="section-header-new">
        <h2>
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:0.5rem;">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            {{ __('services.nearby_title') }}
        </h2>
        <a href="{{ route('services.index') }}">{{ __('home.view_all') }} →</a>
    </div>
    <div class="services-grid-new" id="nearbyServicesGrid">
        <p style="text-align:center;color:var(--text-secondary);padding:2rem;">{{ __('services.searching') }}</p>
    </div>
</section>

<!-- ===== 4. الخدمات ===== -->
<section class="section-new">
    <div class="section-header-new">
        <h2>
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:0.5rem;">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
            </svg>
            {{ __('home.all_services') }}
        </h2>
        <a href="{{ route('services.index') }}">{{ __('home.view_all') }} <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><polyline points="15 18 9 12 15 6"/></svg></a>
    </div>
    <div class="services-grid-new" id="servicesGrid"></div>
    <div class="view-all-services" id="viewAllServices"></div>
</section>

<!-- ===== 5. 3 Columns ===== -->
<div class="columns-3">

    <div class="left-col">
        <div class="card-new ad-card">
            <div style="font-size:2rem;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 6V2M12 22V18M6 12H2M22 12H18M19.07 4.93L16.24 7.76M4.93 19.07L7.76 16.24M16.24 16.24L19.07 19.07M7.76 7.76L4.93 4.93"/>
                </svg>
            </div>
            <h3 class="card-title" style="text-align:center;">{{ __('home.add_featured_ad') }}</h3>
            @auth
                <a href="{{ route('stories.index') }}" class="card-btn" style="display:block;text-align:center;text-decoration:none;">{{ __('home.add_ad_now') }}</a>
            @else
                <a href="{{ route('login') }}" class="card-btn" style="display:block;text-align:center;text-decoration:none;">{{ __('home.add_ad_now') }}</a>
            @endauth
        </div>

        <div class="card-new request-card">
            <div style="font-size:2rem;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 4V20M20 12H4"/>
                </svg>
            </div>
            <h3 class="card-title" style="text-align:center;">{{ __('home.create_request') }}</h3>
            <p class="card-desc" style="text-align:center;">{{ __('home.get_offers') }}</p>
            @auth
                <a href="{{ route('request.create') }}" class="card-btn" style="display:block;text-align:center;text-decoration:none;">{{ __('home.create_request_btn') }}</a>
            @else
                <a href="{{ route('login') }}" class="card-btn" style="display:block;text-align:center;text-decoration:none;">{{ __('home.create_request_btn') }}</a>
            @endauth
        </div>

        <div class="card-new provider-card">
            <h3 class="card-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:0.5rem;">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M16 11l2 2 4-4"/>
                </svg>
                {{ __('home.suggested_providers') }}
            </h3>
            <div id="topProviders"></div>
            <a href="{{ route('providers.index') }}" class="view-all-link">{{ __('home.view_all_providers') }} →</a>
        </div>
    </div>

    <div class="middle-col">
        <div class="create-post">
            @auth
                <img src="{{ asset('uploads/avatars/' . (auth()->user()->avatar ?? 'default-avatar.png')) }}" alt="User">
                <a href="{{ route('post.create') }}" class="post-input" style="text-decoration:none;display:block;">{{ __('home.what_share') }}</a>
            @else
                <img src="{{ asset('uploads/avatars/default-avatar.png') }}" alt="User">
                <a href="{{ route('login') }}" class="post-input" style="text-decoration:none;display:block;color:var(--text-secondary);">{{ __('home.what_share') }}</a>
            @endauth
            <div class="post-options">
                <a href="{{ auth()->check() ? route('request.create') : route('login') }}" style="text-decoration:none;color:var(--text-secondary);font-size:0.75rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:0.2rem;">
                        <path d="M12 4V20M20 12H4"/>
                    </svg>
                    {{ __('home.request_short') }}
                </a>
                <a href="{{ auth()->check() ? route('service.create') : route('login') }}" style="text-decoration:none;color:var(--text-secondary);font-size:0.75rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:0.2rem;">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                    {{ __('home.service_short') }}
                </a>
                <a href="{{ auth()->check() ? route('post.create') : route('login') }}" style="text-decoration:none;color:var(--text-secondary);font-size:0.75rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:0.2rem;">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    {{ __('home.post_short') }}
                </a>
            </div>
        </div>
        <div id="postsContainer"></div>
    </div>

    <div class="right-col">
        <div class="card-new">
            <div class="card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:0.5rem;">
                    <path d="M12 6V2M12 22V18M6 12H2M22 12H18M19.07 4.93L16.24 7.76M4.93 19.07L7.76 16.24M16.24 16.24L19.07 19.07M7.76 7.76L4.93 4.93"/>
                </svg>
                {{ __('home.featured_ads') }}
                <a href="{{ route('stories.index') }}">{{ __('home.view_all') }}</a>
            </div>
            <div id="featuredAdsStories"></div>
        </div>

        <div class="card-new">
            <div class="card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:0.5rem;">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                {{ __('home.latest_requests') }}
                <a href="{{ route('requests.index') }}">{{ __('home.view_all') }}</a>
            </div>
            <div id="latestRequestsExpanded"></div>
        </div>
    </div>
</div>

<!-- ===== 6. 4 Feature Cards ===== -->
<section class="feature-cards">
    <div class="feature-card-new">
        <div class="icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <h4>{{ __('home.affordable_prices') }}</h4>
        <p>{{ __('home.affordable_prices_desc') }}</p>
    </div>
    <div class="feature-card-new">
        <div class="icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="M12 8v4"/>
                <path d="M12 16h.01"/>
            </svg>
        </div>
        <h4>{{ __('home.continuous_support') }}</h4>
        <p>{{ __('home.continuous_support_desc') }}</p>
    </div>
    <div class="feature-card-new">
        <div class="icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
        </div>
        <h4>{{ __('home.guaranteed_quality') }}</h4>
        <p>{{ __('home.guaranteed_quality_desc') }}</p>
    </div>
    <div class="feature-card-new">
        <div class="icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <h4>{{ __('home.ease_security') }}</h4>
        <p>{{ __('home.ease_security_desc') }}</p>
    </div>
</section>

@endsection

@section('scripts')
<script>
function handleError(error) { console.error('خطأ:', error); }



async function loadProviders() {
    try {
        const response = await fetch('/api/users?type=provider&limit=5');
        const data = await response.json();
        if (data.success) {
            const container = document.getElementById('providersStack');
            let html = '';
            data.data.forEach(p => {
                const avatar = p.avatar && p.avatar !== 'default-avatar.png' ? '/uploads/avatars/' + p.avatar : '/uploads/avatars/default-avatar.png';
                html += `<a href="/profile/${p.id}"><img src="${avatar}" alt="${p.name}" class="provider-avatar" title="${p.name}"></a>`;
            });
            html += `<span class="providers-count">${data.total}+</span>`;
            container.innerHTML = html;
        }
    } catch (e) { handleError(e); }
}

async function loadNearbyServices() {
    const container = document.getElementById('nearbyServicesGrid');
    
    if (!navigator.geolocation) {
        container.innerHTML = '<p style="text-align:center;color:var(--text-secondary);padding:2rem;">⚠️ متصفحك لا يدعم تحديد الموقع</p>';
        return;
    }
    
    navigator.geolocation.getCurrentPosition(async (pos) => {
        try {
            const response = await fetch(`/api/services/nearby?lat=${pos.coords.latitude}&lng=${pos.coords.longitude}`);
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                let html = '';
                data.data.slice(0, 6).forEach(s => {
                    const providerAvatar = s.provider && s.provider.avatar && s.provider.avatar !== 'default-avatar.png' 
                        ? '/uploads/avatars/' + s.provider.avatar 
                        : '/uploads/avatars/default-avatar.png';
                    const providerName = s.provider ? s.provider.name : '{{ __("services.unknown") }}';
                    const locationName = s.address || s.city || 'الموقع غير محدد';

html += `
    <div class="service-new-card gold-card">
        <h4 class="service-new-title">${s.title}</h4>
        <div class="service-new-image">
            <img src="${providerAvatar}" alt="${s.title}">
        </div>
        <a href="/profile/${s.provider_id}" style="text-decoration:none;color:inherit;"><div class="service-new-provider">👤 ${providerName}</div></a>
        <div class="service-new-price">💰 ${s.price} ر.ي</div>
        <div class="service-new-distance">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="#10B981" stroke="#10B981" stroke-width="1.5" style="vertical-align:middle; margin-left: 0.3rem;">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3" fill="white"/>
            </svg>
            ${locationName}
        </div>
        <a href="/service/${s.id}" class="btn-primary-blue">{{ __('home.view_service_btn') }}</a>
    </div>
`;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p style="text-align:center;color:var(--text-secondary);padding:2rem;">{{ __("services.no_nearby") }}</p>';
            }
        } catch (e) {
            container.innerHTML = '<p style="text-align:center;color:var(--text-secondary);padding:2rem;">❌ حدث خطأ في جلب الخدمات القريبة</p>';
        }
    }, () => {
        container.innerHTML = '<p style="text-align:center;color:var(--text-secondary);padding:2rem;">{{ __("services.allow_location") }}</p>';
    });
}

async function loadServices() {
    try {
        const response = await fetch('/api/services');
        const data = await response.json();
        if (data.success) {
            const container = document.getElementById('servicesGrid');
            let html = '';
            data.data.slice(0, 6).forEach(s => {
                const providerAvatar = s.provider && s.provider.avatar && s.provider.avatar !== 'default-avatar.png' 
                    ? '/uploads/avatars/' + s.provider.avatar 
                    : '/uploads/avatars/default-avatar.png';
                const providerName = s.provider ? s.provider.name : '{{ __("services.unknown") }}';
                
                html += `
                    <div class="service-new-card gold-card">
                        <h4 class="service-new-title">${s.title}</h4>
                        <div class="service-new-image">
                            <img src="${providerAvatar}" alt="${s.title}">
                        </div>
                        <a href="/profile/${s.provider_id}" style="text-decoration:none;color:inherit;"><div class="service-new-provider">👤 ${providerName}</div></a>
                        <div class="service-new-price">💰 ${s.price} ر.ي</div>
                        <a href="/service/${s.id}" class="btn-primary-blue">{{ __('home.view_service_btn') }}</a>
                    </div>
                `;
            });
            container.innerHTML = html;
            if (data.total > 6) {
                document.getElementById('viewAllServices').innerHTML = `<a href="/services" class="btn-view-all">{{ __('home.view_all_services_btn') }} (${data.total})</a>`;
            }
        }
    } catch (e) { handleError(e); }
}

async function loadPosts() {
    try {
        const response = await fetch('/api/posts');
        const data = await response.json();
        const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
        
        if (data.success && data.data.length > 0) {
            const container = document.getElementById('postsContainer');
            let html = '';
            data.data.forEach(p => {
                const avatar = p.user && p.user.avatar && p.user.avatar !== 'default-avatar.png' ? '/uploads/avatars/' + p.user.avatar : '/uploads/avatars/default-avatar.png';
                const name = p.user ? p.user.name : 'مستخدم';
                const userId = p.user ? p.user.id : '';
                const type = p.user && p.user.user_type === 'provider' ? 'مقدم خدمة' : 'عميل';
                const img = p.image ? '/uploads/posts/' + p.image : null;
                const likesCount = p.likes ? p.likes.length : 0;
                const comments = p.comments || [];
                const commentsCount = comments.length;
                
                const likeAction = isLoggedIn 
                    ? `onclick="toggleLike(${p.id}, this)" style="cursor:pointer;"`
                    : `onclick="window.location.href='{{ route('login') }}'" style="cursor:pointer;"`;
                const commentAction = isLoggedIn 
                    ? `onclick="toggleComments(${p.id})" style="cursor:pointer;"`
                    : `onclick="window.location.href='{{ route('login') }}'" style="cursor:pointer;"`;
                
                let commentsHtml = '';
                if (comments.length > 0) {
                    commentsHtml = '<div class="comments-list" id="commentsList-' + p.id + '" style="display:none;margin-top:0.5rem;border-top:1px solid var(--border);padding-top:0.5rem;">';
                    comments.slice(0, 5).forEach(c => {
                        const cAvatar = c.user && c.user.avatar && c.user.avatar !== 'default-avatar.png' ? '/uploads/avatars/' + c.user.avatar : '/uploads/avatars/default-avatar.png';
                        const cName = c.user ? c.user.name : 'مستخدم';
                        const cUserId = c.user ? c.user.id : '';
                        const replies = c.replies || [];
                        
                        commentsHtml += `
                            <div style="display:flex;gap:0.5rem;margin-bottom:0.5rem;align-items:flex-start;">
                                <a href="/profile/${cUserId}"><img src="${cAvatar}" style="width:28px;height:28px;border-radius:50%;flex-shrink:0;cursor:pointer;"></a>
                                <div style="flex:1;">
                                    <div style="background:var(--bg);border-radius:12px;padding:0.4rem 0.6rem;font-size:0.8rem;">
                                        <a href="/profile/${cUserId}" style="text-decoration:none;color:inherit;"><strong>${cName}</strong></a>
                                        <span style="display:block;color:var(--text-secondary);">${c.comment_text}</span>
                                    </div>
                                    ${isLoggedIn ? `<button onclick="showReplyForm(event, ${p.id}, ${c.id}, '${cName}')" style="background:none;border:none;color:var(--text-secondary);font-size:0.65rem;cursor:pointer;margin-top:0.1rem;">{{ __('comments.reply') }}</button>` : ''}
                                    <div id="replyForm-${c.id}" style="display:none;margin-top:0.3rem;"></div>
                                    
                                    ${replies.length > 0 ? `
                                        <div style="margin-right:1.5rem;margin-top:0.3rem;">
                                            ${replies.slice(0, 3).map(r => {
                                                const rAvatar = r.user && r.user.avatar && r.user.avatar !== 'default-avatar.png' ? '/uploads/avatars/' + r.user.avatar : '/uploads/avatars/default-avatar.png';
                                                const rName = r.user ? r.user.name : 'مستخدم';
                                                const rUserId = r.user ? r.user.id : '';
                                                return `
                                                    <div style="display:flex;gap:0.4rem;margin-bottom:0.3rem;align-items:flex-start;">
                                                        <a href="/profile/${rUserId}"><img src="${rAvatar}" style="width:22px;height:22px;border-radius:50%;flex-shrink:0;cursor:pointer;"></a>
                                                        <div style="background:var(--bg);border-radius:10px;padding:0.3rem 0.5rem;font-size:0.75rem;">
                                                            <a href="/profile/${rUserId}" style="text-decoration:none;color:inherit;"><strong>${rName}</strong></a>
                                                            <span style="display:block;color:var(--text-secondary);">${r.comment_text}</span>
                                                        </div>
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    });
                    if (comments.length > 5) {
                        commentsHtml += `<a href="/post/${p.id}" style="font-size:0.75rem;color:var(--primary);text-decoration:none;display:block;text-align:center;">{{ __('comments.view_all') }} (${comments.length})</a>`;
                    }
                    commentsHtml += '</div>';
                } else {
                    commentsHtml = '<div class="comments-list" id="commentsList-' + p.id + '" style="display:none;margin-top:0.5rem;border-top:1px solid var(--border);padding-top:0.5rem;"><p style="color:var(--text-secondary);font-size:0.8rem;text-align:center;">{{ __("comments.no_comments") }}</p></div>';
                }
                
                const commentBoxHtml = isLoggedIn ? `
                    <div id="commentBox-${p.id}" style="display:none;margin-top:0.5rem;border-top:1px solid var(--border);padding-top:0.5rem;">
                        <form onsubmit="submitComment(event, ${p.id}, null)" style="display:flex;gap:0.5rem;">
                            <input type="text" name="comment_text" placeholder="{{ __('comments.write_comment') }}" required style="flex:1;padding:0.5rem 0.75rem;border:1px solid var(--border);border-radius:2rem;font-family:'Tajawal',sans-serif;font-size:0.8rem;">
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('Send') }}</button>
                        </form>
                    </div>` : '';
                
                html += `
                    <div class="post-card" id="post-${p.id}">
                        <div class="post-header">
                            <a href="/profile/${userId}"><img src="${avatar}" alt="${name}" style="cursor:pointer;"></a>
                            <div class="post-user">
                                <a href="/profile/${userId}" style="text-decoration:none;color:inherit;"><div class="name">${name} <span class="badge">${type}</span></div></a>
                                <div class="time">${p.created_at ? new Date(p.created_at).toLocaleDateString('ar') : ''}</div>
                            </div>
                            
                        </div>
                            <div class="post-content"><p>${p.content}</p>${img ? `<img src="${img}" alt="صورة المنشور">` : ''}</div>
                        <div class="post-actions">
                            <button ${likeAction}>❤️ <span class="like-count">${likesCount}</span></button>
                            <button ${commentAction}>💬 <span>${commentsCount}</span></button>
                        </div>
                        ${commentsHtml}
                        ${commentBoxHtml}
                    </div>
                `;
            });
            container.innerHTML = html;
        } else {
            document.getElementById('postsContainer').innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-secondary);">{{ __("posts.no_posts") }}</div>';
        }
    } catch (e) {
        document.getElementById('postsContainer').innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-secondary);">حدث خطأ في تحميل المنشورات</div>';
    }
}

async function loadRequestsExpanded() {
    try {
        const response = await fetch('/api/requests');
        const data = await response.json();
        if (data.success) {
            const container = document.getElementById('latestRequestsExpanded');
            let html = '';
            data.data.slice(0, 4).forEach(r => {
                const userName = r.user ? r.user.name : 'عميل';
                const userAvatar = r.user && r.user.avatar && r.user.avatar !== 'default-avatar.png' ? '/uploads/avatars/' + r.user.avatar : '/uploads/avatars/default-avatar.png';
                html += `
                    <div class="request-expanded-item">
                        <div class="request-header">
                            <img src="${userAvatar}" alt="${userName}">
                            <div>
                                <div class="request-title">${r.title}</div>
                                <div class="request-client">${userName}</div>
                            </div>
                        </div>
                        <div class="request-meta">
                            <span class="request-budget">💰 ${r.budget} ر.ي</span>
                            <span class="request-offers">📋 ${r.offers_count || 0} عروض</span>
                            <span class="request-date">🕐 ${r.created_at ? new Date(r.created_at).toLocaleDateString('ar') : ''}</span>
                        </div>
                  <a href="/request/${r.id}" class="request-view-btn">{{ __('View Details') }} →</a>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
    } catch (e) { handleError(e); }
}

async function loadAdsAsStories() {
    try {
        const response = await fetch('/api/stories?is_ad=1');
        const data = await response.json();
        if (data.success) {
            const container = document.getElementById('featuredAdsStories');
            let html = '';
            data.data.slice(0, 3).forEach(ad => {
                const image = ad.image ? '/uploads/stories/' + ad.image : '/uploads/stories/default-ad.png';
                html += `
                    <div class="ad-story-item" onclick="openStoryViewer(${ad.user_id})">
                        <img src="${image}" alt="${ad.caption || 'إعلان'}">
                        <div class="ad-story-overlay"><span>📢 ${ad.caption || 'إعلان مميز'}</span></div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
    } catch (e) { handleError(e); }
}

async function loadTopProviders() {
    try {
        const response = await fetch('/api/users?type=provider&limit=3&sort=rating');
        const data = await response.json();
        if (data.success) {
            const container = document.getElementById('topProviders');
            let html = '';
            data.data.forEach(p => {
                const avatar = p.avatar && p.avatar !== 'default-avatar.png' ? '/uploads/avatars/' + p.avatar : '/uploads/avatars/default-avatar.png';
                const specialty = p.services && p.services.length > 0 ? p.services[0].title : 'مقدم خدمة';
                const rating = p.average_rating ? parseFloat(p.average_rating).toFixed(1) : '4.8';
                html += `
                    <a href="/profile/${p.id}" style="text-decoration:none;color:inherit;">
                        <div class="provider-item" style="cursor:pointer;">
                            <img src="${avatar}" alt="${p.name}">
                            <div class="info">
                                <div class="name">${p.name}</div>
                                <div class="specialty">${specialty}</div>
                                <div class="rating">⭐ ${rating}</div>
                            </div>
                        </div>
                    </a>
                `;
            });
            container.innerHTML = html;
        }
    } catch (e) { handleError(e); }
}

document.addEventListener('DOMContentLoaded', function() {
    loadProviders();
    loadNearbyServices();
    loadServices();
    loadPosts();
    loadRequestsExpanded();
    loadAdsAsStories();
    loadTopProviders();
});

function toggleComments(postId) {
    var commentsList = document.getElementById('commentsList-' + postId);
    var commentBox = document.getElementById('commentBox-' + postId);
    
    if (commentsList) {
        if (commentsList.style.display === 'none' || commentsList.style.display === '') {
            commentsList.style.display = 'block';
            if (commentBox) commentBox.style.display = 'block';
        } else {
            commentsList.style.display = 'none';
            if (commentBox) commentBox.style.display = 'none';
        }
    }
}

function showReplyForm(event, postId, commentId, commentUser) {
    event.stopPropagation();
    var formDiv = document.getElementById('replyForm-' + commentId);
    if (formDiv) {
        if (formDiv.style.display === 'none' || formDiv.style.display === '') {
            formDiv.style.display = 'block';
            formDiv.innerHTML = `
                <form onsubmit="submitComment(event, ${postId}, ${commentId})" style="display:flex;gap:0.3rem;">
                    <input type="text" name="comment_text" placeholder="{{ __('comments.reply_to') }} ${commentUser}..." required style="flex:1;padding:0.4rem 0.6rem;border:1px solid var(--border);border-radius:2rem;font-family:'Tajawal',sans-serif;font-size:0.75rem;">
                    <button type="submit" class="btn btn-primary btn-sm" style="font-size:0.7rem;padding:0.3rem 0.8rem;">{{ __('comments.reply') }}</button>
                </form>
            `;
        } else {
            formDiv.style.display = 'none';
            formDiv.innerHTML = '';
        }
    }
}

function submitComment(event, postId, parentId = null) {
    event.preventDefault();
    var form = event.target;
    var input = form.querySelector('input[name="comment_text"]');
    if (!input) return;
    var text = input.value.trim();
    if (!text) return;

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/post/' + postId + '/comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ comment_text: text, parent_id: parentId })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            input.value = '';
            showToast2026('تم إضافة التعليق ✅', 'success');
            loadPosts();
        } else {
            showToast2026('حدث خطأ في إضافة التعليق', 'error');
        }
    })
    .catch(function() {
        showToast2026('حدث خطأ، حاول مرة أخرى', 'error');
    });
}

function toggleLike(postId, btn) {
    var likeSpan = btn.querySelector('.like-count');
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/post/' + postId + '/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            if (likeSpan) likeSpan.textContent = data.likes_count;
            loadPosts();
        }
    })
    .catch(function() {
        showToast2026('حدث خطأ', 'error');
    });
}
</script>
@endsection