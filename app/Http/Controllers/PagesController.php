<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
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
        $request->validate([
            'title' => 'required|max:250',
            'info_detail' => 'max:2500',
            'detail_address' => 'max:250',
            'id_ward' => 'required',
            'with_owner' => 'required',
            'id_room_type' => 'required',
            'square' => 'required',
            'price' => 'required',
            'time_expire' => 'before_or_equal:+2 months',
            //'imgs' => 'required|array|min:3|max:10', // validate an array contains minimum 3 elements and maximum 10
            //'imgs.*' => 'required|mimes:jpeg,jpg,png,gif,raw', // and each element must be a jpeg or jpg or png or gif file
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
                //'imgs.min' => 'Phải có ít nhất 3 ảnh',
            ]);

        //Create a new post
        $post = new Post();
        $post->title = $request->title;
        $post->info_detail = $request->info_detail;
        $post->detail_address = $request->detail_address;
        $post->id_ward = $request->id_ward;
        $post->with_owner = $request->with_owner;
        $post->id_room_type = $request->id_room_type;
        $post->square = $request->square;
        $post->price = $request->price;
        $post->coordinates = $request->coordinates;
        $post->id_owner = $request->user()->id;
        $post->time_expire = isset($request->time_expire) ? $request->time_expire : date('Y-m-d H:m:s', strtotime("+2 weeks"));
        $post->views = 0;
        $post->status = 0;
        return response()->json("Successfully created post", 201);
    }
}
