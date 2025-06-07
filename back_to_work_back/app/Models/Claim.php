<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'ad_id',
        'bid_id',
        'user_stats_id',
        'images',
        'reason',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function bid()
    {
        return $this->belongsTo(AdOffer::class, 'bid_id');
    }

    public function userStat()
    {
        return $this->belongsTo(UserStat::class, 'user_stats_id');
    }
}
