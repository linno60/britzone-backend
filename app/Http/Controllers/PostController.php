<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Categorizable;
use App\Content;
use App\Mediable;
use Cloudder;
use App\TimeFrame;
use App\Media;
use App\Person;
use App\Participant;

class PostController extends Controller
{

    private function truncateContent ($post) {
        foreach($post as $postKey => $postValue) {
            $tempContent = $postValue->content[0]->content;
            unset($post[$postKey]->content);
            $post[$postKey]->content = $tempContent;
        }
    }

    private function truncateCategory ($post) {
        foreach($post as $postKey => $postValue) {
            $tempCategory = [];
            
            foreach($postValue->categories as $categoryKey => $categoryValue) {
                array_push($tempCategory, $categoryValue['category']->name);
            }

            unset($post[$postKey]->categories);
            $post[$postKey]->categories = $tempCategory;
        }
    }

    public function index(Request $request) {

        //return response()->json(['date' => new \DateTime()]);
        $post = Post::with(
            ['category' => 
                function($query) use ($request) {
                    $query
                    ->with(
                        ['category' => 
                            function($query1) use ($request) {
                                $query1->where('id', $request->category_id);
                            }
                        ])
                    
                    ->whereHas('category', 
                        function($query2) use ($request) {
                            $query2->where('id', $request->category_id);
                        })
                    
                    ;
                        
            }])
            
            /*
            ->whereHas('category', 
                function($query3) use ($request) {
                    $query3->where('id', $request->category_id);
                })
            */
            
            ->with('content', 'user')
            ->where('name', 'LIKE', '%' . $request->keyword . '%')
            ->paginate($request->display);
        //$this->truncateCategory($post);
        //$this->truncateContent($post);
        return response()->json($post);
    }

    public function store(Request $request) {
        
        /*
            request: {
                name,
                status,
                category_id,
                content
            }
        */
        $post = new Post;
        $post->name = $request->input('name');
        $post->status = $request->input('status');
        $post->user_id = 1;
        $post->touch();

        /* to insert post category */
        $categorized = new Categorizable;
        $categorized->category_id = $request->input('category_id');
        $categorized->touch();
        $post->category()->save($categorized);

        /* to insert post subcategory */
        $subcategorized = new Categorizable;
        $subcategorized->category_id = $request->input('subcategory_id');
        $subcategorized->touch();
        $categorized->categorizables()->save($subcategorized);


        /* to insert mediable */
        $file = $request->input('media');
        $file = json_decode(json_encode($file), true);

        /* selecting from library */
        if (!empty($file['id'])) {
            $media = Media::find($request->input('media')['id']);
            $post->media()->save($media);
        }

        /* new upload */
        if (!empty($request['media']['image'])) {
            $media = new Media;
            $filename = $request['media']['image']->path();
            Cloudder::upload($filename);
            $media->cloud_id = Cloudder::getPublicId();
            $post->media()->save($media);
        }


        /* to insert post content */
        $content = new Content;
        $content->content = $request->input('content');
        $content->touch();        
        $post->content()->save($content);

        /* to insert time frame */
        $session = new TimeFrame;
        $session->name = 'start';
        $date = date('Y-m-d H:i:s', strtotime($request->input('timeStart')));
        $session->value = $date;
        $session->touch();
        $post->session()->save($session);

        $session = new TimeFrame;
        $session->name = 'end';
        $date = date('Y-m-d H:i:s', strtotime($request->input('timeEnd')));
        $session->value = $date;
        $session->touch();
        $post->session()->save($session);

        //return response()->json(['date' => $date]);
        

    }

    public function show($id) {
        $post = Post::with('content', 'user', 'media')->categorizing()
            ->find($id);
        
        
        $post = json_decode(json_encode($post), true);
        $media = $post['media'][0];
        $media['url'] = Cloudder::show($media['cloud_id']);
        unset($post['media']['media']);
        $post['media'] = $media;

        $post['content'] = $post['content']['content'];
        $category = $post['category'][0];
        $subcategory = $category['categorizables'][0]['category'];
        $category = $category['category'];
        $category['subcategory'] = $subcategory;

        $post['category'] = $category;
        
        return response()->json($post);
    }

    public function destroy($id) {
        $post = Post::find($id);

        $post->category()->delete();
        $post->media()->detach();
        $post->content()->delete();
        $post->session()->delete();
        $post->participants()->detach();
        
        $post->delete();

    }

