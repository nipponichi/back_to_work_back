<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdCategory extends Model
{
    use HasFactory;

    protected $table = 'ads_categories';

    protected $fillable = [
        'category', 
        'description'
    ];

    public function professionals()
    {
        return $this->belongsToMany(User::class, 'pro_categories', 'category_id', 'user_id');
    }
}
