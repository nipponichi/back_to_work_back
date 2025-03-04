<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';
    protected $fillable = [
        'name', 
        'email', 
    ];

    public $timestamps = false;

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id', 'id');
    }

    public function classroom()
    {
        return $this->hasOne(Classroom::class, 'teacher_id', 'id');
    }
}
