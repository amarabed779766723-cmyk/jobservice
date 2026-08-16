<?php

namespace App\Http\Controllers\Api;

use App\Models\Story;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoryController extends Controller
{
    // جلب القصص/الإعلانات
    public function index(Request $request)
    {
        $query = Story::with(['user']);

        if ($request->has('is_ad')) {
            $query->where('is_ad', $request->is_ad);
        }

        $stories = $query->where('status', 'approved')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stories
        ]);
    }
}