    public function currentClass(Request $request) {

        //$datetime = urldecode($request->localTime);
        //$date = date('Y-m-d H:i:s', strtotime($request->localTime));
        //return response()->json(['local' => $datetime]);

        $post = Post::with('content', 'user', 'media', 'session')->categorizing()
            ->get();
        
        //return response()->json($post);

        $response = [];

        

        foreach ($post as $postKey => $postValue) {
            if (count($postValue->session) == 0) {
                continue;
            }

            $frame = [];

            foreach($postValue->session as $sessionKey => $sessionValue) {

                if ($sessionValue->name == 'start') {
                   $frame['start'] = $sessionValue->value;
                }

                if ($sessionValue->name == 'end') {
                   $frame['end'] = $sessionValue->value;
                }

            }

            //return response()->json($frame);

            if (!empty($frame['start']) && !empty($frame['end'])) {
                $start = strtotime($frame['start']);
                $end = strtotime($frame['end']);
                $now = new \DateTime();
                $now = $now->getTimestamp();

                $frame['now'] = date('Y-m-d H:i:s', $now);
                $post[$postKey]->frame = $frame;
                
                

               /* 
                return response()->json([
                    date('Y-m-d H:i:s', $start), 
                    date('Y-m-d H:i:s', $now), 
                    date('Y-m-d H:i:s', $end)
                ]);
                */

                if ($now >= $start & $now <= $end ) {
                    //return 'true';
                    array_push($response, $post[$postKey]);
                } 
                
                
                else {
                    array_push($response, $post[$postKey]);
                }
                
                
                
                
            }

            $content = $post[$postKey]['content']->content;
            unset($post[$postKey]['content']);
            $post[$postKey]['content'] = $content;
           
            $post[$postKey]['image'] = Cloudder::show($post[$postKey]['media'][0]->cloud_id, [
                'width'     =>  null,
                'height'    =>  null,
                'crop'      =>  'scale',
                'format'    =>  'jpg',
            ]);


            $category = $post[$postKey]['category'][0];

            unset($post[$postKey]['user']);
            unset($post[$postKey]['category']);
            unset($post[$postKey]['media']);
            unset($post[$postKey]['session']);

            $post[$postKey]['category'] = $category['categorizables'][0]['category']['name'];

            
        }
         
        
        
        return response()->json($response);
    }


    public function participate(Request $request) {

        // old member
        if ($request->input('auth')) {

             // check if user already check-in
            $post = Post::with(['participants' => function($query) use ($request) {
                $query->where('phone', $request->input('auth'))->orWhere('email', $request->input('auth'));
            }])->find($request->input('post_id'));
            if (count($post->participants) > 0) {

                //user already check in
                return response()->json([
                    'person'    =>  $post->participants[0]->name,
                    'message'   =>  'Hello <b>' . $post->participants[0]->name . '</b>, you\'ve already check in for this meeting, please contact any PIC if you need some helps.',
                ]);

            } else {
                
                // check if user already register
                $member = Person::where('phone', $request->input('auth'))->orWhere('email', $request->input('auth'))->first();
                if ($member) {
                    
                    //existing member checking-in

                    $post->participants()->save($member);
                    return response()->json([
                        'person'    =>  $member->name,
                        'message'   =>  'Welcome again <b>' . $member->name . '</b>. Enjoy the class...',
                    ]);

                } else {

                    return response()->json([
                        'person'    =>  'anonymous',
                        'message'   =>  'Email address nor phone number haven\'t registerd yet. You may insert it wrong or are you new member?',
                        'guest'     =>  true,
                    ]);

                }

                return response()->json($post);
            }




        //new member
        } else {

        
            // check if user already check-in
            $post = Post::with(['participants' => function($query) use ($request) {
                $query->where('phone', $request->input('phone'))->orWhere('email', $request->input('email'));
            }])->find($request->input('post_id'));
            if (count($post->participants) > 0) {

                //user already check in
                return response()->json([
                    'person'    =>  $post->participants[0]->name,
                    'message'   =>  'Hello <b>' . $post->participants[0]->name . '</b>, you\'ve already check in for this meeting, please contact any PIC if you need some helps.',
                ]);

            } else {
                
                // check if user already register
                $member = Person::where('phone', $request->input('phone'))->orWhere('email', $request->input('email'))->first();
                if ($member) {
                    
                    //existing member checking-in

                    $post->participants()->save($member);
                    return response()->json([
                        'person'    =>  $member->name,
                        'message'   =>  'Welcome again <b>' . $member->name . '</b>. Enjoy the class...',
                    ]);
                } else {

                    $person = new Person;
                    $person->name = $request->input('name');
                    $person->born_date = $request->input('birth');
                    $person->job = $request->input('job');
                    $person->company = $request->input('company');
                    $person->address = '';
                    $person->phone = $request->input('phone');
                    $person->email = $request->input('email');
                    $person->touch();
                    $post->participants()->save($person);

                    return response()->json([
                        'person'    =>  $person->name,
                        'message'   =>  '<b>Newcomer...!!!</b>, you are very welcomed here, <b>' . $person->name . '</b>. Please, enjoy the class...',
                    ]);

                }

                return response()->json($post);
            }
        
            return response()->json('cacad');
        }
    }
}
