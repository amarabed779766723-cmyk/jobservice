<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;  // <-- علقناها

class User extends Authenticatable implements MustVerifyEmail
{
    // use HasFactory;  // <-- علقناها

    protected $table = 'users';
    
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'user_type',
        'avatar', 'cover', 'bio', 'role', 'wallet_balance',
        'status', 'settings', 'city', 'website',
        'latitude', 'longitude', 'address', 'is_active', 'email_verified_at'
    ];
    
    protected $hidden = ['password', 'remember_token'];
    
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];
    
    // ========== العلاقات ==========
    
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
    
    public function userPackages()
    {
        return $this->hasMany(UserPackage::class);
    }

    // ✅ العلاقة مع التقييمات (المطلوبة لـ home.blade.php)
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'rated_user_id');
    }

    // ✅ العلاقة مع الخدمات
    public function services()
    {
        return $this->hasMany(Service::class, 'provider_id');
    }

    // ✅ العلاقة مع الطلبات
    public function requests()
    {
        return $this->hasMany(ServiceRequest::class, 'user_id');
    }

    // ✅ العلاقة مع المنشورات
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // ✅ العلاقة مع التعليقات
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // ✅ العلاقة مع الإعجابات
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // ✅ العلاقة مع الإشعارات
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ✅ العلاقة مع القصص
    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    // ✅ العلاقة مع المتابعين
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }

    // ✅ العلاقة مع المتابَعات
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }

    // ✅ العلاقة مع المحادثات
    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_participants');
    }

    // ========== دوال مساعدة ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
    
    public function scopeSuspended($query)
    {
        return $query->where('is_active', 0);
    }
    
    public function isActive()
    {
        return $this->is_active == 1;
    }

    public function isProvider()
    {
        return $this->user_type === 'provider';
    }

    public function isClient()
    {
        return $this->user_type === 'client';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // ✅ حساب متوسط التقييم
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('score') ?? 0;
    }

    // ✅ عدد التقييمات
    public function getRatingsCountAttribute()
    {
        return $this->ratings()->count();
    }

    // ✅ صورة الملف الشخصي
    public function getAvatarUrlAttribute()
    {
        return $this->avatar 
            ? asset('uploads/avatars/' . $this->avatar) 
            : asset('uploads/avatars/default-avatar.png');
    }

    // ✅ التحقق من المتابعة
    public function isFollowing($userId)
    {
        return $this->following()->where('following_id', $userId)->exists();
    }

    // ✅ الباقة النشطة
    public function getCurrentPackageAttribute()
    {
        $userPackage = $this->userPackages()
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->with('package')
            ->first();
            
        return $userPackage ? $userPackage->package : null;
    }
}