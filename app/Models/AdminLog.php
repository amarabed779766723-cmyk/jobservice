<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $table = 'admin_logs';
    public $timestamps = false;
    protected $fillable = ['admin_id', 'action', 'target_type', 'target_id', 'details', 'created_at'];
}