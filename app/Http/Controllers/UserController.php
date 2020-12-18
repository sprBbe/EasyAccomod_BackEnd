<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function postEditProfile(Request $request)
    {
        if ($request->phone == '') {
            $request->validate([
                'name' => 'required|max:250',
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
                'name' => 'required|max:250',
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
        return response()->json("Sửa thông tin thành công!", 200);
    }

    function postChangePassword(Request $request)
    {
        $user = $request->user();
        if (!(Hash::check($request->get('old_password'), $user->password))) {
            // The password doesn't matche
            return response()->json("Mật khẩu hiện tại của bạn nhập không đúng", 400);
        }
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|max:32|different:old_password',
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

    function postAddFav(Request $request, $id_post)
    {
        $user = $request->user();
        //$post = Post::find($id_post);
        $favourites = $user->favourites;
        $temp = array();
        foreach ($favourites as $favourite) {
            $temp[] = $favourite->id;
        }
        if (!in_array($id_post, $temp)) {
            $user->favourites()->attach($id_post);
            return response()->json("Thêm thành công", 201);
        } else {
            return response()->json("Đã được yêu thích trước đó");
        }
    }

    function postRemoveFav(Request $request, $id_post)
    {
        $user = $request->user();
        //$post = Post::find($id_post);
        $favourites = $user->favourites;
        $temp = array();
        foreach ($favourites as $favourite) {
            $temp[] = $favourite->id;
        }
        if (in_array($id_post, $temp)) {
            $user->favourites()->detach($id_post);
            return response()->json("Gỡ yêu thích thành công");
        } else return response()->json("Bài viết chưa được yêu thích trước đó");
    }

    function postComment(Request $request, $id_post)
    {
        $request->validate([
            'cmt' => 'required|max:10000',
            'rate' => 'required|in:' . implode(',', array(1, 2, 3, 4, 5)),
        ]);
        $user = $request->user();
        $cmt = new Comment();
        $cmt->content = $request->cmt;
        $cmt->rate = $request->rate;
        $cmt->id_from = $user->id;
        $cmt->id_post = $id_post;
        $cmt->status = 0;
        $cmt->save();
        return response()->json("Thêm cmt thành công", 201);
    }

    function postReport(Request $request, $id_post)
    {
        $request->validate([
            'req' => 'required|max:10000',
        ]);
        $user = $request->user();
        $rp = new Report();
        $rp->request = $request->req;
        $rp->id_from = $user->id;
        $rp->id_post = $id_post;
        $rp->status = 0;
        $rp->save();
        return response()->json("Thêm report thành công", 201);
    }

    function getNoti(Request $request)
    {
        $noti = Notification::where('id_to',$request->user()->id)->get();
        return response()->json([
            'noti' => $noti,
        ]);
    }
}
