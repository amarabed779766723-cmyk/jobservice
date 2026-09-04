@extends('layouts.app')
@section('title', 'تعديل الملف - Job Service')
@section('content')
<div style="max-width:650px;margin:0 auto;padding:1.5rem 1rem;">
    <a href="{{ route('profile') }}" class="back-link">← رجوع للملف الشخصي</a>
    <h2 style="margin-bottom:1rem;">تعديل الملف الشخصي</h2>

    @if(session('success'))
        <div class="success-msg">✅ {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
    @endif

    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card">
        @csrf

        <div class="input-group" style="text-align:center;">
            <label>الصورة الشخصية</label>
            <div class="crop-area">
                <img id="avatarPreview" src="{{ asset('uploads/avatars/' . ($currentUser->avatar ?? 'default-avatar.png')) }}" 
                     style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #dbeafe;display:block;margin:0 auto 0.5rem;">
            </div>
            <input type="file" id="avatarInput" accept="image/*" onchange="openCropper(this, 'avatar')">
            <input type="hidden" name="avatar_crop" id="avatarCropData">
            <small style="color:var(--text-secondary);">اضغط لاختيار صورة ثم قصها</small>
        </div>

        <div class="input-group" style="text-align:center;">
            <label>صورة الغلاف</label>
            <div class="crop-area">
                <div id="coverPreview" style="background:url('{{ asset('uploads/covers/' . ($currentUser->cover ?? 'default-cover.jpg')) }}') center/cover; height:120px; border-radius:8px; margin-bottom:0.5rem; background-color:#e5e7eb;"></div>
            </div>
            <input type="file" id="coverInput" accept="image/*" onchange="openCropper(this, 'cover')">
            <input type="hidden" name="cover_crop" id="coverCropData">
            <small style="color:var(--text-secondary);">اضغط لاختيار صورة ثم قصها</small>
        </div>

        <div class="input-group"><label>الاسم الكامل</label><input type="text" name="name" value="{{ $currentUser->name }}" required></div>
        <div class="input-group"><label>النبذة التعريفية</label><textarea name="bio" rows="3">{{ $currentUser->bio }}</textarea></div>
        <div class="input-group"><label>رقم الجوال</label><input type="tel" name="phone" value="{{ $currentUser->phone }}"></div>
        <div class="input-group"><label>المدينة</label><input type="text" name="city" id="cityInput" value="{{ $currentUser->city }}"></div>

        {{-- ✅ زر تحديد الموقع تلقائياً --}}
        <div class="input-group">
            <label>📍 الموقع الجغرافي</label>
            <div style="display:flex; gap:0.5rem; align-items:center;">
                <input type="text" name="address" id="addressInput" value="{{ $currentUser->address }}" placeholder="عنوانك" style="flex:1;">
                <button type="button" onclick="getCurrentLocation()" class="btn btn-primary btn-sm" style="white-space:nowrap;">
                    📍 تحديد موقعي
                </button>
            </div>
            <input type="hidden" name="latitude" id="latitudeInput" value="{{ $currentUser->latitude }}">
            <input type="hidden" name="longitude" id="longitudeInput" value="{{ $currentUser->longitude }}">
            <small style="color:var(--text-secondary);" id="locationStatus">
                @if($currentUser->latitude && $currentUser->longitude)
                    ✅ تم حفظ موقعك
                @else
                    ⚠️ لم يتم تحديد موقعك بعد
                @endif
            </small>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;">💾 حفظ التغييرات</button>
    </form>
</div>

<div id="cropperModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.9); z-index:9999; flex-direction:column; align-items:center; justify-content:center; overflow:auto; padding:20px;">
    <canvas id="cropCanvas" style="max-width:90vw; max-height:50vh; border:2px solid white; cursor:move;"></canvas>
    <div style="margin-top:1rem; display:flex; gap:1rem;">
        <button onclick="saveCrop()" class="btn btn-success">✅ قص وحفظ</button>
        <button onclick="closeCropper()" class="btn btn-outline" style="color:white; border-color:white;">❌ إلغاء</button>
    </div>
</div>

{{-- ✅ سكريبت تحديد الموقع --}}
<script>
function getCurrentLocation() {
    if (!navigator.geolocation) {
        alert('⚠️ متصفحك لا يدعم تحديد الموقع');
        return;
    }

    document.getElementById('locationStatus').textContent = '⏳ جاري تحديد موقعك...';

    var options = {
        enableHighAccuracy: true,
        timeout: 20000,
        maximumAge: 0
    };

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            
            document.getElementById('latitudeInput').value = lat;
            document.getElementById('longitudeInput').value = lng;
            
            // ✅ جلب العنوان - مع User-Agent إجباري
            var url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng + '&accept-language=ar';
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'User-Agent': 'JobService/1.0 (jobservice@gmail.com)',
                    'Accept': 'application/json'
                }
            })
            .then(function(r) {
                if (!r.ok) {
                    throw new Error('HTTP ' + r.status);
                }
                return r.json();
            })
            .then(function(data) {
                console.log('✅ بيانات الموقع:', data);
                
                var address = data.display_name || '';
                var city = '';
                var road = '';
                var suburb = '';
                var neighbourhood = '';
                
                if (data.address) {
                    road = data.address.road || data.address.pedestrian || '';
                    suburb = data.address.suburb || data.address.neighbourhood || '';
                    city = data.address.city || data.address.town || data.address.village || data.address.state || '';
                    
                    // ✅ تجميع العنوان بالتفصيل - الشارع + الحي + المدينة
                    var parts = [];
                    if (road) parts.push(road);
                    if (suburb) parts.push(suburb);
                    if (city) parts.push(city);
                    
                    address = parts.join('، ') || address;
                }
                
                // ✅ تعبئة الحقول
                if (city) {
                    document.getElementById('cityInput').value = city;
                }
                if (address) {
                    document.getElementById('addressInput').value = address;
                }
                
                document.getElementById('locationStatus').textContent = '✅ تم تحديد موقعك: ' + (address || city || 'تم');
            })
            .catch(function(error) {
                console.log('❌ خطأ:', error);
                
                // ✅ إذا فشل - جرب Google Geocoding
                var googleUrl = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' + lat + ',' + lng + '&key={{ config("services.google_maps.api_key") }}&language=ar';
                
                return fetch(googleUrl);
            })
            .then(function(r) {
                if (r && r.ok) {
                    return r.json();
                }
                return null;
            })
            .then(function(data) {
                if (data && data.results && data.results.length > 0) {
                    var result = data.results[0];
                    var address = result.formatted_address;
                    var city = '';
                    
                    result.address_components.forEach(function(component) {
                        if (component.types.includes('locality') || component.types.includes('administrative_area_level_1')) {
                            city = component.long_name;
                        }
                    });
                    
                    if (city) document.getElementById('cityInput').value = city;
                    if (address) document.getElementById('addressInput').value = address;
                    
                    document.getElementById('locationStatus').textContent = '✅ تم تحديد موقعك: ' + (city || address || 'تم');
                }
            })
            .catch(function(error) {
                console.log('❌ خطأ نهائي:', error);
                document.getElementById('locationStatus').textContent = '✅ تم تحديد موقعك (الإحداثيات: ' + lat.toFixed(4) + ', ' + lng.toFixed(4) + ')';
            });
        },
        function(err) {
            switch(err.code) {
                case err.PERMISSION_DENIED:
                    document.getElementById('locationStatus').textContent = '❌ تم رفض إذن الموقع';
                    break;
                case err.POSITION_UNAVAILABLE:
                    document.getElementById('locationStatus').textContent = '❌ معلومات الموقع غير متاحة';
                    break;
                case err.TIMEOUT:
                    document.getElementById('locationStatus').textContent = '❌ انتهت المهلة';
                    break;
                default:
                    document.getElementById('locationStatus').textContent = '❌ خطأ: ' + err.message;
                    break;
            }
        },
        options
    );
}
</script>

@endsection