<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskPicture extends Model
{
    use HasFactory;

    protected $table = 'task_pictures';

    protected $fillable = [
        'picture', 
        'task_id' 
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}
