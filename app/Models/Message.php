<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    public $timestamps = false;
    protected $fillable = ['chat_id', 'sender_id', 'message_text', 'image', 'file_attachment', 'file_name', 'reply_to', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];
    
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function chat() { return $this->belongsTo(Chat::class); }
    public function replyTo() { return $this->belongsTo(Message::class, 'reply_to'); }
}