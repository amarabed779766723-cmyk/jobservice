<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $table = 'stories';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'image',
        'is_video',
        'caption',
        'is_ad',
        'package_id',
        'status',
        'expires_at',
        'link',
        'ad_price'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function views()
    {
        return $this->hasMany(StoryView::class);
    }

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'story_views', 'story_id', 'user_id');
    }

    public function isViewedBy($userId)
    {
        return $this->views()->where('user_id', $userId)->exists();
    }
}