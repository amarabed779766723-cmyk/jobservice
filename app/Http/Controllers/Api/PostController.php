<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['user', 'likes', 'comments' => function($q) {
            $q->with('user')->with('replies.user')->whereNull('parent_id')->latest();
        }])
        ->where('is_approved', 1)
        ->latest()
        ->limit(10)
        ->get();

        $posts->transform(function ($post) {
            $post->likes_count = $post->likes->count();
            $post->comments_count = $post->comments->count();
            
            // ✅ تحويل الهاشتاق إلى رابط
            $post->content = preg_replace(
                '/#([\p{Arabic}a-zA-Z0-9_]+)/u',
                '<a href="/hashtag/$1" style="color:#2563EB; text-decoration:none; font-weight:600;">#$1</a>',
                $post->content
            );
            
            return $post;
        });

        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
    }

    public function show($id)
    {
        $post = Post::with(['user', 'likes', 'comments.user'])->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'المنشور غير موجود'
            ], 404);
        }

        $post->likes_count = $post->likes->count();
        $post->comments_count = $post->comments->count();
        
       

        return response()->json([
            'success' => true,
            'data' => $post
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'is_approved' => 1,
        ]);

        return response()->json([
            'success' => true,
            'data' => $post->load('user')
        ]);
    }

    public function toggleLike($id)
    {
        $post = Post::findOrFail($id);
        $user = auth()->user();

        if ($post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->where('user_id', $user->id)->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $post->likes()->count()
        ]);
    }

    public function addComment(Request $request, $id)
    {
        $request->validate(['comment_text' => 'required|string']);

        $post = Post::findOrFail($id);
        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'comment_text' => $request->comment_text,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $comment->load('user')
        ]);
    }

    public function destroy($id)
    {
        $post = Post::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $post->delete();

        return response()->json(['success' => true]);
    }
}