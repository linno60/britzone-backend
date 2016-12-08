<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;

class PersonController extends Controller
{
    
    public function index(Request $request) {

        $people = Person::paginate($request->display);

        return response()->json($people);
    }
}
