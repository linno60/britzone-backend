<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use SoftDeletes;

    protected $fillables = ['*'];

    public $timestamps = true;
    
    public function participable() {
        return $this->morphTo();
    }



   
}
