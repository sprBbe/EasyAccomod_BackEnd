<?php

namespace App\Http\Controllers\AdminAPI;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post as PostResource;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    function getStatistic()
    {
        $total_users = User::all()->count();
        $most_views_in_month = Post::where([['created_at', '>', Carbon::now()->subDays(30)], ['status', '1']])->orderBy('views', 'desc')->first();
        $top_10_districts_in_month = DB::select(
            "SELECT d.id AS id_district,d.name as district, pr.name as province, COUNT(d.id) AS number_of_posts
            FROM posts p JOIN wards w ON w.id=p.id_ward
            JOIN districts d ON w.id_district=d.id
            JOIN provinces pr ON d.id_province=pr.id
            WHERE p.status = 1 AND p.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY d.id
            ORDER BY number_of_posts DESC
            LIMIT 10"
        );
        $top_10_wards_in_month = DB::select(
            "SELECT w.id AS id_ward,w.name as ward,d.name as district, pr.name as province, COUNT(w.id) AS number_of_posts
            FROM posts p JOIN wards w ON w.id=p.id_ward
            JOIN districts d ON w.id_district=d.id
            JOIN provinces pr ON d.id_province=pr.id
            WHERE p.status = 1 AND p.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY w.id
            ORDER BY number_of_posts DESC
            LIMIT 10"
        );
        return response()->json([
            'total_users' => $total_users,
            'most_views_in_month' => new PostResource($most_views_in_month),
            'top_10_districts_in_month' => $top_10_districts_in_month,
            'top_10_wards_in_month' => $top_10_wards_in_month,
        ]);
    }
}
