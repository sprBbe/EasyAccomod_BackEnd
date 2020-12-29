<?php

namespace App\Http\Controllers\AdminAPI;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Img;
use App\Models\NearPlace;
use App\Models\Notification;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\Post as PostResource;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->get();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return PostResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
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
            'status' => 'required|in:' . implode(',', array(0, 1)),
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
        $post->id_owner = $request->user('api')->id;
        $post->time_expire = Carbon::now()->addWeeks($request->time_to_display);
        $post->views = 0;
        $post->rented = 0;
        $post->status = $request->status;
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
        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return PostResource|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return PostResource|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $request->validate([
            'status' => 'required|in:' . implode(',', array(0, 1)),
        ]);
        $post->status = $request->status;
        $post->save();
        $noti = new Notification();
        $noti->content = "Bài đăg \"".$post->title."\" của bạn đã được thay đổi trạng thái bởi quản trị viên";
        $noti->id_to = $post->id_owner;
        $noti->save();
        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        foreach ($post->images as $img) {
            unlink('uploads/post_images/' . $img->link);
        }
        $post->images()->delete();
        $post->amenities()->detach();
        $post->nearPlaces()->delete();
        $post->delete();
        return response()->json(null, 204);
    }
}
