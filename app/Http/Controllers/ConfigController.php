<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class ConfigController extends Controller
{

    public function index(Request $request) {
        $category = Category::where('type', $request->type)->get();
        return response()->json($category);
    }

    public function store(Request $request) {

        $category = new Category;
        $category->name = $request->input('name');
        $category->type = $request->input('type');
        $category->icon = $request->input('icon');
        $category->touch();
        $category->save();
                
    }
}
