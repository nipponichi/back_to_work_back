<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class addChat extends Model
{
    use HasFactory;

    protected $table = 'add_chats';

    protected $fillable = [
        'message', 
        'is_read',
        'add_id',
        'sender_id',
        'receiver_id'
    ];

    public function addChat()
    {
        return $this->belongsTo(Add::class, 'add_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}
