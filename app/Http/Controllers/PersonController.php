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

    public function update(Request $request, $id) {
        $person = Person::find($id);
        
        /*
        (empty($request->input('born_date'))   ? : $person->born_date = $request->input('birth');
        (empty($request->input('job'))         ? : $person->job = $request->input('job');
        (empty($request->input('company'))     ? : $person->company = $request->input('company');
        (empty($request->input('address'))     ? : $person->address = $request->input('address');
        (empty($request->input('gender'))      ? : $person->gender = $request->input('gender');
        (empty($request->input('marital'))     ? : $person->marital = $request->input('marital');
        */

        $person->born_date = $request->input('birth');
        $person->gender = $request->input('gender');
        $person->marital = $request->input('marital');

        $person->touch();
        $person->save();

        return response()->json($request);
    }
}
