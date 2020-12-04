<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;

class PagesController extends Controller
{
    function home()
    {
        $nine_post_on_top = Post::where('time_expire', '>', Carbon::now())->orderby('views', 'desc')->take(9)->get();
        $six_post_lastest = Post::orderby('created_at', 'desc')->take(9)->get();
        return response()->json([
                'nine_post_on_top' => $nine_post_on_top,
                'six_post_lastest' => $six_post_lastest,
            ]
        );
    }
    function get_new_post(){

    }
}
