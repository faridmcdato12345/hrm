<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnclaimedBenefit extends Model
{
    protected $guarded = [];

    public function employees(){
        return $this->belongsTo(Employee::class,'employee_id');
    }
    public function benefits(){
        return $this->belongsTo(Benefit::class,'benefit_id');
    }
}
