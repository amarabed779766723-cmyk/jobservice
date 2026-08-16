@extends('layouts.app')
@section('title', Str::limit($post->content ?? 'منشور', 30) . ' - Job Service')
@section('content')
<div style="max-width:700px;margin:0 auto;padding:1.5rem 1rem;">
    <a href="{{ route('home') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('Home') }}
    </a>

    <div class="card" style="margin-top:1rem;">
        <div class="card-header">
            <img src="{{ asset('uploads/avatars/' . ($post->user->avatar ?? 'default-avatar.png')) }}" class="card-avatar" alt="">
            <div>
                <div class="card-user">{{ $post->user->name }}</div>
                <div class="card-meta">{{ $post->created_at->format('Y/m/d H:i') }}</div>
            </div>
        </div>
        @if($post->content)<p>{!! nl2br(e($post->content)) !!}</p>@endif
        @if($post->image)<img src="{{ asset('uploads/posts/'.$post->image) }}" style="width:100%;border-radius:8px;margin:0.75rem 0;" alt="">@endif
        @if($post->video)<video controls style="width:100%;border-radius:8px;margin:0.75rem 0;"><source src="{{ asset('uploads/videos/'.$post->video) }}"></video>@endif
        @if($post->file_attachment)<div><a href="{{ asset('uploads/files/'.$post->file_attachment) }}" download>📎 {{ $post->file_name ?? __('Download File') }}</a></div>@endif
        @auth
            @if(auth()->id() != $post->user_id)
                <button onclick="reportPost({{ $post->id }}, {{ $post->user_id }})" style="background:none; border:none; cursor:pointer; font-size:0.9rem;" title="{{ __('Report') }}">🚩</button>
            @endif
        @endauth 
        <div class="card-footer">
            <span>❤️ {{ $post->likes_count }} {{ __('Like') }}</span>
            <span>💬 {{ $post->comments_count }} {{ __('Comment') }}</span>
        </div>
    </div>

    <h3 style="margin-top:1.5rem;">💬 {{ __('Comments') }} ({{ $post->comments_count }})</h3>
    <div id="commentsPreview-{{ $post->id }}" style="margin-bottom:1rem;">
        @foreach($comments as $comment)
            <div style="display:flex; gap:0.5rem; margin-bottom:0.75rem; align-items:flex-start;" id="comment-{{ $comment->id }}">
                <img src="{{ asset('uploads/avatars/' . ($comment->user->avatar ?? 'default-avatar.png')) }}" style="width:32px;height:32px;border-radius:50%;">
                <div style="flex:1;">
                    <div style="background:var(--bg); border-radius:12px; padding:0.5rem 0.75rem;">
                        <strong style="font-size:0.9rem;">{{ $comment->user->name }}</strong>
                        <span style="color:var(--text-secondary); font-size:0.85rem; display:block;">{{ $comment->comment_text }}</span>
                    </div>
                    <div style="margin-right: 1.5rem; margin-top: 0.25rem;">
                        @foreach($comment->replies as $reply)
                            <div style="display:flex; gap:0.5rem; margin-top:0.4rem; align-items:flex-start;">
                                <img src="{{ asset('uploads/avatars/' . ($reply->user->avatar ?? 'default-avatar.png')) }}" style="width:26px;height:26px;border-radius:50%;">
                                <div style="background:var(--bg); border-radius:12px; padding:0.4rem 0.6rem; flex:1;">
                                    <strong style="font-size:0.85rem;">{{ $reply->user->name }}</strong>
                                    <span style="color:var(--text-secondary); font-size:0.8rem; display:block;">{{ $reply->comment_text }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @auth
                        <button onclick="showReplyForm({{ $comment->id }})" style="background:none; border:none; color:var(--text-secondary); font-size:0.75rem; cursor:pointer; margin-top:0.25rem;">↩️ {{ __('Reply') }}</button>
                        <div id="replyForm-{{ $comment->id }}" style="display:none; margin-top:0.5rem; margin-right:2rem;">
                            <form onsubmit="submitComment(event, {{ $post->id }}, {{ $comment->id }})" style="display:flex; gap:0.5rem;">
                                <input type="text" name="comment_text" placeholder="{{ __('Write a reply...') }}" required
                                       style="flex:1; padding:0.4rem 0.6rem; border:1px solid var(--border); border-radius:2rem; font-family:'Tajawal',sans-serif; font-size:0.85rem;">
                                <button type="submit" class="btn btn-primary btn-sm">↩️</button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    @auth
        <form onsubmit="submitComment(event, {{ $post->id }}, null)" style="display:flex; gap:0.5rem; margin-top:1rem;">
            <input type="text" name="comment_text" placeholder="{{ __('Write a comment...') }}" required
                   style="flex:1; padding:0.6rem 1rem; border:1px solid var(--border); border-radius:2rem; font-family:'Tajawal',sans-serif;">
            <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
        </form>
    @else
        <p style="text-align:center; margin-top:1rem;"><a href="{{ route('login') }}">{{ __('Log in to comment') }}</a></p>
    @endauth
</div>


@endsection