<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
		'employee_id',
		'from_designation_id',
		'to_designation_id',
		'created_at',
		'updated_at',
		'document',
	];
	protected $table = 'designation_employee';

	public function designationsFrom(){
		return $this->belongsTo('App\Designation','from_designation_id','id');
	}
	public function designationsTo(){
		return $this->belongsTo('App\Designation','to_designation_id','id');
	}
	public function employees(){
		return $this->belongsTo('App\Employee','employee_id','id');
	}
}
