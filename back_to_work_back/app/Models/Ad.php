<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $table = 'ads';
    protected $fillable = [
        'name', 
        'description',
        'category_id',
        'due_date',
        'location',
        'is_done',
        'user_id' 
    ];

    public function category()
    {
        return $this->belongsTo(adCategory::class, 'category_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /*
    public function adPicture()
    {
        return $this->hasMany(AdPicture::class, 'ad_id', 'id');
    }
    */

    public function pictures()
{
    return $this->hasMany(AdPicture::class, 'ad_id');
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
