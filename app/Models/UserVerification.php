<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    protected $table = 'user_verifications';
    public $timestamps = false;
    
    protected $fillable = [
        'user_id',
        'verification_type',
        'document_image',
        'status',
        'admin_note',
        'verified_by',
        'verified_at',
        'created_at',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // ✅ التحقق من حالة التوثيق
    public static function isVerified($userId)
    {
        return self::where('user_id', $userId)
            ->where('status', 'approved')
            ->exists();
    }
}