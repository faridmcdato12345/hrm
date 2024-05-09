<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'designation_name', 'status','salary'
    ];

    public function job()
    {
        return $this->hasMany('App\Job');
    }
    public function employees(){
        return $this->hasOne('App\Employee');
    }

    protected $table = 'designations';
}
