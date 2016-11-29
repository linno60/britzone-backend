<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillables = ['*'];

    public function category() {
        return $this->morphMany('App\Categorizable', 'categorizable');
    }

    public function content() {
        return $this->morphOne('App\Content', 'contentable');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function media() {
        return $this->morphToMany('App\Media', 'mediable');;
    }

    public function session() {
        return $this->morphMany('App\TimeFrame', 'frameable');
    }

    public function participants() {
        return $this->morphToMany('App\Person', 'participable', 'participants');
    }

    public function scopeCategorizing($query) {
        return $query->with(['category' => function($query) {
            $query->with('category', 'categorizables.category');
        }]);
    }

}
