<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    
    protected $fillable = [
        'user_id',
        'package_id',
        'amount',
        'type',
        'status',
        'sender_name',
        'sender_phone',
        'receipt_image',
        'admin_note',
        'confirmed_by',
        'confirmed_at',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}