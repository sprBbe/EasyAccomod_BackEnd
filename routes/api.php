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
    Route::get('home', 'PagesController@getHome')->name('home');
    Route::get('get_img/{url}', 'PagesController@getImg')->name('get_img');
    Route::get('get_all_provinces', 'PagesController@getAllProvinces')->name('get_all_provinces');
    Route::get('get_district_by_id_province/{id_province}', 'PagesController@getDistrictByIdProvince');
    Route::get('get_ward_by_id_district/{id_district}', 'PagesController@getWardByIdDistrict');
    Route::get('filter', 'PagesController@getFilter');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('new_post', 'PagesController@postNewPost')->name('new_post');
        Route::post('edit_profile', 'PagesController@postEditProfile')->name('post_edit_profile');
        Route::post('change_password', 'PagesController@postChangePassword')->name('post_change_password');
    });
});
Route::group(['prefix' => 'admin', 'namespace' => 'App\Http\Controllers\AdminController', 'middleware' => 'admin.check'], function () {
    Route::apiResources([
        'posts' => 'PostController',
    ]);
});
