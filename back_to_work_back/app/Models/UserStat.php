<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStat extends Model
{
    use HasFactory;

    protected $table = 'user_stats';
    protected $fillable = [
        'quality_price', 
        'customer_care',
        'timing',
        'review',
        'user_id',
        'add_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function ad()
    {
        return $this->belongsTo(Ad::class, 'add_id', 'id');
    }
}
