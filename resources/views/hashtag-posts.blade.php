@extends('layouts.app')
@section('title', '#' . $tag . ' - Job Service')
@section('content')
<div class="hashtag-page">
    <a href="{{ route('home') }}" class="back-link">← رجوع للرئيسية</a>
    <h2 class="hashtag-title">#{{ $tag }}</h2>
    <p class="hashtag-count">{{ $posts->total() }} منشور</p>
    
    @foreach($posts as $post)
    <div class="hashtag-post-card">
        <div class="hashtag-post-header">
            <img src="{{ asset('uploads/avatars/' . ($post->user->avatar ?? 'default-avatar.png')) }}" 
                 class="hashtag-post-avatar" alt="{{ $post->user->name }}">
            <div>
                <strong class="hashtag-post-user">{{ $post->user->name }}</strong>
                <p class="hashtag-post-time">{{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>
        <p class="hashtag-post-content">{!! \App\Http\Controllers\PostController::convertHashtags($post->content) !!}</p>
        @if($post->image)
            <img src="{{ asset('uploads/posts/' . $post->image) }}" class="hashtag-post-image">
        @endif
        <a href="{{ route('post.show', $post->id) }}" class="hashtag-post-link">عرض المنشور</a>
    </div>
    @endforeach
    
    {{ $posts->links() }}
</div>
@endsection