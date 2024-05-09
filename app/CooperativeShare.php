<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CooperativeShare extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'social_walfare_id',
        'bracket',
        'amount',
        'to_bracket',
        'status'
    ];

    public function socialWelfares(){
        return $this->belongsTo(SocialWelfare::class,'social_walfare_id','id');
    }
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Cooperative Share has been {$eventName}";
    }
}
