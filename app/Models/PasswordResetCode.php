<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetCode extends Model
{
    protected $table = 'password_reset_codes';
    
    public $timestamps = false;
    
    protected $fillable = [
        'email',
        'code',
        'created_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
}