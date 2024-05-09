<?php

namespace App\Traits;

use App\EmployeeSocialWelfareNumber;

trait EmployeeSocialNumberTrait {
    public function storeEmployeeSocialNumber($emp_id,$soc_id,$num){
        EmployeeSocialWelfareNumber::create([
            'employee_id' => $emp_id,
            'social_welfare_id' => $soc_id,
            'number' => $num,
        ]);
    }
}