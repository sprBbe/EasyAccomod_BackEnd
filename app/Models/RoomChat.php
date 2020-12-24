<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomChat extends Model
{
    use HasFactory;

    protected $table = "room_chats";

    /**
     * Get all messages received of users.
     */
    public function messages()
    {
        return $this->hasMany('App\Models\Message', 'id_room_chat');
    }

    /**
     * Check if person in room chat.
     */
    public function hasUser($id_user)
    {
        return ($this->id_user1 == $id_user) || ($this->id_user2 == $id_user);
    }
}
