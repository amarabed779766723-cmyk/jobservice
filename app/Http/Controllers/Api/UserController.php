<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
{
    $query = User::with(['services'])
        ->withCount(['ratings as average_rating' => function($q) {
            $q->select(DB::raw('coalesce(avg(score), 0)'));
        }])
        ->withCount('ratings');
    
    if ($request->has('type')) {
        $query->where('user_type', $request->type);
    }
    
    // فلتر: 5 تقييمات أو أكثر + متوسط 4 نجوم أو أكثر
    if ($request->has('sort') && $request->sort === 'rating') {
        $query->having('ratings_count', '>=', 5)
              ->having('average_rating', '>=', 4)
              ->orderByDesc('average_rating');
    } elseif ($request->has('sort') && $request->sort === 'services_count') {
        $query->orderByDesc('services_count');
    } else {
        $query->latest();
    }
    
    if ($request->has('limit')) {
        $query->limit((int) $request->limit);
    }
    
    $users = $query->get();
    $users->makeHidden(['password', 'remember_token', 'email_verified_at']);
    
    $total = User::where('user_type', $request->type ?? 'provider')
        ->whereHas('ratings', function($q) {
            $q->havingRaw('COUNT(*) >= 5');
        })
        ->count();

    return response()->json([
        'success' => true,
        'data' => $users,
        'total' => $total
    ]);
}

    public function show($id)
    {
        $user = User::with(['services', 'ratings'])
            ->withCount(['ratings as average_rating' => function($q) {
                $q->select(DB::raw('coalesce(avg(score), 0)'));
            }])
            ->find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'المستخدم غير موجود'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}