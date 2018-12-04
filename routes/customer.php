<?php

use Illuminate\Http\Request;

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



Route::post('register', 'CustomerController@create');
Route::post('login', 'CustomerController@login');
Route::get('product', 'CustomerController@allProduct');


Route::group(['middleware' => 'jwt.auth'], function () {
    //Route::get('customer', 'CustomerController@all');

    Route::get('user', 'CustomerController@customer');

    Route::post('like-product', 'CustomerController@likeProduct');


    Route::post('favorite', 'CustomerController@addFavorite');
    Route::get('favorite', 'CustomerController@getFavorite');


    Route::post('follow-outlet', 'CustomerController@followOutlet');
    Route::get('following', 'CustomerController@following');


    Route::post('logout', 'CustomerController@logout');

});
