<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = "messages";

    /**
     * Get sender of messages
     *
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'id_from');
    }

    /**
     * Get roomchat of message
     *
     */
    public function roomChat()
    {
        return $this->belongsTo(RoomChat::class, 'id_room_chat');
    }
}
