<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\HasActivity;
//use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\CausesActivity;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    //use LogsActivity;
    use HasActivity;
    
    protected $guard_name = 'web';

	
	protected $table = 'employees';
	
    protected $dates = ['deleted_at'];
    protected $appends = ['full_name'];

    protected $guarded = [];
    //protected static $logName = 'system';
    // protected static $logAttribute = ['*'];
    // protected static $logFillable = true;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "This employee has been {$eventName}";
    }
	public function designations(){
		return $this->belongsTo('App\Designation','designation_id','id');
	}
    public function employeeLoans(){
        return $this->belongsTo(EmployeeLoan::class);
    }
    
    public function unclaimed_salary(){
        return $this->hasMany(UnclaimedSalary::class);
    }

    public function unclaimed_benefit(){
        return $this->hasMany(UnclaimedBenefit::class);
    }

    public function unclaimed_thirteen(){
        return $this->hasMany(UnclaimedThirteen::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function branch()
    {
        return $this->belongsTo('App\Branch', 'branch_id');
    }

    public function attendanceSummary()
    {
        return $this->hasMany('App\AttendanceSummary','employee_id')->latest();
    }

    public function attendanceSummaries()
    {
        return $this->hasMany('App\AttendanceSummary');
    }

    public function leaveTypes()
    {
        return $this->belongsToMany('App\LeaveType')
            ->withPivot('status')
            ->withTimestamps();
    }
    public function isAllowed($permission)
    {
        $allowedLists = $this->permissions()->get()->pluck('name')->toArray();
        if (
            $this->hasRole('admin') ||
            in_array($permission, $allowedLists)
        ) {
            return 1;
        } else {
            return 0;
        }

        /*if(!$this->hasPermissionTo($permission)){
           return 0;
        }
        else{
            return 1;
        }*/
    }

    public function previousEarnedPoints(){
        return $this->hasMany('App\TotalEarnedLeaves','employee_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Department', 'department_id');
    }

    public function leaveLogs(){
        return $this->hasMany(LeaveLog::class,'employee_id');
    }
    public function leaves()
    {
        return $this->hasMany('App\Leave', 'employee_id');
    }
    public function cashAdvance(){
        return $this->hasMany('App\CashAdvance','employee_id');
    }
    public function memos(){
        return $this->hasMany(EmployeeMemo::class,'employee_id');
    }
    
}
