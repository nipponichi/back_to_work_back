<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddPicture extends Model
{
    use HasFactory;

    protected $table = 'adds_pictures';

    protected $fillable = [
        'path',
        'type',
        'add_id',
    ];

    public function add()
    {
        return $this->belongsTo(Add::class, 'add_id');
    }
}