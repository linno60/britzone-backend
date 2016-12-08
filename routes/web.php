<?php

use App\Post;
use Maatwebsite\Excel\Facades\Excel;

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
Route::get('/download', function() {

        $id = 1;
        $participants = Post::with('participants')->find($id);
        //return response()->json($participants);
        $participants = $participants->participants;

        Excel::create('participants', function($excel) use ($participants) {

            $excel->sheet('participants', function($sheet) use ($participants) {
                $sheet->fromArray($participants);
                
            });

        })->download('xlsx');
});
    

Route::get('/home', 'HomeController@index');
