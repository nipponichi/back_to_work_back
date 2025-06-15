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

    protected $casts = [
        'is_paid' => 'boolean',
        'is_valid' => 'boolean',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ad_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
