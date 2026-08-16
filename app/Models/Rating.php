<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'ratings';
    public $timestamps = false;
    protected $fillable = ['request_id', 'rater_id', 'rated_user_id', 'score', 'review'];
    public function rater() { return $this->belongsTo(User::class, 'rater_id'); }
}