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
        $q = $request->get('q', '');
        if (strlen($q) < 2) {
            return response()->json(['services' => [], 'posts' => [], 'users' => []]);
        }
        return response()->json([
            'services' => Service::where('title', 'like', "%{$q}%")->take(4)->get(),
            'posts' => Post::where('content', 'like', "%{$q}%")->take(4)->get(),
            'users' => User::where('name', 'like', "%{$q}%")->take(4)->get()
        ]);
    }
}