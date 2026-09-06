<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletSetting extends Model
{
    protected $table = 'wallet_settings';
    
    public $timestamps = false;
    
    protected $fillable = [
        'wallet_name',
        'wallet_number',
        'wallet_owner',
        'is_active',
    ];
    
    // ✅ تغيير: ترجع كل المحافظ النشطة
    public static function getActive()
    {
        return self::where('is_active', 1)->get();
    }
    
    // ✅ إضافة: ترجع أول محفظة (للتوافق مع الكود القديم)
    public static function getFirstActive()
    {
        return self::where('is_active', 1)->first();
    }
}