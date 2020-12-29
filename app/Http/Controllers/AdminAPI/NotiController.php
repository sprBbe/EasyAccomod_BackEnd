<?php

namespace App\Http\Controllers\AdminAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotiController extends Controller
{
    /**
     * Gửi thông báo cho người dùng
     *
     * @param Request $request
     * @param $id_to
     * @return \Illuminate\Http\JsonResponse
     */
    function sendNotification(Request $request,$id_to){
        $request->validate([
            'noti' => 'required|max:1000000'
        ]);
        //Admin vẫn có thể gửi thông báo cho chính mình
        $noti = new Notification();
        $noti->content = $request->noti;
        $noti->id_to = $id_to;
        $noti->save();
        return response()->json("Thêm thông báo thành công!", 201);
    }
}
