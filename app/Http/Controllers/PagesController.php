<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Img;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        if ($request->user()->role_id != 2 || $request->user()->role_id != 3) {
            return response()->json("Bạn phải là admin hoặc chủ nhà mới được gửi bài đăng mới", 403);
        }
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
        $post->id_room_type = $request->id_room_type;
        $post->square = $request->square;
        $post->price = $request->price;
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
        return response()->json("Successfully created post", 201);
    }

    function postEditProfile(Request $request)
    {
        if($request->phone==''){
            $request->validate([
                'name' => 'max:250',
                'detail_address' => 'max:250',
                'national_id_number' => 'max:15',
                'id_ward' => 'numeric|min:1|max:11162',
            ], [
                'name.max' => 'Tên phải ngắn hơn 250 ký tự',
                'detail_address.max' => 'Địa chỉ cụ thể chỉ nhập số nhà/ tên đường/ thôn xóm/... và không đuợc quá 250 ký tự',
                'national_id_number.max' => 'Số CMND nhập sai định dạng',
            ]);
        } else {
            $request->validate([
                'name' => 'max:250',
                'detail_address' => 'max:250',
                'national_id_number' => 'max:15',
                'phone' => ['regex:/^(([\+]([\d]{2,}))([0-9\.\-\/\s]{5,})|([0-9\.\-\/\s]{5,}))*$/'],
                'id_ward' => 'numeric|min:1|max:11162',
            ], [
                'name.max' => 'Tên phải ngắn hơn 250 ký tự',
                'detail_address.max' => 'Địa chỉ cụ thể chỉ nhập số nhà/ tên đường/ thôn xóm/... và không đuợc quá 250 ký tự',
                'national_id_number.max' => 'Số CMND nhập sai định dạng',
                'phone.regex' => 'Số điện thoại sai định dạng',
            ]);
        }

        $user = $request->user();
        $user->name = $request->name;
        $user->detail_address = $request->detail_address;
        $user->national_id_number = $request->national_id_number;
        $user->phone = $request->phone;
        $user->id_ward = $request->id_ward;
        $user->save();
        return response()->json("Sửa thông tin thành công!",200);
    }

    function postChangePassword(Request $request)
    {
        $user = $request->user();
        if (!(Hash::check($request->get('old_password'), $user->password))) {
            // The password doesn't matche
            return response()->json("Mật khẩu hiện tại của bạn nhập không đúng",400);
        }
        $request->validate([
            'old_password'     => 'required',
            'new_password'     => 'required|min:6|max:32|different:old_password',
            'password_confirmation' => 'required|same:new_password',
        ], [
            'new_password.min' => 'Mật khẩu chỉ nằm trong khoảng 6 đến 32 ký tự',
            'new_password.max' => 'Mật khẩu chỉ nằm trong khoảng 6 đến 32 ký tự',
            'password_confirmation.same' => "Mật khẩu mới nhập lại không khớp",
            'new_password.different' => 'Mật khẩu mới không được giống mật khẩu cũ'
        ]);
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response()->json("Đổi mật khẩu thành công!");
    }
}
