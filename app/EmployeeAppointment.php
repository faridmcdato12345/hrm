<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeAppointment extends Model
{
    protected $table = "employee_appointment";
	
	protected $fillable = [
		'emp_id',
		'designation_id'
	];
}
