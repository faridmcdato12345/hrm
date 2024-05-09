<?php

namespace App\Services;

use App\Leave;

class CheckLeave {

    public function checking($id,$date){
        $leave = Leave::where('employee_id',$id)
        ->whereDate('datefrom','=',$date)
        ->orWhereDate('dateto','=',$date)
        ->get();
        if($leave->isEmpty()){
            return false;
        }else{
            return true;
        }
        
    }
}