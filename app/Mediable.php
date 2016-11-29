<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mediable extends Model
{
    use SoftDeletes;

    protected $fillables = ['*'];
    
    public function mediable() {
        return $this->morphTo();
    }

    public function media() {
        return $this->belongsTo('App\Media');
    }
}
