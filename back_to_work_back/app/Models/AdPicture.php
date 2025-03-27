<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPicture extends Model
{
    use HasFactory;

    protected $table = 'ads_pictures';

    protected $fillable = [
        'path',
        'type',
        'ad_id',
    ];

    public function add()
    {
        return $this->belongsTo(Ad::class, 'ad_id');
    }
}