<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSocialWelfareNumber extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'social_welfare_id',
        'number'
    ];

    public function employees(){
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function socialWelfares(){
        return $this->belongsTo(SocialWelfare::class,'social_welfare_id');
    }
}
