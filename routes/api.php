<?php

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

Route::post('auth/register', 'Customers\AuthController@signup');
Route::post('auth/login', 'Customers\AuthController@login');
Route::post('auth/logout', 'Customers\AuthController@logout');

Route::group(['middleware' => ['jwt.auth']], function () {
  Route::get('/profile', 'Customers\AccountController@getProfile');
});



