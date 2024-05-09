<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeMemo extends Model
{
    protected $guarded = [];

    public function employees(){
        return $this->belongsTo(Employee::class,'employee_id','id');
    }
}
