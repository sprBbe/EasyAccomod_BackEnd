<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::group(['prefix' => 'auth', 'namespace' => 'App\Http\Controllers'], function () {
    Route::post('login', 'AuthController@login')->name("login");
    Route::post('signup', 'AuthController@signup');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});
Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('home', 'PageController@getHome')->name('home');
    Route::get('post/{id_post}', 'PageController@getPost')->name('post');
    Route::get('get_img/{url}', 'PageController@getImg')->name('get_img');
    Route::get('get_all_room_type', 'PageController@getAllRoomType');
    Route::get('get_all_provinces', 'PageController@getAllProvinces')->name('get_all_provinces');
    Route::get('get_district_by_id_province/{id_province}', 'PageController@getDistrictByIdProvince');
    Route::get('get_ward_by_id_district/{id_district}', 'PageController@getWardByIdDistrict');
    Route::post('filter', 'PageController@postFilter');
    Route::get('comment/{id_post}', 'PageController@getComment');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('get_post_posted', 'UserController@getPostPosted');
        Route::post('new_post', 'PageController@postNewPost')->name('new_post');
        Route::post('edit_post/{id_post}', 'PageController@postEditPost')->name('edit_post');
        Route::post('update_rented_status/{id_post}', 'PageController@updateRentedStatus');
        Route::get('get_fav_post', 'UserController@getFavPost');
        Route::post('add_fav/{id_post}', 'UserController@postAddFav')->name('add_fav');
        Route::post('remove_fav/{id_post}', 'UserController@postRemoveFav')->name('remove_fav');
        Route::post('comment/{id_post}', 'UserController@postComment')->name('comment');
        Route::post('report/{id_post}', 'UserController@postReport')->name('report');
        Route::get('get_noti','UserController@getNoti');
        Route::post('edit_profile', 'UserController@postEditProfile')->name('post_edit_profile');
        Route::post('change_password', 'UserController@postChangePassword')->name('post_change_password');
    });
});
Route::group(['prefix' => 'admin', 'namespace' => 'App\Http\Controllers\AdminAPI', 'middleware' => 'admin.check'], function () {
    Route::apiResources([
        'posts' => 'PostController',
        'users' => 'UserController',
    ]);
    Route::apiResource('comments','CommentController')->only('index','destroy','update');
    Route::apiResource('reports','ReportController')->only('index','destroy','update');
    Route::post('send_notification/{id_to}','NotiController@sendNotification');
    Route::get('get_statistic','StatisticController@getStatistic');
});
