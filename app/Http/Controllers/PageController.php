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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Lấy các phòng trọ cho trang chủ
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function getHome()
    {
        if (Cache::has('nine_posts_on_top')) {
            //Nếu tồn tại key nine_posts_on_top
            $nine_posts_on_top = Cache::get('nine_posts_on_top');
        } else {
            // Nếu không, truy vấn database và lưu vào cache
            $nine_posts_on_top = Post::where([
                ['time_expire', '>', Carbon::now()],
                ['status', 1],
                ['rented', 0],
            ])->orderby('views', 'desc')->take(9)->get();
            Cache::put('nine_posts_on_top', $nine_posts_on_top, env('CACHE_TIME', 0));
        }
        if (Cache::has('six_posts_lastest')) {
            $six_posts_lastest = Cache::get('six_posts_lastest');
        } else {
            $six_posts_lastest = Post::where([
                ['time_expire', '>', Carbon::now()],
                ['status', 1],
                ['rented', 0],
            ])->orderby('created_at', 'desc')->take(6)->get();
            Cache::put('six_posts_lastest', $six_posts_lastest, env('CACHE_TIME', 0));
        }
        return response()->json([
            'nine_posts_on_top' => PostResource::collection($nine_posts_on_top),
            'six_posts_lastest' => PostResource::collection($six_posts_lastest),
        ]);
    }

    /**
     * Lọc phòng trọ theo các tiêu chí post lên
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */

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
        $posts->where([['status', 1],]);
        $posts->where('time_expire', '>', Carbon::now())->orderBy('created_at', 'desc')->get();
        $posts = $posts->get();
        $posts_img = array();
        foreach ($posts as $post) {
            $posts_img[] = $post->images;
        }
        return PostResource::collection($posts);
    }

    /**
     * Lấy tất cả loại phòng: Phòng trọ, Chung cư, Nhà nguyên căn,...
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Lấy tất cả các tỉnh trong database
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function getAllProvinces()
    {
        $provinces = Province::all();
        return response()->json([
            "provinces" => $provinces,
        ], 200);
    }

    /**
     * Lấy huyện theo id của tỉnh
     *
     * @param $id_province
     * @return \Illuminate\Http\JsonResponse
     */
    function getDistrictByIdProvince($id_province)
    {
        $province = Province::find($id_province);
        $districts = $province->district;
        return response()->json([
            "districts" => $districts,
        ], 200);
    }

    /**
     * Lấy xã theo id của huyện
     *
     * @param $id_district
     * @return \Illuminate\Http\JsonResponse
     */
    function getWardByIdDistrict($id_district)
    {
        $district = District::find($id_district);
        $wards = $district->ward;
        return response()->json([
            "wards" => $wards,
        ], 200);
    }

    /**
     * Nhận bài đăng mới
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            'time_to_display' => 'required|min:1|max:52',
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
        $post->time_expire = Carbon::now()->addWeeks($request->time_to_display);
        $post->views = 0;
        $post->status = $request->user()->id_role == 2 ? 1 : 0;
        $post->rented = 0;
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

    /**
     * Sửa bài đăng (chỉ bài đăng chưa được duyệt mới được sửa)
     *
     * @param Request $request
     * @param $id_post
     * @return \Illuminate\Http\JsonResponse
     */
    function postEditPost(Request $request, $id_post)
    {
        $post = Post::find($id_post);
        if ($post->id_owner != $request->user('api')->id) {
            return response()->json("Bạn phải là chủ phòng này mới được chỉnh sửa");
        }
        if ($post->status != 0) {
            return response()->json("Phòng trọ chưa được duyệt mới được chỉnh sửa, liên hệ admin để bỏ duyệt cho bài đăng này");
        }
        if ($request->hasFile('imgs')) {
            $request->validate([
                'title' => 'required|max:250',
                'info_detail' => 'max:2500',
                'detail_address' => 'max:250',
                'id_ward' => 'required|min:1|max:32248',
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
                'time_to_display' => 'required|min:1|max:52',
                //'status' => 'required|in:' . implode(',', array(0, 1)),
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
        } else {
            $request->validate([
                'title' => 'required|max:250',
                'info_detail' => 'max:2500',
                'detail_address' => 'max:250',
                'id_ward' => 'required|min:1|max:32248',
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
                'time_to_display' => 'required|min:1|max:52',
                //'status' => 'required|in:' . implode(',', array(0, 1)),
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
                ]);
        }
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
        //$post->id_owner = $request->user('api')->id;
        $post->time_expire = Carbon::parse($post->created_at)->addWeeks($request->time_to_display);
        $post->views = 0;
        //$post->status = $request->status;
        $post->save();
        if (count($images_name) != 0) {
            foreach ($post->images as $img) {
                unlink('uploads/post_images/' . $img->link);
            }
            $post->images()->delete();
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
        }
        $additional_amenities = $request->additional_amenity;
        if (isset($additional_amenities)) {
            $post->amenities()->detach();
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
            $post->nearPlaces()->delete();
            foreach ($near_places as $near_place) {
                $nearpl = new NearPlace();
                $nearpl->name = $near_place;
                $post->nearPlaces()->save($nearpl);
            }
        }
        $post = Post::find($id_post);
        return response()->json([
            'message' => 'Successfully edited post',
            'post' => new PostResource($post),
        ]);
    }

    function getComment($id_post)
    {
        $post = Post::find($id_post);
        if ($post->status != 1) {
            return response()->json([
                'message' => "Bài đăng chưa được duyệt",
            ]);
        }
        $cmt = Comment::where('id_post', $id_post)->get();
        return response()->json([
            'cmt' => $cmt,
        ]);
    }

    /**
     * Lấy ra một bài đăng
     *
     * @param $id_post
     * @return \Illuminate\Http\JsonResponse
     */
    function getPost($id_post)
    {
        $post = Post::find($id_post);
        if ($post->status != 1) {
            return response()->json([
                'message' => "Bài đăng chưa được duyệt",
            ]);
        }
        if (!($post->time_expire > Carbon::now())) {
            return response()->json([
                'message' => "Bài đăng đã hết hạn",
            ]);
        }
        $post->views++;
        $post->save();
        return response()->json([
            'post' => new PostResource($post),
        ]);
    }

    /**
     * Cập nhật trạng thái: Đã cho thuê/Còn phòng
     *
     * @param Request $request
     * @param $id_post
     * @return \Illuminate\Http\JsonResponse
     */
    function updateRentedStatus(Request $request, $id_post)
    {
        $post = Post::find($id_post);
        if ($post->id_owner != $request->user('api')->id) {
            return response()->json("Bạn phải là chủ phòng này mới được chỉnh sửa");
        }
        $request->validate([
            'rented' => 'required|in:' . implode(',', array(0, 1)),
        ]);
        $post->rented = $request->rented;
        $post->save();
        return response()->json([
            'mesasge' => 'Đã cập nhật trạng thái thuê trọ',
            'post' => new PostResource($post),
        ]);
    }
}
