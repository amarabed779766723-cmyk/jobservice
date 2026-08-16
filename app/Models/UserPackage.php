<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserPackage extends Model
{
    protected $table = 'user_packages';
    public $timestamps = false;
    
    protected $fillable = [
        'user_id', 
        'package_id', 
        'start_date', 
        'end_date', 
        'status',
        'price_paid',
        'created_at'
    ];
    
    public function user() { return $this->belongsTo(User::class); }
    public function package() { return $this->belongsTo(Package::class); }
}