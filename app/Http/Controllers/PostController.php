<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Notification;

class PostController extends Controller
{
    public function create()
    {
        return view('post-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'video' => 'nullable|mimes:mp4,webm,ogg|max:51200',
            'file_attachment' => 'nullable|file|max:20480',
        ]);

        $post = new Post();
        $post->user_id = Auth::id();
        $post->content = $request->content;

        if ($request->filled('image_crop')) {
            $imageData = $request->image_crop;
            $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $filename = 'post_' . time() . '_' . rand(1000,9999) . '.jpg';
            file_put_contents(public_path('uploads/posts/' . $filename), base64_decode($imageData));
            $post->image = $filename;
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'post_' . time() . '_' . rand(1000,9999) . '.' . $file->extension();
            $file->move(public_path('uploads/posts'), $filename);
            $post->image = $filename;
        }

        if ($request->filled('trimmed_video')) {
            $videoData = $request->trimmed_video;
            preg_match('/^data:video\/(\w+);base64,/', $videoData, $matches);
            $ext = $matches[1] ?? 'mp4';
            $videoData = preg_replace('/^data:video\/\w+;base64,/', '', $videoData);
            $videoData = str_replace(' ', '+', $videoData);
            $filename = 'vid_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            file_put_contents(public_path('uploads/videos/' . $filename), base64_decode($videoData));
            $post->video = $filename;
        }
        elseif ($request->hasFile('video')) {
            $file = $request->file('video');
            $filename = 'vid_' . time() . '_' . rand(1000,9999) . '.' . $file->extension();
            $file->move(public_path('uploads/videos'), $filename);
            $post->video = $filename;
        }

        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $filename = 'file_' . time() . '_' . rand(1000,9999) . '.' . $file->extension();
            $file->move(public_path('uploads/files'), $filename);
            $post->file_attachment = $filename;
            $post->file_name = $file->getClientOriginalName();
        }

        $post->save();

        ActivityLogController::log(Auth::id(), 'create_post', 'إنشاء منشور جديد');
        ActivityLogController::notifyAdmin(Auth::user()->name . ' نشر منشور جديد - يحتاج مراجعة', route('admin.posts'));

        return redirect()->route('home')->with('success', 'تم نشر المنشور!');
    }

    // ✅ دالة تحويل الهاشتاقات - تدعم العربية
    public static function convertHashtags($text)
    {
        if (!$text) return '';
        return preg_replace(
            '/#([\p{Arabic}a-zA-Z0-9_]+)/u',
            '<a href="/hashtag/$1" style="color:#2563EB; text-decoration:none; font-weight:600;">#$1</a>',
            $text
        );
    }

    public function show($id)
    {
        $post = Post::with('user')->withCount(['likes', 'comments'])->findOrFail($id);
        
        
        
        $comments = Comment::where('post_id', $id)
            ->whereNull('parent_id')
            ->with('user')
            ->with('replies.user')
            ->latest()
            ->get();
        return view('post-detail', compact('post', 'comments'));
    }

    public function toggleLike(Request $request, $id)
    {
        $user = Auth::user();
        $post = Post::findOrFail($id);
        $existing = Like::where('user_id', $user->id)->where('post_id', $id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            Like::create(['user_id' => $user->id, 'post_id' => $id]);
            if ($post->user_id != $user->id) {
                Notification::create([
                    'user_id' => $post->user_id, 'sender_id' => $user->id,
                    'type' => 'like', 'message' => $user->name . ' أعجب بمنشورك.',
                    'link' => route('post.show', $post->id)
                ]);
            }
        }
        return response()->json(['success' => true, 'likes_count' => Like::where('post_id', $id)->count()]);
    }

    public function addComment(Request $request, $id)
    {
        $request->validate(['comment_text' => 'required|string']);
        $user = Auth::user();
        $post = Post::findOrFail($id);

        $comment = Comment::create([
            'user_id' => $user->id,
            'post_id' => $id,
            'comment_text' => $request->comment_text,
            'parent_id' => $request->parent_id
        ]);

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ?? 'default-avatar.png'
        ];

        if ($post->user_id != $user->id) {
            Notification::create([
                'user_id' => $post->user_id, 'sender_id' => $user->id,
                'type' => 'comment', 'message' => $user->name . ' علّق على منشورك.',
                'link' => route('post.show', $post->id)
            ]);
        }
        if ($request->parent_id) {
            $parent = Comment::find($request->parent_id);
            if ($parent && $parent->user_id != $user->id) {
                Notification::create([
                    'user_id' => $parent->user_id, 'sender_id' => $user->id,
                    'type' => 'reply', 'message' => $user->name . ' رد على تعليقك.',
                    'link' => route('post.show', $post->id)
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'comments_count' => Comment::where('post_id', $id)->count(),
            'comment' => [
                'id' => $comment->id,
                'comment_text' => $comment->comment_text,
                'parent_id' => $comment->parent_id,
                'created_at' => $comment->created_at,
                'user' => $userData
            ]
        ]);
    }

    public function delete($id)
    {
        $post = Post::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        if ($post->image && file_exists(public_path('uploads/posts/' . $post->image))) {
            unlink(public_path('uploads/posts/' . $post->image));
        }
        if ($post->video && file_exists(public_path('uploads/videos/' . $post->video))) {
            unlink(public_path('uploads/videos/' . $post->video));
        }
        if ($post->file_attachment && file_exists(public_path('uploads/files/' . $post->file_attachment))) {
            unlink(public_path('uploads/files/' . $post->file_attachment));
        }
        
        $post->delete();

        ActivityLogController::log(Auth::id(), 'delete_post', 'حذف منشور');
        
        return back()->with('success', 'تم حذف المنشور!');
    }
}