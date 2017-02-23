<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    
    private function postHierarchy($nodes) {
        foreach($nodes as $nodeKey => $nodeValue) {

            $children = Category::where('type', '=', 'p')->where('category_id', '=', $nodeValue->id)->get(['categories.name AS label', 'categories.*']);
            if (count($children) > 0) {
                $nodes[$nodeKey]['children'] = $children;
                $this->postHierarchy($children);    
            }
        }
    }

    public function index(Request $request) {

        $category = Category::where('type', $request->type)->whereNull('category_id')->get();
        return response()->json($category);
        
    }

    public function store(Request $request) {
        $category = new Category;
        $category->name = $request->name;
        $category->type = $request->type;
        $category->icon = $request->icon;
        if($request->category_id) $category->category_id = $request->category_id;
        $category->category_relation = $request->category_relation;
        $category->touch();
        $category->save();

        $category->children = [];
        return response()->json($category);
    }

    public function show($id) {

        $category = Category::with('categories')->find($id);
        return response()->json($category);

    }

    public function update(Request $request, $id) {
        $category = Category::find($id);
        $category->name = $request->name;
        $category->type = $request->type;
        $category->icon = $request->icon;
        if($request->category_id) $category->category_id = $request->category_id;
        $category->category_relation = $request->category_relation;
        $category->touch();
        $category->save();

        $category->children = [];
        return response()->json($category);

    }

    public function destroy($id) {
        $category = Category::find($id);
        $category->delete();
        return response()->json();
    }

    public function all(Request $request) {
        $categories = Category::where('type', '=', $request->type)->whereNull('category_id')->get(['categories.name AS label', 'categories.*']);
        
        $this->postHierarchy($categories);

        return response()->json($categories);
    }

    public function children($id) {

        $category = Category::with('attendable')->where('category_id', $id)->get();
        return response()->json($category);
        
    }

    public function attendable($id) {
        $category = Category::with('attendable')->find($id);
        return response()->json($category);
    }
}
