<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {

    return redirect('http://britzone.id');
    //return response()->json('teset');
});

//Auth::routes();


Route::get('auth/facebook', 'Auth\FacebookController@redirectToProvider');
Route::get('auth/facebook/callback', 'Auth\FacebookController@handleProviderCallback');


Route::get('/home', 'HomeController@index');
