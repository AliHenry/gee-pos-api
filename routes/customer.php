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



Route::post('customer', 'CustomerController@create');
Route::post('customer/login', 'CustomerController@login');

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('customer', 'CustomerController@all');

    Route::post('like-product', 'CustomerController@likeProduct');

    Route::post('follow-outlet', 'CustomerController@followOutlet');
});
