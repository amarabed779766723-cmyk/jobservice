<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Post;
use App\Models\User;

class SearchController extends Controller
{
    public function index(Request $request)
{
    // ✅ جلب كل الهاشتاقات
    if ($request->has('all_hashtags')) {
        $posts = Post::where('content', 'LIKE', '%#%')->where('is_approved', 1)->pluck('content');
        $hashtags = [];
        
        foreach ($posts as $content) {
            preg_match_all('/#([\p{Arabic}a-zA-Z0-9_]+)/u', $content, $matches);
            foreach ($matches[1] as $tag) {
                $hashtags[$tag] = $tag;
            }
        }
        
        return response()->json(['hashtags' => array_values($hashtags)]);
    }
    
    $q = $request->get('q', '');
    $hashtag = $request->get('hashtag', '');
    
    if ($hashtag) {
        $posts = Post::where('content', 'LIKE', "%#{$hashtag}%")->where('is_approved', 1)->with('user')->latest()->take(10)->get();
        $services = Service::where('description', 'LIKE', "%#{$hashtag}%")->with('provider')->latest()->take(5)->get();
        $requests = \App\Models\ServiceRequest::where('description', 'LIKE', "%#{$hashtag}%")->where('status', 'open')->with('user')->latest()->take(5)->get();
        
        return response()->json([
            'hashtag' => $hashtag,
            'posts' => $posts,
            'services' => $services,
            'requests' => $requests
        ]);
    }
    
    if (strlen($q) < 2) {
        return response()->json(['services' => [], 'posts' => [], 'users' => []]);
    }
    
    return response()->json([
        'services' => Service::where('title', 'like', "%{$q}%")->take(4)->get(),
        'posts' => Post::where('content', 'like', "%{$q}%")->take(4)->get(),
        'users' => User::where('name', 'like', "%{$q}%")->take(4)->get()
    ]);
}
    
    // ✅ دالة جديدة - عرض صفحة الهاشتاق
    public function hashtagPosts($tag)
    {
        $posts = Post::where('content', 'LIKE', "%#{$tag}%")
            ->where('is_approved', 1)
            ->with('user')
            ->latest()
            ->paginate(12);
        
        return view('hashtag-posts', compact('posts', 'tag'));
    }
}