<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    public function posts() {
        return $this->morphedByMany('App\Post', 'participable', 'participants');
    }
}
