<?php

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'api', 'namespace' => 'Api'], function(){

    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');

    Route::group(['middleware' => 'auth.api'], function(){
        Route::get('post', 'PostsController@index');
        Route::get('post/{post}', 'PostsController@show');
        Route::post('post', 'PostsController@create');
        Route::post('post/{post}', 'PostsController@update');
        Route::delete('post/{post}', 'PostsController@delete');
    });

});

//TODO edit user avatar + add user avatar picture to post
//TODO edit email and password for user

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


