<?php

namespace App;

use Spatie\Activitylog\Traits\HasActivity;
use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{

    use HasActivity;

    protected $fillable = [
        'employee_id', 'first_time_in', 'last_time_out', 'total_time', 'date', 'status', 'is_delay', 'first_timestamp_in', 'last_timestamp_out',
    ];
    protected $dates = [
        'first_timestamp_in',
        'last_timestamp_out',
    ];
    public function employee(){
        return $this->belongsTo('App\Employee','employee_id');
    }
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Attendance has been {$eventName}";
    }
}
