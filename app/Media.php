<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;

    //protected $with = ['posts'];

    public function posts() {
        return $this->morphedByMany('App\Post', 'mediable');
    }

    
}
