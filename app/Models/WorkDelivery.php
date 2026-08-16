<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkDelivery extends Model
{
    protected $table = 'work_deliveries';
    public $timestamps = false;
    protected $fillable = ['booking_id', 'offer_id', 'sender_id', 'receiver_id', 'title', 'description', 'file_attachment', 'file_name', 'status', 'client_confirmed', 'created_at'];
    
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver() { return $this->belongsTo(User::class, 'receiver_id'); }
    public function booking() { return $this->belongsTo(Booking::class); }
}