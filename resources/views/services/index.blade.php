@extends('layouts.app')

@section('title', __('home.all_services'))

@section('content')

<div class="page-container">
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
                const hasImage = s.image && s.image !== 'service-default.png' && s.image !== '';
                const imageHtml = hasImage 
                    ? `<img src="/uploads/services/${s.image}" alt="${s.title}">`
                    : `<div class="service-avatar-placeholder">${s.title.charAt(0)}</div>`;
                const providerName = s.provider ? s.provider.name : 'غير معروف';
                
                html += `
                    <div class="service-new-card gold-card">
                        <h4 class="service-new-title">${s.title}</h4>
                        <div class="service-new-image">${imageHtml}</div>
                        <div class="service-new-provider">👤 ${providerName}</div>
                        <div class="service-new-price">💰 ${s.price} ر.س</div>
                        <a href="/service/${s.id}" class="btn-primary-blue">عرض الخدمة ←</a>
                    </div>
                `;
            });
            
            container.innerHTML = html || '<p style="text-align:center;color:var(--text-secondary);">لا توجد خدمات</p>';
            
            let paginationHtml = '';
            if (data.current_page > 1) {
                paginationHtml += `<button onclick="loadAllServices(${data.current_page - 1})" class="btn btn-outline btn-sm">⬅ السابق</button> `;
            }
            paginationHtml += `<span>صفحة ${data.current_page} من ${data.last_page}</span> `;
            if (data.current_page < data.last_page) {
                paginationHtml += `<button onclick="loadAllServices(${data.current_page + 1})" class="btn btn-outline btn-sm">التالي ➡</button>`;
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