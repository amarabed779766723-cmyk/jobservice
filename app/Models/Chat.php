<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';
    public $timestamps = false;
    
    public function participants() { return $this->belongsToMany(User::class, 'chat_participants', 'chat_id', 'user_id'); }
    public function messages() { return $this->hasMany(Message::class, 'chat_id'); }
}