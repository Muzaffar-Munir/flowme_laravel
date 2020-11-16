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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::get('details', 'App\Http\Controllers\AuthController@details');

Route::post('forget/password', 'App\Http\Controllers\AuthController@passwordForget');
Route::post('change/password', 'App\Http\Controllers\AuthController@changePassword');

Route::post('transfer/money', 'App\Http\Controllers\MtnController@transferMoney');
Route::post('send/money', 'App\Http\Controllers\MtnController@sendMoney');

Route::get('fetch/history', 'App\Http\Controllers\UserTransactionController@index');
