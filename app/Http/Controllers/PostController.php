<?php

namespace App\Http\Controllers;

use DateTime;
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
use Excel;

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

        function post_timeframing($post) {
            $frame = [];

            foreach($post['session'] as $sessionKey => $sessionValue) {

                if ($sessionValue['name'] == 'start') {
                    $frame['start'] = $sessionValue['value'];
                    $frame['start'] = new \DateTime($frame['start']);
                    $frame['start'] = $frame['start']->format('D, M j, Y');
                }

                if ($sessionValue['name'] == 'end') {
                    $frame['end'] = $sessionValue['value'];
                    $frame['end'] = new \DateTime($frame['end']);
                    $frame['end'] = $frame['end']->format('D, M j, Y');
                }

                

            }

            //return response()->json($frame);

            if (!empty($frame['start']) && !empty($frame['end'])) {
                $start = strtotime($frame['start']);
                $end = strtotime($frame['end']);
                $now = new \DateTime();
                $now = $now->getTimestamp();

                $frame['now'] = date('Y-m-d H:i:s', $now);
                $post['frame'] = $frame;
                
            }

            
        }

        function accumulate_participants($posts) {

            foreach($posts as $key=>$value) {
                $total_participants = count($value->participants);    
                unset($posts[$key]['participants']);
                $posts[$key]['total_participants'] = $total_participants;
                post_timeframing($posts[$key]);
                unset($posts[$key]['session']);
            }
        }

        

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
            ->orderBy('created_at', 'desc')
            ->with('participants', 'session')->categorizing()
            ->paginate($request->display);
        //$this->truncateCategory($post);
        //$this->truncateContent($post);
        accumulate_participants($post);

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

         //return response()->json($request->all());


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
        $post = Post::with('content', 'user', 'media', 'participants', 'session')->categorizing()
            ->find($id);
        
        
        $post = json_decode(json_encode($post), true);

        /* check if post have media attached */
        if (!empty($post['media'][0])) {
            $media = $post['media'][0];
            $media['url'] = Cloudder::show($media['cloud_id'], [
                    'width'     =>  null,
                    'height'    =>  null,
                    'crop'      =>  'scale',
                    'format'    =>  'jpg',
            ]);
            unset($post['media']['media']);
            $post['media'] = $media;
        }
        

        $post['content'] = $post['content']['content'];
        $category = $post['category'][0];
        $subcategory = $category['categorizables'][0]['category'];
        $category = $category['category'];
        $category['subcategory'] = $subcategory;

        $post['category'] = $category;


        $frame = [];

        foreach($post['session'] as $sessionKey => $sessionValue) {

            if ($sessionValue['name'] == 'start') {
                $frame['start'] = $sessionValue['value'];
            }

            if ($sessionValue['name'] == 'end') {
                $frame['end'] = $sessionValue['value'];
            }

        }

        //return response()->json($frame);

        if (!empty($frame['start']) && !empty($frame['end'])) {
            $start = strtotime($frame['start']);
            $end = strtotime($frame['end']);
            $now = new \DateTime();
            $now = $now->getTimestamp();

            $frame['now'] = date('Y-m-d H:i:s', $now);
            $post['frame'] = $frame;
            
        }

        
        return response()->json($post);
    }

    public function update(Request $request, $id) {

        //return response()->json($request['media']['id']);

        $post = Post::with('content', 'session')->categorizing()->find($id);
        
        //return response()->json($post);

        $post->name = $request->input('name');
        $post->status = $request->input('status');
        $post->user_id = 1;
        $post->touch();
        
        /* update content */
        $content = Content::find($post->content->id);
        $content->content = $request->input('content');
        $content->touch();        
        $post->content()->save($content);

        /* update time frame */
        foreach($request->session as $session) {
            if ($session['name'] == 'start') {
                $sessionFrame = TimeFrame::find($session['id']);
                $date = date('Y-m-d H:i:s', strtotime($request->input('timeStart')));
                $sessionFrame->value = $date;
                $sessionFrame->touch();
                $post->session()->save($sessionFrame);
            }

            if ($session['name'] == 'end') {
                $sessionFrame = TimeFrame::find($session['id']);
                $date = date('Y-m-d H:i:s', strtotime($request->input('timeEnd')));
                $sessionFrame->value = $date;
                $sessionFrame->touch();
                $post->session()->save($sessionFrame);
            }
        }
        
        /* get post category */
        $categorized = Categorizable::where('categorizable_id', $id)
                        ->where('categorizable_type', 'App\Post')->first();
        
        /* update post subcategory */
        $subcategorized = Categorizable::where('categorizable_id', $categorized->id)
                        ->where('categorizable_type', 'App\Categorizable')->first();
        $subcategorized->category_id = $request->input('subcategory_id');
        $subcategorized->touch();
        $categorized->categorizables()->save($subcategorized);


        /* to insert mediable */
        $file = $request->input('media');
        $file = json_decode(json_encode($file), true);

        /* selecting from library */
        if (!empty($request['media']['id'])) {
            $media = Media::find($request['media']['id']);
            $post->media()->detach();       //remove existing relationship
            $post->media()->save($media);   //remove existing relationship
        }

        /* new upload */
        if (!empty($request['media']['image'])) {
            $media = new Media;
            $filename = $request['media']['image']->path();
            Cloudder::upload($filename);
            $media->cloud_id = Cloudder::getPublicId();
            $post->media()->detach();       //remove existing relationship
            $post->media()->save($media);   //remove existing relationship
        }

        
        

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

        $post = Post::with('content', 'user', 'media', 'session')->categorizing()
            ->get();
        
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
                

                if ($now >= $start & $now <= $end ) {
                    //return 'true';
                    array_push($response, $post[$postKey]);
                } 
                
                /*
                else {
                    array_push($response, $post[$postKey]);
                }
                */
                
                
                
            }

            $content = $post[$postKey]['content']->content;
            unset($post[$postKey]['content']);
            $post[$postKey]['content'] = $content;
           
            if ($post[$postKey]['image'])
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

        function user_already_register() {

        };

        function check_membership( $auth1, $auth2 ) {
            return Person::where('phone', $auth1)->orWhere('email', $auth2)->first();
        }

        function existing_member_check_in($post, $member) {
            $post->participants()->save($member);
            $participant = Participant::where('person_id', $member->id)
                ->where('participable_id', $post->id)
                ->where('participable_type', 'App\Post')
                ->first();
            $participant->created_at = new DateTime;
            $participant->updated_at = new DateTime;
            $participant->save();
        }

        function completing_member_data($member) {
            $missing_data = [];

            if (empty($member->born_date)) array_push($missing_data, 'born_date');
            if (empty($member->gender)) array_push($missing_data, 'gender');
            if (empty($member->marital)) array_push($missing_data, 'marital');
            if (empty($member->company)) array_push($missing_data, 'company');

            return $missing_data;
        }

        // old member
        if ($request->input('auth')) {

             // check if user already check-in
            $post = Post::with(['participants' => function($query) use ($request) {
                $query->where('phone', $request->input('auth'))->orWhere('email', $request->input('auth'));
            }])->find($request->input('post_id'));

           // $post->participants()->touch();

            if (count($post->participants) > 0) {

                //user already check in
                return response()->json([
                    'person'    =>  $post->participants[0]->name,
                    'person_id' =>  $post->participants[0]->id,
                    'message'   =>  'Hello <b>' . $post->participants[0]->name . '</b>, you\'ve already check in for this meeting, please contact any PIC if you need some helps.',
                ]);

            } else {
                
                // check if user already register
                $member = check_membership($request->input('auth'), $request->input('auth'));
                if ($member) {
                    //existing member checking-in
                    existing_member_check_in($post, $member);

                    return response()->json([
                        'person'    =>  $member->name,
                        'person_id' =>  $member->id,
                        'message'   =>  'Welcome again <b>' . $member->name . '</b>. Enjoy the class...',
                        'fields'    => completing_member_data($member),
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
                    'person_id' =>  $post->participants[0]->id,
                    'message'   =>  'Hello <b>' . $post->participants[0]->name . '</b>, you\'ve already check in for this meeting, please contact any PIC if you need some helps.',
                ]);

            } else {
                
                // check if user already register
                $member = check_membership($request->input('phone'), $request->input('email'));
                if ($member) {
                    //existing member checking-in
                    existing_member_check_in($post, $member);

                    return response()->json([
                        'person'    =>  $member->name,
                        'person_id' =>  $member->id,
                        'message'   =>  'Welcome again <b>' . $member->name . '</b>. Enjoy the class...',
                        'fields'    => completing_member_data($member),
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
                    $person->gender = (!empty($request->input('gender'))) ? $request->input('gender') : null;
                    $person->marital = (!empty($request->input('marital'))) ? $request->input('marital') : null;
                    
                    $person->touch();

                    existing_member_check_in($post, $person);

                    return response()->json([
                        'person'    =>  $person->name,
                        'message'   =>  '<b>Newcomer...!!!</b>, you are very welcomed here, <b>' . $person->name . '</b>. Please, enjoy the class...',
                    ]);

                }

                return response()->json($post);
            }
        
        }
    }

    public function uploadParticipants(Request $request, $post_id) {

        function clearing_null_value($response) {
            foreach ($response as $key => $value) {
                if (!$value->name) {
                    unset($response[$key]);
                }
            }
        }

        function insert_new_participant($person_id, $post_id) {

            //need check if already in database
            $participant = new Participant;
            $participant->person_id = $person_id;
            $participant->participable_id = $post_id;
            $participant->participable_type = 'App\Post';
            $participant->save();
            
            
        }

        function insert_new_person($participant, $post_id) {

            //need check if already in participant list
            $person = new Person;
            $person->name = $participant->name;
            $person->phone = $participant->phone;
            $person->email = $participant->email;
            $person->job = ($participant->job) ? $participant->job : '';
            $person->address = '';
            $person->touch();
            $person->save();

            return $person->id;
        }

        function checking_phone_and_email($participants, $post_id) {
            foreach($participants as $key => $value) {
                $people = Person::where('phone', '=', $value->phone)->orWhere('email', '=', $value->email)->first();

                if (count($people) > 0) {
                    insert_new_participant($people->id, $post_id);
                } else {
                    $person_id = insert_new_person($participants[$key], $post_id);
                    insert_new_participant($person_id, $post_id);
                }
            }
        }

    
        $filename = $request->file->path();
        $response = Excel::load($filename, function($reader) {
            $result = $reader->get();
            return $result;
        })->parsed;
        
        clearing_null_value($response);

        checking_phone_and_email($response, $post_id);

        $post = Post::with('participants')->find($post_id);

        return response()->json($post->participants);
        
    }

}
