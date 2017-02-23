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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::get('login', 'Auth\LoginController@index');
Route::post('login', 'Auth\LoginController@login');

Route::group(['middleware' => ['jwt.auth']], function() {


    Route::resource('config', 'ConfigController');

    Route::get('category/all', 'CategoryController@all');
    Route::get('category/children/{id}', 'CategoryController@children');
    Route::get('category/attendable/{id}', 'CategoryController@attendable');

    Route::resource('category', 'CategoryController');

    Route::resource('media', 'MediaController');

    Route::get('post/currentClass', 'PostController@currentClass');
    Route::post('post/participate', 'PostController@participate');
    //Route::get('post/{id}/download/participants', 'PostController@downloadParticipants');
    Route::resource('post', 'PostController');

    Route::resource('person', 'PersonController');

    Route::resource('user', 'UserController');
});