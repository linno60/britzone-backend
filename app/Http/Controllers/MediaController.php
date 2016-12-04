<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Media;
use Cloudder;
class MediaController extends Controller
{
    public function index(Request $request) {

        $media = Media::paginate($request->display);
        //return response()->json($media);

        foreach($media as $mediaKey => $mediaValue) {
            $media[$mediaKey]->url = Cloudder::show($mediaValue->cloud_id, 
            [
                'width'     =>  200,
                'height'    =>  200,
                'gravity'   =>  'auto',
                'crop'      =>  'fill',
            ]);
        }
        return response()->json($media);
    }

    public function store(Request $request) {
        
        $media = new Media;

        if ($request->name) $media->name = $request->name;
        if ($request->description) $media->description = $request->description;

        $filename = $request->image->path();

        //return response()->json($filename);

        Cloudder::upload($filename);
        $media->cloud_id = Cloudder::getPublicId();

        $media->touch();
        $media->save();

        return response()->json(['message' => 'berhasil']);
        
    }
}
