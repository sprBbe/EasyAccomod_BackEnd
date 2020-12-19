<?php

namespace App\Http\Controllers\AdminAPI;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Resources\Report as ReportResource;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $rps = Report::orderBy('created_at', 'desc')->get();
        return ReportResource::collection($rps);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return ReportResource|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rp = Report::find($id);
        $request->validate([
            'status' => 'required|in:' . implode(',', array(0, 1)),
        ]);
        $rp->status = $request->status;
        $rp->save();
        return new ReportResource($rp);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rp = Report::find($id);
        $rp->delete();
        return response()->json(null, 204);
    }
}
