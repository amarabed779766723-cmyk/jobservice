<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AdPackage extends Model
{
    protected $table = 'ad_packages';
    public $timestamps = false;
    protected $fillable = ['name', 'duration_days', 'price'];
}