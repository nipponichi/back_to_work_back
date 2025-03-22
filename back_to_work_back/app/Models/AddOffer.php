<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOffer extends Model
{
    use HasFactory;

    protected $table = 'adds_offers';

    protected $fillable = [
        'bid', 
        'description',
        'is_valid',
        'ad_id',
        'user_id' 
    ];

    public function ad()
    {
        return $this->belongsTo(Add::class, 'add_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
