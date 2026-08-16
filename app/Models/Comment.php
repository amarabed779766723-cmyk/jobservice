<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = false;
    protected $table = 'comments';
    protected $fillable = ['user_id', 'post_id', 'comment_text', 'parent_id', 'likes_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}