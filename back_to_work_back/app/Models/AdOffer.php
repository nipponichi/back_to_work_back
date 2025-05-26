<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdOffer extends Model
{
    use HasFactory;

    protected $table = 'ads_offers';

    protected $fillable = [
        'bid', 
        'description',
        'is_valid',
        'is_paid',
        'ad_id',
        'user_id' 
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ad_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function userStat()
    {
        return $this->hasManyThrough(
            UserStat::class, // Modelo destino (UserStat)
            User::class,     // Modelo intermedio (User)
            'id',           // FK en User (user.id)
            'user_id',      // FK en UserStat (userstats.user_id)
            'user_id',      // FK en AdOffer (adoffers.user_id)
            'id'            // PK en User (user.id)
        );
    }
}
