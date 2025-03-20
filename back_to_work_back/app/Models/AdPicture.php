<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPicture extends Model
{
    use HasFactory;

    protected $table = 'ad_pictures';

    protected $fillable = [
        'picture', 
        'ad_id' 
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ad_id', 'id');
    }
}
