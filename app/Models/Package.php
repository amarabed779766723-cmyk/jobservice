<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'packages';
    public $timestamps = false;
    protected $fillable = ['name', 'duration_days', 'price', 'max_services', 'max_requests', 'max_ads', 'badge', 'is_featured'];
}