<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class CashAdvance extends Model
{


    protected $fillable = ['employee_id','amount','period_amount_deduction','month_deduction','status'];
    
    public function employees()
    {
        return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Cash Advance has been {$eventName}";
    }
}
