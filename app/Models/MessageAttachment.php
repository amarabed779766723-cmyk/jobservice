<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    protected $table = 'message_attachments';
    public $timestamps = false;
    protected $fillable = ['message_id', 'file_name', 'file_path', 'file_type', 'created_at'];
    
    public function message() { return $this->belongsTo(Message::class); }
}