<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'social_welfare_id',
        'name',
        'status',
        'amount',
    ];

    public function socialWelfares(){
        return $this->belongsTo(SocialWelfare::class,'social_welfare_id','id');
    }
}