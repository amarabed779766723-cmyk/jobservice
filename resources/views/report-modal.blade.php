<!-- نافذة الإبلاغ المنبثقة -->
<div id="reportModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:var(--bg-card); border-radius:12px; padding:2rem; width:90%; max-width:450px;">
        <h3 style="margin-bottom:1rem;">🚩 إبلاغ عن محتوى</h3>
        <form method="POST" action="{{ route('report.store') }}">
            @csrf
            <input type="hidden" name="reported_user_id" id="reportUserId">
            <input type="hidden" name="service_id" id="reportServiceId">
            <input type="hidden" name="post_id" id="reportPostId">
            <div class="input-group">
                <label>سبب البلاغ</label>
                <textarea name="reason" rows="4" required placeholder="اذكر سبب الإبلاغ..."></textarea>
            </div>
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn btn-danger">إرسال البلاغ</button>
                <button type="button" class="btn btn-outline" onclick="closeReportModal()">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
function reportUser(userId) {
    document.getElementById('reportUserId').value = userId;
    document.getElementById('reportServiceId').value = '';
    document.getElementById('reportPostId').value = '';
    document.getElementById('reportModal').style.display = 'flex';
}
function reportService(serviceId, userId) {
    document.getElementById('reportUserId').value = userId;
    document.getElementById('reportServiceId').value = serviceId;
    document.getElementById('reportPostId').value = '';
    document.getElementById('reportModal').style.display = 'flex';
}
function reportPost(postId, userId) {
    document.getElementById('reportUserId').value = userId;
    document.getElementById('reportServiceId').value = '';
    document.getElementById('reportPostId').value = postId;
    document.getElementById('reportModal').style.display = 'flex';
}
function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
}
</script>