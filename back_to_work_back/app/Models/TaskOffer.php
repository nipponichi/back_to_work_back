<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskOffer extends Model
{
    use HasFactory;

    protected $table = 'task_offers';

    protected $fillable = [
        'bid', 
        'description',
        'is_valid',
        'task_id',
        'user_id' 
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
