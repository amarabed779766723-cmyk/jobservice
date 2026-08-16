<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $table = 'service_requests';
    public $timestamps = false;
    protected $fillable = ['user_id', 'title', 'description', 'budget', 'status'];
    public function user() { return $this->belongsTo(User::class); }
}