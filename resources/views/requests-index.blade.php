@extends('layouts.app')
@section('title', __('Requests') . ' - Job Service')
@section('content')

<div class="page-container">
    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Home') }}
    </a>
    <h2>📋 {{ __('Requests') }}</h2>
    
    <div id="allRequestsContainer"></div>
    <div id="paginationContainer" style="text-align:center;margin-top:2rem;"></div>
</div>

@endsection

@section('scripts')
<script>
async function loadAllRequests(page = 1) {
    try {
        const response = await fetch(`/api/requests?page=${page}`);
        const data = await response.json();
        
        if (data.success) {
            const container = document.getElementById('allRequestsContainer');
            let html = '';
            
            data.data.forEach(r => {
                const userName = r.user ? r.user.name : '{{ __("Client") }}';
                const userAvatar = r.user && r.user.avatar && r.user.avatar !== 'default-avatar.png' ? '/uploads/avatars/' + r.user.avatar : '/uploads/avatars/default-avatar.png';
                
                html += `
                    <div class="card">
                        <div class="card-header">
                            <img src="${userAvatar}" class="card-avatar" alt="">
                            <div>
                                <div class="card-user">${userName}</div>
                                <div class="card-meta">${new Date(r.created_at).toLocaleDateString('ar')}</div>
                            </div>
                        </div>
                        <a href="/request/${r.id}" style="text-decoration:none;color:inherit;">
                            <h3 class="card-title">${r.title}</h3>
                        </a>
                        <p class="card-body">${r.description ? r.description.substring(0, 150) + '...' : ''}</p>
                        <div class="card-footer">
                            <span class="card-price">{{ __('Budget') }}: ${Number(r.budget).toLocaleString()} ر.س</span>
                            <a href="/request/${r.id}" class="btn btn-primary btn-sm">{{ __('Submit Offer') }}</a>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html || '<div class="card"><p>{{ __("No Requests") }}</p></div>';
            
            let paginationHtml = '';
            if (data.current_page > 1) {
                paginationHtml += `<button onclick="loadAllRequests(${data.current_page - 1})" class="btn btn-outline btn-sm">⬅ السابق</button> `;
            }
            paginationHtml += `<span>صفحة ${data.current_page} من ${data.last_page}</span> `;
            if (data.current_page < data.last_page) {
                paginationHtml += `<button onclick="loadAllRequests(${data.current_page + 1})" class="btn btn-outline btn-sm">التالي ➡</button>`;
            }
            document.getElementById('paginationContainer').innerHTML = paginationHtml;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    } catch (e) {
        console.error('خطأ:', e);
    }
}

document.addEventListener('DOMContentLoaded', () => loadAllRequests());
</script>
@endsection