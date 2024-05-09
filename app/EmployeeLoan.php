<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeLoan extends Model
{
    protected $fillable = [
        'employee_id',
        'social_welfare_id',
        'loan_id',
        'amount',
        'monthly_deduction'
    ];

    public function employees(){
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function loans(){
        return $this->belongsTo(Loan::class,'loan_id');
    }

    public function socialWelfares(){
        return $this->belongsTo(SocialWelfare::class,'social_welfare_id');
    }
}
