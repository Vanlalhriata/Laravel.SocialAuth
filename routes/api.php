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

Route::group(['prefix' => 'v1'], function()
{

    Route::post('/signup', 'Api\V1\Auth\SignUpController@signup');
    Route::post('/login', 'Api\V1\Auth\LoginController@login');

    Route::group(['middleware' => 'jwt.auth'], function()
    {

        Route::get('/test', 'Api\V1\TestController@test');

    });

});
