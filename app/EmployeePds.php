<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeePds extends Model
{
    protected $fillable = [
        'employee_id',
        'document',
        'document_name'
    ];
    protected $table = 'employee_201';
}
