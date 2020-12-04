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
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});
Route::group(['namespace'=>'App\Http\Controllers'],function(){
    Route::get('home', 'PagesController@home')->name('home');
    Route::get('new_post', 'PagesController@get_new_post')->name('get_new_post');
    Route::post('new_post', 'PagesController@post_new_post')->name('post_new_post');
});
