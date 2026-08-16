<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $fillable = [
        'service_id', 'client_id', 'provider_id', 
        'booking_date', 'booking_time', 'notes', 
        'status', 'provider_reply', 'suggested_date', 'suggested_time'
    ];
    
    public function service() { return $this->belongsTo(Service::class); }
    public function client() { return $this->belongsTo(User::class, 'client_id'); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
}