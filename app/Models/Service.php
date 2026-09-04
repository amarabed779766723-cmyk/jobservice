<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    
    public $timestamps = false;
    
    protected $fillable = [
        'provider_id', 
        'title', 
        'description', 
        'price', 
        'duration', 
        'image',
        'latitude',
        'longitude',
        'address',
        'city',
    ];
    
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'request_id');
    }
    
    // ✅ دالة حساب المسافة بين نقطتين
    public static function distance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371;
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
    
    // ✅ جلب الخدمات القريبة
    public static function nearby($lat, $lng, $radius = 50)
    {
        return self::select('*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?)) 
                + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$lat, $lng, $lat]
            )
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->get();
    }
}