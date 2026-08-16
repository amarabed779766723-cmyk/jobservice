<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    
    public $timestamps = false;
    
    protected $fillable = ['provider_id', 'title', 'description', 'price', 'duration', 'image'];
    
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'request_id');
    }
}