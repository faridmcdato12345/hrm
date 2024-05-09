<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\HasActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeLeaveType extends Model
{
    use LogsActivity;
    use HasActivity;

    protected $primaryKey = ['user_id', 'stock_id'];
    public $incrementing = false;

    protected $table = 'employee_leave_type';

    protected $fillable = [
        'leave_id', 'status',
    ];
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Employee leave type has been {$eventName}";
    }
}
