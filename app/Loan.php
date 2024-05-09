<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','status','social_welfare_id'];

    public function socialWelfares(){
        return $this->belongsTo(SocialWelfare::class,'social_welfare_id');
    }

}
