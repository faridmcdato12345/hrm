<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveLog extends Model
{
    protected $fillable = ['user_id','employee_id','status','comment'];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }
}
