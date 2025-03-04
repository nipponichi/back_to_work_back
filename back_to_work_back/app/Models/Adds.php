<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adds extends Model
{
    use HasFactory;

    protected $table = 'adds';
    protected $fillable = [
        'name',
        'phone',
        'age',
        'category',
        'short_des',
    ];
    public $timestamps = false;

}
