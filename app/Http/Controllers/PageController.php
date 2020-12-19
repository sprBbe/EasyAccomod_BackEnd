<?php

namespace App\Http\Controllers;

use App\Http\Resources\Post as PostResource;
use App\Models\Amenity;
use App\Models\Comment;
use App\Models\District;
use App\Models\Img;
use App\Models\NearPlace;
use App\Models\Province;
use App\Models\RoomType;
use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PageController extends Controller
{
    function getHome()
    {
        $eight_posts_on_top = Post::where([
            ['time_expire', '>', Carbon::now()],
            ['status', 1]
        ])->orderby('views', 'desc')->take(8)->get();
        $eight_posts_lastest = Post::where([
            ['time_expire', '>', Carbon::now()],
            ['status', 1]
        ])->orderby('created_at', 'desc')->take(8)->get();
        return response()->json([
            'eight_post_on_top' => PostResource::collection($eight_posts_on_top),
            'eight_post_lastest' => PostResource::collection($eight_posts_lastest),
        ]);
    }

    function postFilter(Request $request)
    {
        $posts = Post::query();
        if ($request->has('id_post')) {
            $posts->where('id', $request->id_post);
        }
        if ($request->has('id_ward')) {
            $posts->where('id_ward', $request->id_ward);
        }
        if ($request->has('with_owner')) {
            $posts->where('with_owner', $request->with_owner);
        }
        if ($request->has('restroom')) {
            $posts->where('restroom', $request->restroom);
        }
        if ($request->has('kitchen')) {
            $posts->where('kitchen', $request->kitchen);
        }
        if ($request->has('water_heater')) {
            $posts->where('water_heater', $request->water_heater);
        }
        if ($request->has('air_conditioner')) {
            $posts->where('air_conditioner', $request->air_conditioner);
        }
        if ($request->has('balcony')) {
            $posts->where('balcony', $request->balcony);
        }
        if ($request->has('id_room_type')) {
            $posts->where('id_room_type', $request->id_room_type);
        }
        if ($request->has('square_min')) {
            $posts->where('square', '>=', $request->square_min);
        }
        if ($request->has('square_max')) {
            $posts->where('square', '<=', $request->square_max);
        }
        if ($request->has('price_min')) {
            $posts->where('price', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $posts->where('price', '<=', $request->price_max);
        }
        $posts->where('status', 1);
        $posts->where('time_expire', '>', Carbon::now())->orderBy('created_at','desc')->get();
        $posts = $posts->get();
        $posts_img = array();
        foreach ($posts as $post) {
            $posts_img[] = $post->images;
        }
        return PostResource::collection($posts);
    }

    function getAllRoomType()
    {
        $roomTypes = RoomType::all();
        return response()->json([
            "room_types" => $roomTypes,
        ], 200);
    }

    function getImg($url)
    {
        return response()->file("uploads/post_images/" . $url);
    }

    function getAllProvinces()
    {
        $provinces = Province::all();
        return response()->json([
            "provinces" => $provinces,
        ], 200);
    }

    function getDistrictByIdProvince($id_province)
    {
        $province = Province::find($id_province);
        $districts = $province->district;
        return response()->json([
            "districts" => $districts,
        ], 200);
    }

    function getWardByIdDistrict($id_district)
    {
        $district = District::find($id_district);
        $wards = $district->ward;
        return response()->json([
            "wards" => $wards,
        ], 200);
    }

    function postNewPost(Request $request)
    {
        if ($request->user()->id_role == 1) {
            return response()->json("Bạn phải là admin hoặc chủ nhà mới được gửi bài đăng mới", 403);
        }
        if (!in_array($request->user()->id_role, array(1, 2, 3))) {
            return response()->json("Quyền người dùng không tồn tại!", 403);
        }
        $request->validate([
            'title' => 'required|max:250',
            'info_detail' => 'max:2500',
            'detail_address' => 'max:250',
            'id_ward' => 'required',
            'with_owner' => 'required',
            'restroom' => 'required|in:' . implode(',', array(0, 1)),
            'kitchen' => 'required|in:' . implode(',', array(0, 1)),
            'water_heater' => 'required|in:' . implode(',', array(0, 1)),
            'air_conditioner' => 'required|in:' . implode(',', array(0, 1)),
            'balcony' => 'required|in:' . implode(',', array(0, 1)),
            'additional_amenity' => 'array|max:30',
            'near_place' => 'array|max:30',
            'id_room_type' => 'required',
            'square' => 'required',
            'price' => 'required',
            'electricity_price' => 'required|numeric',
            'water_price' => 'required|numeric',
            'time_expire' => 'before_or_equal:+2 months',
            'imgs' => 'required|min:3|max:10', // validate an array contains minimum 3 elements and maximum 10
            'imgs.*' => 'required|mimes:jpeg,jpg,png,gif,raw|max:100000', // and each element must be a jpeg or jpg or png or gif file
        ],
            [
                'title.required' => 'Bạn chưa nhập tiêu đề bài đăng',
                'title.max' => 'Tiêu đề bài đăng không được quá 250 ký tự',
                'info_detail.max' => 'Thông tin chi tiết không quá 2500 ký tự',
                'detail_address.max' => 'Địa chỉ cụ thể chỉ nhập số nhà/ tên đường/ thôn xóm/... và không đuợc quá 250 ký tự',
                'id_ward.required' => 'Chưa chọn xã/phường',
                'with_owner' => 'Chưa chọn Chung chủ/Không chung chủ',
                'id_room_type.required' => 'Chưa chọn loại phòng',
                'square.required' => 'Chưa nhập diện tích phòng',
                'price.required' => 'Chưa nhập giá phòng',
                'imgs.min' => 'Phải có ít nhất 3 ảnh',
            ]);
        /* Check file */
        $images_name = array();
        $imgs = $request->file('imgs');
        foreach ($imgs as $img) {
            $namefile = "post_" . Str::random(5) . "_" . $img->getClientOriginalName();
            while (file_exists('uploads/post_images' . $namefile)) {
                $namefile = "post_" . Str::random(5) . "_" . $img->getClientOriginalName();
            }
            $images_name[] = $namefile;
            $img->move('uploads/post_images', $namefile);
        }

        //Create a new post
        $post = new Post();
        $post->title = $request->title;
        $post->info_detail = $request->info_detail;
        $post->detail_address = $request->detail_address;
        $post->id_ward = $request->id_ward;
        $post->with_owner = $request->with_owner;
        $post->restroom = $request->restroom;
        $post->kitchen = $request->kitchen;
        $post->water_heater = $request->water_heater;
        $post->air_conditioner = $request->air_conditioner;
        $post->balcony = $request->balcony;
        $post->id_room_type = $request->id_room_type;
        $post->square = $request->square;
        $post->price = $request->price;
        $post->electricity_price = $request->electricity_price;
        $post->water_price = $request->water_price;
        $post->coordinates = $request->coordinates;
        $post->id_owner = $request->user()->id;
        $post->time_expire = isset($request->time_expire) ? $request->time_expire : date('Y-m-d H:m:s', strtotime("+2 weeks"));
        $post->views = 0;
        $post->status = 0;
        $post->save();
        $temp = 1;
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
        $additional_amenities = $request->additional_amenity;
        if (isset($additional_amenities)) {
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
        }
        $near_places = $request->near_place;
        if (isset($near_places)) {
            foreach ($near_places as $near_place) {
                $nearpl = new NearPlace();
                $nearpl->name = $near_place;
                $post->nearPlaces()->save($nearpl);
            }
        }
        return response()->json("Successfully created post", 201);
    }

    function getComment($id_post)
    {
        $cmt = Comment::where('id_post', $id_post)->get();
        return response()->json([
            'cmt' => $cmt,
        ]);
    }
}
