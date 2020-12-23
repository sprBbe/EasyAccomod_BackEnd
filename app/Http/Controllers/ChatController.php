<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Resources\Message as MessageResource;

class ChatController extends Controller
{
    function getMessageReceived(Request $request, $id_to)
    {
        $user = $request->user();
        $mes = Message::where(['id_from', $user->id], ['id_to', $id_to])->orderBy('created_at', 'ASC')->take(50)->get();
        return response()->json([
            'message' => new MessageResource($mes),
        ]);
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
        $mes->id_to = $id_to;
        $mes->save();
        return response()->json("Thêm thành công", 201);
    }
}
