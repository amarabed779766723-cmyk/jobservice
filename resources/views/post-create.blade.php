@extends('layouts.app')
@section('title', 'إنشاء منشور - Job Service')
@section('content')
<div style="max-width:650px;margin:0 auto;padding:1.5rem 1rem;">
    <a href="{{ route('home') }}" class="back-link">← رجوع للرئيسية</a>
    <h2>📝 إنشاء منشور جديد</h2>

    @if($errors->any())<div class="error-msg">{{ $errors->first() }}</div>@endif

    <form id="postForm" method="POST" action="{{ route('post.store') }}" enctype="multipart/form-data" class="card">
        @csrf
        <div class="input-group">
            <textarea name="content" rows="4" placeholder="ما الذي تريد مشاركته؟"></textarea>
        </div>

        <!-- صورة مع قص -->
        <div class="input-group" style="text-align:center;">
            <label>📷 صورة</label>
            <div id="postImagePreview" style="display:none; margin-bottom:0.5rem;">
                <img id="postImagePreviewImg" style="max-width:100%; max-height:200px; border-radius:8px;">
            </div>
            <input type="file" id="postImageInput" accept="image/*" onchange="openCropper(this, 'post')">
            <input type="hidden" name="image_crop" id="postImageCropData">
            <small style="color:var(--text-secondary);">اختر صورة ثم قصها</small>
        </div>

        <!-- فيديو مع قص -->
        <div class="input-group" style="text-align:center;">
            <label>🎬 فيديو</label>
            <div id="videoTrimmer" style="display:none; background:var(--bg); border-radius:12px; padding:1rem; margin-top:0.5rem;">
                <video id="videoPreview" style="width:100%; max-height:250px; border-radius:8px;" controls></video>
                <div style="display:flex; gap:0.5rem; margin-top:0.75rem; align-items:center;">
                    <span style="font-size:0.85rem;">⏱️ البداية:</span>
                    <input type="range" id="trimStart" min="0" max="100" value="0" oninput="updateTrimRange()" style="flex:1;">
                    <span id="trimStartTime" style="font-size:0.85rem; min-width:45px;">0:00</span>
                </div>
                <div style="display:flex; gap:0.5rem; margin-top:0.25rem; align-items:center;">
                    <span style="font-size:0.85rem;">⏱️ النهاية:</span>
                    <input type="range" id="trimEnd" min="0" max="100" value="100" oninput="updateTrimRange()" style="flex:1;">
                    <span id="trimEndTime" style="font-size:0.85rem; min-width:45px;">0:00</span>
                </div>
                <button type="button" onclick="trimVideo()" class="btn btn-primary btn-sm" style="margin-top:0.5rem; width:100%;">✂️ قص الفيديو</button>
                <p id="trimStatus" style="font-size:0.8rem; color:var(--text-secondary); margin-top:0.25rem; display:none;">جاري القص...</p>
            </div>
            <input type="file" id="postVideoInput" name="video" accept="video/*" onchange="initVideoTrimmer(this)" style="margin-top:0.5rem;">
            <input type="hidden" name="trimmed_video" id="trimmedVideoData">
            <small style="color:var(--text-secondary);">اختر فيديو، حدد البداية والنهاية، ثم اضغط "قص الفيديو"</small>
        </div>

        <div class="input-group"><label>📎 ملف (PDF, Word...)</label><input type="file" name="file_attachment"></div>
        <button type="submit" class="btn btn-primary" style="width:100%;">نشر</button>
    </form>
</div>

<!-- مودال قص الصورة -->
<div id="cropperModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.9); z-index:9999; flex-direction:column; align-items:center; justify-content:center; overflow:auto; padding:20px;">
    <canvas id="cropCanvas" style="max-width:90vw; max-height:50vh; border:2px solid white; cursor:move;"></canvas>
    <div style="margin-top:1rem; display:flex; gap:1rem;">
        <button type="button" onclick="saveCrop()" class="btn btn-success">✅ قص وحفظ</button>
        <button type="button" onclick="closeCropper()" class="btn btn-outline" style="color:white; border-color:white;">❌ إلغاء</button>
    </div>
</div>

<script>
let videoFile = null;
let videoUrl = null;

function initVideoTrimmer(input) {
    videoFile = input.files[0];
    if (!videoFile) return;

    if (videoUrl) URL.revokeObjectURL(videoUrl);
    videoUrl = URL.createObjectURL(videoFile);
    
    const video = document.getElementById('videoPreview');
    video.src = videoUrl;

    video.onloadedmetadata = function() {
        const duration = video.duration;
        document.getElementById('trimEnd').max = duration;
        document.getElementById('trimEnd').value = duration;
        document.getElementById('trimEndTime').textContent = formatTime(duration);
        document.getElementById('trimStartTime').textContent = '0:00';
        document.getElementById('trimStart').value = 0;
        document.getElementById('videoTrimmer').style.display = 'block';
        document.getElementById('trimmedVideoData').value = '';
        document.getElementById('trimStatus').style.display = 'none';
    };
}

function updateTrimRange() {
    const start = parseFloat(document.getElementById('trimStart').value);
    const end = parseFloat(document.getElementById('trimEnd').value);

    if (start >= end) {
        document.getElementById('trimStart').value = end - 0.5;
        return;
    }

    document.getElementById('trimStartTime').textContent = formatTime(start);
    document.getElementById('trimEndTime').textContent = formatTime(end);
    document.getElementById('videoPreview').currentTime = start;
}

function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = Math.floor(seconds % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
}

async function trimVideo() {
    if (!videoFile) return;
    
    const start = parseFloat(document.getElementById('trimStart').value);
    const end = parseFloat(document.getElementById('trimEnd').value);
    
    if (start >= end) return;
    
    document.getElementById('trimStatus').style.display = 'block';
    document.getElementById('trimStatus').textContent = 'جاري القص...';
    document.getElementById('trimStatus').style.color = 'var(--text-secondary)';
    
    try {
        const video = document.createElement('video');
        video.src = videoUrl;
        video.muted = true;
        
        await new Promise((resolve) => {
            video.onloadedmetadata = resolve;
            if (video.readyState >= 1) resolve();
        });
        
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 360;
        const ctx = canvas.getContext('2d');
        
        const stream = canvas.captureStream(30);
        
        const mimeType = MediaRecorder.isTypeSupported('video/mp4') ? 'video/mp4' :
                        MediaRecorder.isTypeSupported('video/webm;codecs=vp8') ? 'video/webm;codecs=vp8' : 'video/webm';
        
        const recorder = new MediaRecorder(stream, { mimeType: mimeType });
        const chunks = [];
        
        recorder.ondataavailable = (e) => chunks.push(e.data);
        
        recorder.onstop = () => {
            const blob = new Blob(chunks, { type: mimeType });
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('trimmedVideoData').value = reader.result;
                document.getElementById('trimStatus').textContent = '✅ تم القص بنجاح!';
                document.getElementById('trimStatus').style.color = '#10b981';
            };
            reader.readAsDataURL(blob);
        };
        
        video.currentTime = start;
        recorder.start();
        
        video.ontimeupdate = () => {
            if (video.currentTime >= end) {
                video.pause();
                recorder.stop();
                return;
            }
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        };
        
        await video.play();
        
    } catch (error) {
        document.getElementById('trimStatus').textContent = '❌ فشل القص: ' + error.message;
        document.getElementById('trimStatus').style.color = '#ef4444';
    }
}
</script>

@endsection