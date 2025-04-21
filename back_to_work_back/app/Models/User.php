<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ad()
    {
        return $this->hasMany(Ad::class, 'user_id', 'id');
    }
    
    public function adOffer()
    {
        return $this->hasMany(AdOffer::class, 'user_id', 'id');
    }

    public function adPicture()
    {
        return $this->hasMany(AdPicture::class, 'user_id', 'id');
    }

    public function adChat()
    {
        return $this->hasMany(AdChat::class, 'user_id', 'id');
    }

    public function userStat()
    {
        return $this->hasOne(UserStat::class, 'user_id', 'id');
    }
    public function categories()
    {
        return $this->belongsToMany(AdCategory::class, 'pro_categories', 'user_id', 'category_id');
    }

    public function provinces()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }
}
