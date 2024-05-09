<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'employee_id', 
        'leave_type', 
        'datefrom', 
        'dateto', 
        'hourslogged', 
        'reason', 
        'status',
        'description', 
        'subject', 
        'count',
        'document'
    ];

    public function leaveType()
    {
        return $this->belongsTo('App\LeaveType', 'leave_type', 'id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }

    public function pointOfContact()
    {
        return $this->belongsTo('App\Employee', 'point_of_contact', 'id');
    }

    public function lineManager()
    {
        return $this->belongsTo('App\Employee', 'line_manager', 'id');
    }
}
