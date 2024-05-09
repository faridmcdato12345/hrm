<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialWelfare extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status'
    ];
    public function memberships(){
        return $this->hasMany(Membership::class);
    }
}
