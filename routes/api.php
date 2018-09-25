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
Route::get('check-outlet', 'OutletController@checkCode');

Route::post('customer', 'CustomerController@create');

Route::group(['middleware' => 'auth:customer-api'], function () {
    Route::get('customer', 'CustomerController@all');
});

Route::group(['middleware' => 'auth:api'], function () {

    Route::get('auth/user', 'LoginController@user');

    Route::post('auth/logout', 'LoginController@logout');

    Route::post('business', 'BusinessController@create');


    Route::post('category', 'CategoryController@create');

    Route::get('category', 'CategoryController@all');

    Route::get('category/{uuid}', 'CategoryController@get');

    Route::put('category/{uuid}', 'CategoryController@edit');


    Route::post('product', 'ProductController@create');

    Route::get('product', 'ProductController@all');

    Route::get('product/{uuid}', 'ProductController@get');

    Route::put('product/{uuid}', 'ProductController@edit');


    Route::post('transaction', 'TransactionController@create');

    Route::get('transaction', 'TransactionController@all');


    Route::post('user', 'UserController@create');

    Route::get('user', 'UserController@all');

    Route::get('user/{uuid}', 'UserController@get');

    Route::get('role', 'RoleController@all');


});