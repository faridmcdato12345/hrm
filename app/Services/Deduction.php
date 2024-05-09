<?php

namespace App\Services;

use Carbon\Carbon;

class Deduction {
    
    private $timeDeduction;
    private $salary;
    private $month;

    public function __construct($timeDeduction,$salary,$month){
        $this->timeDeduction = $timeDeduction;
        $this->salary = floatval($salary);
        $this->month = $month;
    }

    public function compute(){
        $workingDays = intval($this->getWorkingDays());
        $perDay = $this->salary / $workingDays;
        $perHour = $perDay / 8;
        $perMin = round($perHour / 60,2);

        $newSalary = $perMin * $this->timeDeduction;


        return $newSalary;
    }

    public function addAbsent(){
        $time = explode(":",$this->timeDeduction,3);
        $hr = intval($time[0]);
        $min = intval($time[1]);
        $toMin = 60 * $hr;
        $totalTime = $toMin + $min;
        $this->timeDeduction = $totalTime;
        
        return $this->compute();
  
    }

    public function getWorkingDays(){
        $month = $this->month;
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $dates = [];
        while ($start->lte($end)) {
            $carbon = Carbon::parse($start);
            if ($carbon->isWeekend() !=true) { 
                $dates[] = $start->copy()->format('Y-m-d');
            }
            $start->addDay();
        }

        $count = count($dates);
        
        return $count;
    }


}