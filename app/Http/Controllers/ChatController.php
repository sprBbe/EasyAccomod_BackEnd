<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Resources\User as UserResource;
use App\Models\Message;
use App\Models\RoomChat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\Message as MessageResource;

class ChatController extends Controller
{
    function getAllAdmin(){
        $posts = User::where('id_role',2)->orderBy('created_at','desc')->get();
        return UserResource::collection($posts);
    }
    function getMessageReceived(Request $request, $id_to)
    {
        $user = $request->user();
        $room_chat = RoomChat::where([['id_user1', $user->id], ['id_user2', $id_to]])->orWhere([['id_user2', $user->id], ['id_user1', $id_to]])->first();
        if (isset($room_chat)) {
            $mes = $room_chat->messages;
            return response()->json([
                'message' => MessageResource::collection($mes),
            ]);
        }
        else {
            return response()->json(['No message in this room chat']);
        }
    }

    function postSendMessage(Request $request, $id_to)
    {
        $user = $request->user();
        $request->validate([
            'cnt' => 'required|max:10000',
        ]);
        $mes = new Message();
        $mes->content = $request->cnt;
        $mes->id_from = $user->id;
        $room_chat = RoomChat::where([['id_user1', $user->id], ['id_user2', $id_to]])->orWhere([['id_user2', $user->id], ['id_user1', $id_to]])->first();
        if (isset($room_chat)) $mes->id_room_chat = $room_chat->id;
        else {
            $room_chat = new RoomChat();
            $room_chat->id_user1 = $user->id;
            $room_chat->id_user2 = $id_to;
            $room_chat->save();
            $mes->id_room_chat = $room_chat->id;
        }
        $mes->save();
        broadcast(new MessageSent($user,$mes))->toOthers();
        return response()->json([
            'message' => new MessageResource($mes),
        ], 201);
    }
}
