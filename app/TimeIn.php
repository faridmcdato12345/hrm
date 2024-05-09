<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeIn extends Model
{
    protected $fillable = [
		'time',
		'margin',
		'status',
	];

	protected $table = 'time_ins';
}
