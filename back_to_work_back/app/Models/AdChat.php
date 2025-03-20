<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class adChat extends Model
{
    use HasFactory;

    protected $table = 'ad_chats';

    protected $fillable = [
        'message', 
        'is_read',
        'ad_id',
        'sender_id',
        'receiver_id'
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ad_id', 'id');
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
