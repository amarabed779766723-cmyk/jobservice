<div id="storyViewerModal">
    <div class="story-progress-bars" id="progressBars"></div>
    
    <div class="story-viewer-header">
        <img id="storyUserAvatar" src="" alt="">
        <div class="info">
            <div id="storyUserName"></div>
            <div class="time" id="storyTime"></div>
        </div>
        <button class="story-viewer-close" onclick="closeStoryViewer()">✕</button>
    </div>

    <div class="story-viewer-content" id="storyContent">
        <button class="story-nav-btn left" onclick="prevStory()" style="display:none;">‹</button>
        <button class="story-nav-btn right" onclick="nextStory()" style="display:none;">›</button>
    </div>

    <div class="story-views-info" id="storyViewsInfo" onclick="showStoryViewsModal()" style="display:none;">
        👁️ <span id="storyViewsCount">0</span> مشاهدة
    </div>
</div>

{{-- نافذة المشاهدين --}}
<div id="storyViewsModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.8); z-index:99999; flex-direction:column; align-items:center; justify-content:flex-end;">
    <div style="background:#1a1a1a; width:100%; max-width:500px; border-radius:20px 20px 0 0; padding:1.5rem; max-height:70vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; color:white;">
            <h3 style="margin:0; font-size:1.1rem;">👁️ المشاهدون (<span id="viewsModalCount">0</span>)</h3>
            <button onclick="closeStoryViewsModal()" style="background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;">✕</button>
        </div>
        <div id="viewsModalList" style="color:white;"></div>
    </div>
</div>