<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStat extends Model
{
    use HasFactory;

    protected $table = 'user_stats';
    protected $fillable = [
        'rating',
        'review',
        'sender_id',
        'ad_id',
        'receiver_id',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ad_id', 'id');
    }
}
