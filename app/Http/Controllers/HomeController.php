<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // ===== المنشورات =====
        $posts = Post::with(['user', 'likes', 'comments'])
            ->where('is_approved', 1)
            ->latest()
            ->take(10)
            ->get();

        // ===== الخدمات =====
        $services = Service::with(['provider'])
            ->latest()
            ->take(6)
            ->get();

        // ===== الطلبات المفتوحة =====
        $latestRequests = ServiceRequest::with(['user'])
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        // ===== القصص/الإعلانات =====
        $stories = Story::with(['user'])
            ->where('status', 'approved')
            ->latest()
            ->take(10)
            ->get();

        // ===== الخدمات القريبة (معلقة مؤقتاً) =====
        $nearbyServices = collect();

        return view('home', compact('posts', 'services', 'latestRequests', 'stories', 'nearbyServices'));
    }

    public function providers()
{
    $providers = \App\Models\User::where('user_type', 'provider')
        ->withCount(['ratings as average_rating' => function($q) {
            $q->select(\Illuminate\Support\Facades\DB::raw('coalesce(avg(score), 0)'));
        }])
        ->withCount('ratings')
        ->having('ratings_count', '>=', 5)
        ->having('average_rating', '>=', 4)
        ->orderByDesc('average_rating')
        ->paginate(12);
    
    return view('providers.index', compact('providers'));
}
}