<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $appends = array('label', 'iconLeaf');

    public function categories() {
        return $this->hasMany('App\Category');
    }

    public function children() {
        return $this->hasMany('App\Category', 'category_id');
    }

    public function attendable() {
        return $this->morphOne('App\Attendable', 'attendable');
    }

    public function getLabelAttribute() {
        return $this->name;
    }

    public function getIconLeafAttribute() {
        return $this->icon;
    }

}
