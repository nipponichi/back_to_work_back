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

    /*
    public function adPicture()
    {
        return $this->hasMany(AddPicture::class, 'ad_id', 'id');
    }
    */

    public function pictures()
{
    return $this->hasMany(AddPicture::class, 'add_id');
}

    public function addOffer()
    {
        return $this->hasMany(AddOffer::class, 'add_id', 'id');
    }

    public function addChat()
    {
        return $this->hasMany(AddChat::class, 'add_id', 'id');
    }

}
