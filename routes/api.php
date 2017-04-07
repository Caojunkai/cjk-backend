<?php

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
    Route::post('/account/user','AccountController@register');//注册
    Route::put('/account/user/token','AccountController@login');//登录
    Route::delete('/account/user/token','AccountController@logout');//登出
    Route::put('/account/user','AccountController@updateProfile');//修改信息
    Route::get('/account/user','AccountController@getProfile');//获取信息
    Route::post('/account/pwd','AccountController@pwdReset');//重置密码发送邮件
    Route::patch('/account/pwd','AccountController@pwdResetByEmail');//重置密码
    Route::put('/account/pwd','AccountController@pwdModify');//修改密码
    //============================Users===================================
    Route::resource('/users','UserController');
    Route::get('/users/{user_id}/topics','UserController@getTopics');
    Route::get('/users/{user_id}/following','UserRelationshipController@getFollowing');
    Route::get('/users/{user_id}/followers','UserRelationshipController@getFollowers');
    Route::get('/users/{user_id}/subscribes','UserController@subscribes');
    Route::get('/users/{user_id}/upvotes','UserController@upvotes');
    Route::post('/users/{user_id}/following','UserRelationshipController@following');
    //===========================categories=================================
    Route::resource('/categories','CategoryController');
    Route::get('/categories/{cate_id}/topics','CategoryController@topics');
    Route::get('/categories/{cate_id}/articles','CategoryController@articles');
    //===========================topics=================================
    Route::resource('/topics','TopicController');
//    Route::get('/topics/last','TopicController@last');
//    Route::get('/topics/popular','TopicController@popular');
    Route::get('/topics/{topic_id}/articles','TopicController@articles');
    //===========================topics-subscriber=================================
    Route::get('/topics/{topic_id}/subscribers','TopicSubscribeController@subscribers');
    Route::post('/topics/{topic_id}/subscribe','TopicSubscribeController@subscribe');
    Route::post('/topics/{topic_id}/unsubscribe','TopicSubscribeController@unsubscribe');
    //===========================articles======================================
    Route::resource('/articles','ArticleController');
    Route::resource('/articles/{article_id}/votes','ArticleVoteController');
    Route::resource('/articles/{article_id}/comments','ArticleCommentController');
});
