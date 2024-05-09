<?php

namespace App\Services;

use App\Leave;

class CheckLeave {

    public function __construct($id,$date){
        $leave = Leave::where('employee_id',$id)
        ->whereDate('datefrom',$date)
        ->orWhereDate('dateto',$date)
        ->get();
        if($leave){
            return true;
        }
        return false;
    }
}