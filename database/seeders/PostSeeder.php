<?php

namespace Database\Seeders;

use App\Models\Post;
use Carbon\Carbon;
use Faker\Factory as Faker;
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
        $faker = Faker::create();

        for ($i = 0; $i < 2; $i++) {
            $posts[] = [
                [
                    'title' => $faker->realText(12, 2),
                    'info_detail' => $faker->realText(125, 2),
                    'detail_address' => $faker->streetAddress,
                    'id_ward' => rand(0, 1),
                    'with_owner' => rand(0, 1),
                    'restroom' => rand(0, 1),
                    'kitchen' => rand(0, 1),
                    'water_heater' => rand(0, 1),
                    'air_conditioner' => rand(0, 1),
                    'balcony' => rand(0, 1),
                    'id_room_type' => rand(1, 4),
                    'square' => rand(15, 90),
                    'price' => rand(1, 55) * 1000000,
                    'electricity_price' => 1000 * rand(11, 15),
                    'water_price' => 100 * rand(25, 50),
                    'coordinates' => '',
                    'id_owner' => rand(2, 3),
                    'time_expire' => Carbon::now()->addYears(),
                    'views' => rand(100, 1000),
                    'status' => 1,
                ],
            ];
            foreach ($posts as $post) {
                Post::create($post);
            }
        }
    }
}
