<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Follow;
use App\Models\Notification;

class FollowController extends Controller
{
    public function toggle($userId)
    {
        $user = Auth::user();
        if ($user->id == $userId) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'لا يمكن متابعة نفسك'], 400);
            }
            return back();
        }

        $existing = Follow::where('follower_id', $user->id)->where('following_id', $userId)->first();
        if ($existing) {
            $existing->delete();
            $isFollowing = false;
        } else {
            Follow::create(['follower_id' => $user->id, 'following_id' => $userId]);
            
            // إنشاء إشعار مع رابط لرد المتابعة
            Notification::create([
                'user_id' => $userId,
                'sender_id' => $user->id,
                'type' => 'follow',
                'message' => $user->name . ' بدأ متابعتك.',
                'link' => route('profile', $user->id) . '?action=follow_back&user=' . $user->id
            ]);
            
            $isFollowing = true;
        }

        if (request()->ajax() || request()->wantsJson()) {
            $followersCount = Follow::where('following_id', $userId)->count();
            return response()->json([
                'success' => true,
                'isFollowing' => $isFollowing,
                'followersCount' => $followersCount
            ]);
        }

        return back();
    }
}