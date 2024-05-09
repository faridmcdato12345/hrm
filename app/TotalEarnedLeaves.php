<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TotalEarnedLeaves extends Model
{
    protected $fillable = [
        'employee_id',
        'previous_total_earned',
        'sl',
        'vl',
        'vl_count',
        'sl_count',
    ];

    public function employees(){
        return $this->belongsTo('App\Employee','employee_id');
    }
}
