<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offers';
    public $timestamps = false;
    protected $fillable = ['request_id', 'provider_id', 'price', 'message', 'status'];
    
    public function request() { return $this->belongsTo(ServiceRequest::class, 'request_id'); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
}