<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Add extends Model
{
    use HasFactory;

    protected $table = 'adds';
    protected $fillable = [
        'name', 
        'description',
        'due_date',
        'location',
        'is_done',
        'user_id' 
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function adPicture()
    {
        return $this->hasMany(AdPicture::class, 'ad_id', 'id');
    }

    public function adOffer()
    {
        return $this->hasMany(AdOffer::class, 'ad_id', 'id');
    }

    public function adChat()
    {
        return $this->hasMany(AdChat::class, 'ad_id', 'id');
    }

}
