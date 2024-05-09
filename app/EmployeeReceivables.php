<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeReceivables extends Model
{
    protected $guarded = [];

    public function departments(){
        return $this->belongsTo(Department::class);
    }
}
