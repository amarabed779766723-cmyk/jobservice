<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BannedNote extends Model
{
    protected $table = 'banned_notes';
    public $timestamps = false;
    protected $fillable = ['user_id', 'message', 'is_read'];
    public function user() { return $this->belongsTo(User::class); }
}