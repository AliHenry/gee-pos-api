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

Route::post('auth/login', 'LoginController@login');
Route::post('auth/register', 'RegisterController@register');
Route::get('auth/user', 'LoginController@user');

Route::group(['middleware' => 'auth:api'], function () {

    Route::get('auth/user', 'LoginController@user');

});