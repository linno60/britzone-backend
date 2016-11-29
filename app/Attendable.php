<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendable extends Model
{
    use SoftDeletes;

    protected $fillables = ['*'];

    public function attendable () {
        return $this->morphTo();
    }
}
