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
        'pro_is_done',
        'user_is_doner',
        'user_id' 
    ];

    protected $casts = [
        'category_id' => 'integer',
        'pro_is_done' => 'boolean',
        'customer_is_done' => 'boolean',
    ];



    public function category()
    {
        return $this->belongsTo(AdCategory::class, 'category_id', 'id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

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

    public static function getAdsInvolvedByUser(int $userId)
    {
        return self::query()
            ->with(['pictures:id,ad_id,path,type', 'adOffer', 'adChat', 'user.userStat'])
            ->where(function ($query) use ($userId) {
                $query->whereHas('adChat', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->orWhereHas('adOffer', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            })

            ->whereDoesntHave('adOffer', function ($query) use ($userId) {
                $query->where('is_paid', true)
                    ->where('user_id', '!=', $userId);
            })
            ->get();
    }
}
