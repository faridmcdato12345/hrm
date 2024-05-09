<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    protected $guarded = [];

    public function unclaimed_benefit(){
        return $this->hasMany(UnclaimedBenefit::class);
    }
}
