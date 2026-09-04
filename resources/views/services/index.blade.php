@extends('layouts.app')

@section('title', __('home.all_services'))

@section('content')

<div class="page-container">
    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Back to Home') }}
    </a>

    <div class="section-header-new">
        <h2>{{ __('home.all_services') }}</h2>
    </div>

    <div class="services-grid-new" id="allServicesGrid"></div>
    <div id="paginationContainer" style="text-align:center;margin-top:2rem;"></div>
</div>

@endsection

@section('scripts')
<script>
async function loadAllServices(page = 1) {
    try {
        const response = await fetch(`/api/services?page=${page}`);
        const data = await response.json();
        
        if (data.success) {
            const container = document.getElementById('allServicesGrid');
            let html = '';
            
            data.data.forEach(s => {
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
                        <div class="service-new-price">💰 ${s.price} ر.س</div>
                        <a href="/service/${s.id}" class="btn-primary-blue">{{ __('home.view_service_btn') }}</a>
                    </div>
                `;
            });
            
            container.innerHTML = html || '<p style="text-align:center;color:var(--text-secondary);">{{ __("services.no_services") }}</p>';
            
            let paginationHtml = '';
            if (data.current_page > 1) {
                paginationHtml += `<button onclick="loadAllServices(${data.current_page - 1})" class="btn btn-outline btn-sm">⬅ {{ __('services.previous') }}</button> `;
            }
            paginationHtml += `<span>{{ __('services.page') }} ${data.current_page} {{ __('services.of') }} ${data.last_page}</span> `;
            if (data.current_page < data.last_page) {
                paginationHtml += `<button onclick="loadAllServices(${data.current_page + 1})" class="btn btn-outline btn-sm">{{ __('services.next') }} ➡</button>`;
            }
            document.getElementById('paginationContainer').innerHTML = paginationHtml;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    } catch (e) {
        console.error('خطأ:', e);
    }
}

document.addEventListener('DOMContentLoaded', () => loadAllServices());
</script>
@endsection