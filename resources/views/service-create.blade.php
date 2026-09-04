@extends('layouts.app')
@section('title', __('Create Service') . ' - Job Service')
@section('content')
<div style="max-width: 650px; margin: 0 auto; padding: 1.5rem 1rem;">
    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Home') }}
    </a>
    <h2>🛠️ {{ __('Create Service') }}</h2>

    @if(session('success'))<div class="success-msg">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="error-msg">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('service.store') }}" enctype="multipart/form-data" class="card" id="serviceForm">
        @csrf
        <div class="input-group">
            <label>{{ __('Service Title') }}</label>
            <input type="text" name="title" required>
        </div>
        <div class="input-group">
            <label>{{ __('Service Description') }}</label>
            <textarea name="description" rows="4"></textarea>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="input-group">
                <label>{{ __('Price') }} (ر.ي)</label>
                <input type="number" name="price" step="0.01" min="1" required>
            </div>
            <div class="input-group">
                <label>{{ __('Duration') }}</label>
                <input type="text" name="duration" placeholder="{{ __('e.g. 3 days') }}">
            </div>
        </div>
        <div class="input-group">
            <label>{{ __('Service Image') }}</label>
            <input type="file" name="image">
        </div>

        {{-- قسم الموقع --}}
        <div class="input-group">
            <label>📍 {{ __('Service Location') }}</label>
            
            <input type="text" id="address" name="address" 
                   placeholder="{{ __('Search for address...') }}" 
                   style="margin-bottom: 0.5rem; width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 8px;">
            
            <div id="map" style="height: 300px; border-radius: 8px; margin-top: 0.5rem;"></div>
            
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="city" id="city">
            
            <button type="button" onclick="getUserLocation()" 
                    class="btn btn-outline btn-sm" 
                    style="margin-top: 0.5rem; width: 100%; padding: 0.5rem;">
                📍 {{ __('Use my current location') }}
            </button>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">{{ __('Publish Service') }}</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
let map, marker;

function initMap() {
    const defaultLocation = { lat: 15.3694, lng: 44.1910 };
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: defaultLocation,
        mapTypeControl: true,
        streetViewControl: false,
        fullscreenControl: true
    });

    map.addListener('click', (event) => {
        placeMarker(event.latLng);
        getAddressFromCoords(event.latLng.lat(), event.latLng.lng());
    });

    const input = document.getElementById('address');
    const autocomplete = new google.maps.places.Autocomplete(input);
    
    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        
        if (place.geometry) {
            const location = {
                lat: place.geometry.location.lat(),
                lng: place.geometry.location.lng()
            };
            
            map.setCenter(location);
            map.setZoom(15);
            placeMarker(location);
            
            let city = '';
            place.address_components.forEach(component => {
                if (component.types.includes('locality') || component.types.includes('administrative_area_level_1')) {
                    city = component.long_name;
                }
            });
            document.getElementById('city').value = city;
            document.getElementById('address').value = place.formatted_address || place.name;
        }
    });
}

function placeMarker(location) {
    if (marker) marker.setMap(null);
    
    marker = new google.maps.Marker({
        position: location,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP
    });
    
    document.getElementById('latitude').value = location.lat();
    document.getElementById('longitude').value = location.lng();
    
    marker.addListener('dragend', () => {
        const pos = marker.getPosition();
        document.getElementById('latitude').value = pos.lat();
        document.getElementById('longitude').value = pos.lng();
        getAddressFromCoords(pos.lat(), pos.lng());
    });
}

function getAddressFromCoords(lat, lng) {
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: { lat, lng } }, (results, status) => {
        if (status === 'OK' && results[0]) {
            document.getElementById('address').value = results[0].formatted_address;
            
            let city = '';
            results[0].address_components.forEach(component => {
                if (component.types.includes('locality') || component.types.includes('administrative_area_level_1')) {
                    city = component.long_name;
                }
            });
            document.getElementById('city').value = city;
        }
    });
}

function getUserLocation() {
    if (navigator.geolocation) {
        const options = {
            enableHighAccuracy: true,
            timeout: 20000,
            maximumAge: 0
        };
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const location = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                console.log('✅ موقعك الحقيقي:', location.lat, location.lng);
                
                map.setCenter(location);
                map.setZoom(17);
                placeMarker(location);
                getAddressFromCoords(location.lat, location.lng);
            },
            (error) => {
                alert('تعذر تحديد موقعك');
            },
            options
        );
    } else {
        alert('متصفحك لا يدعم تحديد الموقع');
    }
}

// ✅ منع الإرسال بدون تحديد الموقع
document.getElementById('serviceForm').addEventListener('submit', function(e) {
    var lat = document.getElementById('latitude').value;
    var lng = document.getElementById('longitude').value;
    
    if (!lat || !lng) {
        e.preventDefault();
        alert('⚠️ يجب تحديد موقع الخدمة على الخريطة أولاً - اضغط على الخريطة أو استخدم "موقعي الحالي"');
    }
});
</script>

{{-- تحميل Google Maps API --}}
<script async defer 
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initMap">
</script>
@endsection