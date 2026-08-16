<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    public $timestamps = false; // ← هذا السطر يحل المشكلة
    protected $fillable = ['reporter_id', 'reported_user_id', 'service_id', 'reason', 'status'];
    
    public function reporter() { return $this->belongsTo(User::class, 'reporter_id'); }
    public function reported() { return $this->belongsTo(User::class, 'reported_user_id'); }
    public function service() { return $this->belongsTo(Service::class); }
}