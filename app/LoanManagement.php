<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanManagement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'social_welfare_id',
        'name',
        'status'
    ];

    public function socialWelfares(){
        return $this->belongsTo('App\Loan','social_welfare_id','id');
    }

}
