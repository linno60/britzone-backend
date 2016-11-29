<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorizable extends Model
{
    use SoftDeletes;

    protected $fillables = ['*'];

    public function categorizable() {
        return $this->morphTo();
    }

    public function categorizables() {
        return $this->morphMany('App\Categorizable', 'categorizable');
    }

    public function category() {
        return $this->belongsTo('App\Category');
    }

   
}
