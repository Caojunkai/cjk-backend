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

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:api');
Route::group(['namespace' => 'Home'],function (){
    Route::any('/',function (){return view('welcome');});
    Route::post('/account/register','AccountController@register');
    Route::post('/account/login','AccountController@login');
    Route::put('/account/profile','AccountController@updateProfile');
    Route::get('/account/getProfile','AccountController@getProfile');
    Route::post('/account/logout','AccountController@logout');
    Route::put('/account/pwdReset','AccountController@pwdReset');
    Route::post('/account/pwdResetByEmail','AccountController@pwdResetByEmail');
    Route::put('/account/pwdModify','AccountController@pwdModify');
});