<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class addCategory extends Model
{
    use HasFactory;

    protected $table = 'adds_categories';

    protected $fillable = [
        'name', 
        'description'
    ];
}
