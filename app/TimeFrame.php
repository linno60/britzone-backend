<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeFrame extends Model
{
    use SoftDeletes;

    public function timeFrame() {
        return $this->morphTo();
        
    }
}
