<?php

namespace Database\Seeders;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posts = [
            [
                'title' => 'Cho thuê phòng trọ giá rẻ Cầu Giấy',
                'info_detail' => '<3 Phòng trọ chỉ 2 triệu đồng/Tháng phù hợp 2 người ở',
                'detail_address' => '55 Xuân Thủy',
                'id_ward' => 1,
                'with_owner' => 1,
                'id_room_type' => 1,
                'square' => rand(15, 40),
                'price' => rand(1, 10) * 1000000,
                'coordinates' => '@21.0229425,105.7976696,15z',
                'id_owner' => 2,
                'time_expire' => Carbon::now()->addYears(100),
                'views' => rand(100, 1000),
                'status' => 1,
            ],
        ];
        for ($i = 0; $i <= 100; $i++) {
            foreach ($posts as $post) {
                Post::create($post);
            }
        }

    }
}
