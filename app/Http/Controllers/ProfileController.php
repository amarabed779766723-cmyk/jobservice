<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Service;
use App\Models\Rating;
use App\Models\Follow;
use App\Models\Post;

class ProfileController extends Controller
{
    public function show($id = null)
    {
        $currentUser = Auth::user();
        $profileId = $id ?? ($currentUser->id ?? 0);
        $profileUser = User::findOrFail($profileId);
        $isLoggedIn = Auth::check();
        
        $myServices = collect();
        if ($profileUser->user_type === 'provider') {
            $myServices = Service::where('provider_id', $profileUser->id)->latest()->get();
        }
        
        $posts = Post::where('user_id', $profileUser->id)
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get();
        
        $followers = Follow::where('following_id', $profileUser->id)->with('follower')->latest()->get();
        $followersCount = $followers->count();
        
        $following = Follow::where('follower_id', $profileUser->id)->with('following')->latest()->get();
        $followingCount = $following->count();
        
        $ratings = Rating::where('rated_user_id', $profileUser->id)->with('rater')->latest()->get();
        $ratingsCount = $ratings->count();
        $avgRating = round(Rating::where('rated_user_id', $profileUser->id)->avg('score'), 1);
        
        $isFollowing = false;
        if ($isLoggedIn && $currentUser->id != $profileUser->id) {
            $isFollowing = Follow::where('follower_id', $currentUser->id)
                ->where('following_id', $profileUser->id)
                ->exists();
        }
        
        return view('profile', compact(
            'profileUser', 'isLoggedIn', 'currentUser',
            'myServices', 'posts',
            'followers', 'followersCount',
            'following', 'followingCount',
            'ratings', 'ratingsCount', 'avgRating',
            'isFollowing'
        ));
    }
    
    public function edit()
    {
        $currentUser = Auth::user();
        $isSuspended = ($currentUser->is_active == 0);
        return view('edit-profile', compact('currentUser', 'isSuspended'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $user->name = $request->name;
        $user->bio = $request->bio;
        $user->phone = $request->phone;
        $user->city = $request->city;
        $user->website = $request->website;
        
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->extension();
            $file->move(public_path('uploads/avatars'), $filename);
            $user->avatar = $filename;
        }
        
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = 'cover_' . $user->id . '_' . time() . '.' . $file->extension();
            $file->move(public_path('uploads/covers'), $filename);
            $user->cover = $filename;
        }
        
        if ($request->filled('avatar_crop')) {
            $imageData = $request->avatar_crop;
            $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $filename = 'avatar_' . $user->id . '_' . time() . '.jpg';
            file_put_contents(public_path('uploads/avatars/' . $filename), base64_decode($imageData));
            if ($user->avatar && $user->avatar !== 'default-avatar.png' && file_exists(public_path('uploads/avatars/' . $user->avatar))) {
                unlink(public_path('uploads/avatars/' . $user->avatar));
            }
            $user->avatar = $filename;
        }
        
        if ($request->filled('cover_crop')) {
            $imageData = $request->cover_crop;
            $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $filename = 'cover_' . $user->id . '_' . time() . '.jpg';
            file_put_contents(public_path('uploads/covers/' . $filename), base64_decode($imageData));
            if ($user->cover && $user->cover !== 'default-cover.jpg' && file_exists(public_path('uploads/covers/' . $user->cover))) {
                unlink(public_path('uploads/covers/' . $user->cover));
            }
            $user->cover = $filename;
        }
        
        $user->save();

        ActivityLogController::log(Auth::id(), 'update_profile', 'تحديث الملف الشخصي');
        
        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح!');
    }

    public function followers($id)
    {
        $profileUser = User::findOrFail($id);
        $currentUserId = Auth::id();
        
        $followers = Follow::where('following_id', $id)
            ->with('follower')
            ->paginate(20);
        
        $followers->getCollection()->transform(function ($follow) use ($currentUserId) {
            $follow->is_followed_by_me = Follow::where('follower_id', $currentUserId)
                ->where('following_id', $follow->follower->id)
                ->exists();
            return $follow;
        });
        
        return view('profile.followers', compact('profileUser', 'followers'));
    }

    public function following($id)
    {
        $profileUser = User::findOrFail($id);
        $currentUserId = Auth::id();
        
        $following = Follow::where('follower_id', $id)
            ->with('following')
            ->paginate(20);
        
        $following->getCollection()->transform(function ($follow) use ($currentUserId) {
            $follow->is_followed_by_me = true;
            return $follow;
        });
        
        return view('profile.following', compact('profileUser', 'following'));
    }

    public function suspend()
    {
        $user = Auth::user();
        $user->is_active = 0;
        $user->save();
        Auth::logout();
        return redirect('/login')->with('suspended', 'تم إيقاف حسابك مؤقتاً.');
    }

    public function activate(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->is_active = 1;
            $user->save();
            Auth::login($user);
            return redirect('/home')->with('success', '✅ تم تفعيل حسابك!');
        }
        return back()->withErrors(['email' => 'المستخدم غير موجود.']);
    }
}