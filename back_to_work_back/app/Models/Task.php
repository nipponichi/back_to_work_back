<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    protected $fillable = [
        'name', 
        'description',
        'due_date',
        'location',
        'is_done',
        'user_id' 
    ];

    public function task()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function taskPictures()
    {
        return $this->hasMany(TaskPicture::class, 'task_id', 'id');
    }

    public function taskOffers()
    {
        return $this->hasMany(TaskOffer::class, 'task_id', 'id');
    }

    public function taskChats()
    {
        return $this->hasMany(TaskChat::class, 'task_id', 'id');
    }

}
