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

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            
            document.getElementById('latitudeInput').value = lat;
            document.getElementById('longitudeInput').value = lng;
            
            // ✅ جلب اسم المدينة من الإحداثيات
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&accept-language=ar')
                .then(r => r.json())
                .then(data => {
                    var city = data.address.city || data.address.town || data.address.village || data.address.state || 'موقعك الحالي';
                    document.getElementById('cityInput').value = city;
                    document.getElementById('addressInput').value = data.display_name;
                    document.getElementById('locationStatus').textContent = '✅ تم تحديد موقعك: ' + city;
                })
                .catch(function() {
                    document.getElementById('cityInput').value = lat.toFixed(4) + ', ' + lng.toFixed(4);
                    document.getElementById('locationStatus').textContent = '✅ تم تحديد موقعك';
                });
        },
        function(err) {
            document.getElementById('locationStatus').textContent = '❌ لم نتمكن من تحديد موقعك. تأكد من تفعيل GPS.';
        }
    );
}
</script>

@endsection