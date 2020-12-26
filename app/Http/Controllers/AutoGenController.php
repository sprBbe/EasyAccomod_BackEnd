<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Img;
use App\Models\NearPlace;
use App\Models\Post;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Http\Request;

class AutoGenController extends Controller
{
    function autoGen()
    {
        $faker = Faker::create();
        $post = new Post();
        $post->title = $faker->text(50);
        $post->info_detail = $faker->text(125, 2);
        $post->detail_address = $faker->streetAddress;
        $post->id_ward = 1;
        $post->with_owner = rand(0, 1);
        $post->restroom = rand(0, 1);
        $post->kitchen = rand(0, 1);
        $post->water_heater = rand(0, 1);
        $post->air_conditioner = rand(0, 1);
        $post->balcony = rand(0, 1);
        $post->id_room_type = rand(1, 4);
        $post->square = rand(15, 90);
        $post->price = rand(1, 55) * 1000000;
        $post->electricity_price = 1000 * rand(11, 15);
        $post->water_price = 100 * rand(25, 50);
        $post->coordinates = "";
        $post->id_owner = rand(2, 3);
        $post->time_expire = Carbon::now()->addWeeks(10);
        $post->views = rand(100, 1000);
        $post->status = rand(0, 1);
        $post->rented = rand(0, 1);
        $post->save();
        $temp = 1;
        for ($i = 0; $i < rand(3, 6); $i++) {
            $images_name[] = "bo-hinh-nen-chat-luong-cao-" . rand(1, 101) . ".jpg";
        }
        foreach ($images_name as $image_name) {
            $img = new Img();
            $img->link = $image_name;
            if ($temp == 1) {
                $img->is_main_img = 1;
                $temp = 0;
            } else {
                $img->is_main_img = 0;
            }
            $post->images()->save($img);
        }
        $arr = array("Máy giặt", "Tủ lạnh", "Kệ bếp", "Tủ quần áo", "Bàn ghế", "Giường", "Sofa");
        $random_keys = array_rand($arr, rand(1, 5));
        foreach ((array)$random_keys as $random_key) {
            $additional_amenities[] = $arr[$random_key];
        }
        foreach ($additional_amenities as $additional_amenity) {
            $amenity = Amenity::where('name', '=', $additional_amenity)->take(1)->get();
            if ($amenity->count() != 0) {
                $post->amenities()->attach($amenity[0]->id);
            } else {
                $amenity = new Amenity();
                $amenity->name = $additional_amenity;
                $amenity->save();
                $post->amenities()->attach($amenity->id);
            }
        }
        $arr = array("ĐH Quốc Gia", "ĐH Báo Chí", "Siêu thị Lotte", "Vinmart", "Cửa hàng tiện lợi", "Trường tiểu học", "Trường cấp hai", "Trung Tâm Tiếng Anh");
        $random_keys = array_rand($arr, rand(1, 5));
        foreach ($random_keys as $random_key) {
            $near_places[] = $arr[$random_key];
        }
        if (isset($near_places)) {
            foreach ((array)$near_places as $near_place) {
                $nearpl = new NearPlace();
                $nearpl->name = $near_place;
                $post->nearPlaces()->save($nearpl);
            }
        }
        echo "ok";
    }
}
