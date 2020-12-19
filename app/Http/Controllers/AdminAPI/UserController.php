<?php

namespace App\Http\Controllers\AdminAPI;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $posts = User::orderBy('created_at','desc')->get();
        return UserResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return UserResource|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->phone == '') {
            $request->validate([
                'name' => 'required|string|max:250',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|confirmed',
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
                'name' => 'required|string|max:250',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|confirmed',
                'detail_address' => 'max:250',
                'national_id_number' => 'max:15',
                'phone' => ['regex:/^(([\+]([\d]{2,}))([0-9\.\-\/\s]{5,})|([0-9\.\-\/\s]{5,}))*$/'],
                'id_ward' => 'numeric|min:1|max:11162',
                'id_role' => 'numeric|min:1|max:3'
            ], [
                'name.max' => 'Tên phải ngắn hơn 250 ký tự',
                'detail_address.max' => 'Địa chỉ cụ thể chỉ nhập số nhà/ tên đường/ thôn xóm/... và không đuợc quá 250 ký tự',
                'national_id_number.max' => 'Số CMND nhập sai định dạng',
                'phone.regex' => 'Số điện thoại sai định dạng',
            ]);
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->id_role = $request->id_role;
        $user->detail_address = $request->detail_address;
        $user->national_id_number = $request->national_id_number;
        $user->phone = $request->phone;
        $user->id_ward = $request->id_ward;
        $user->save();
        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return UserResource|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return UserResource|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($request->phone == '') {
            $request->validate([
                'name' => 'required|max:250',
                'detail_address' => 'max:250',
                'national_id_number' => 'max:15',
                'id_ward' => 'numeric|min:1|max:11162',
                'id_role' => 'numeric|min:1|max:3'
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
                'id_role' => 'numeric|min:1|max:3'
            ], [
                'name.max' => 'Tên phải ngắn hơn 250 ký tự',
                'detail_address.max' => 'Địa chỉ cụ thể chỉ nhập số nhà/ tên đường/ thôn xóm/... và không đuợc quá 250 ký tự',
                'national_id_number.max' => 'Số CMND nhập sai định dạng',
                'phone.regex' => 'Số điện thoại sai định dạng',
            ]);
        }
        if (isset($request->new_password) && $request->new_password != '') {
            $request->validate([
                'new_password' => 'required|min:6|max:32',
                'password_confirmation' => 'required|same:new_password',
            ], [
                'new_password.min' => 'Mật khẩu chỉ nằm trong khoảng 6 đến 32 ký tự',
                'new_password.max' => 'Mật khẩu chỉ nằm trong khoảng 6 đến 32 ký tự',
                'password_confirmation.same' => "Mật khẩu mới nhập lại không khớp",
            ]);
            $user->password = bcrypt($request->new_password);
        }
        $user->name = $request->name;
        $user->detail_address = $request->detail_address;
        $user->national_id_number = $request->national_id_number;
        $user->phone = $request->phone;
        $user->id_ward = $request->id_ward;
        $user->id_role = $request->id_role;
        $user->save();
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(null, 204);
    }
}
