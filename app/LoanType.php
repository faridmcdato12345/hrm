<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    protected $fillable = [
        'name','amount','loan_id','status','period'
    ];

    public function loans(){
        return $this->belongsTo('App\Loan','loan_id','id');
    }

    public function employees(){
        return $this->belongsToMany(Employee::class,'employees_loan_types','loan_type_id','employee_id');
    }
}
