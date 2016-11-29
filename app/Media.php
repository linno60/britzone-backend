<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;

    public function posts() {
        return $this->morphByMany('App\Post', 'mediable');
    }

    
}
