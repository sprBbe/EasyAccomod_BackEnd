<?php

namespace App\Http\Controllers\AdminAPI;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Http\Resources\Comment as CommentResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $cmts = Comment::orderBy('created_at', 'desc')->get();
        return CommentResource::collection($cmts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return CommentResource|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cmt = Comment::find($id);
        $request->validate([
            'status' => 'required|in:' . implode(',', array(0, 1)),
        ]);
        $cmt->status = $request->status;
        $cmt->save();
        $noti = new Notification();
        $noti->content = "Bình luận của bạn đã được thay đổi trạng thái";
        $noti->id_to = $cmt->id_from;
        $noti->save();
        return new CommentResource($cmt);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cmt = Comment::find($id);
        $cmt->delete();
        return response()->json(null, 204);
    }
}
