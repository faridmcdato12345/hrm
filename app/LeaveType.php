<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name', 'counts', 'status',
    ];

    public function employees()
    {
        return $this->belongsToMany('App\Employee')
            ->withTimestamps();
    }
}
