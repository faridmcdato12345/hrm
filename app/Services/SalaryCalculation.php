<?php

namespace App\Services;

use DateTime;
use DatePeriod;
use DateInterval;
use Illuminate\Support\Facades\DB;
use App\Interfaces\CalculateInterface;

class SalaryCalculation{

    public function calculate($prevDed,$from,$to,$deduction,$salary){
        $deductionSec = $this->convertTimeToSeconds($deduction);
        // dd($deductionSec);
        
        $date = date($from);
        $c = explode("-",$date);
        $year = $c[0];
        $month = $c[1];
        $weekdaysCount = $this->getWeekdaysCount($from,$to);
        $workingHours = $this->get_working_hours($from,$to);
        $workingHoursOfMonth = $this->countWorkingHourOfMonth($year,$month,array(0,6));
        $totalWorkedHours = $workingHours - $deductionSec;
        $perDay = $salary / $workingHoursOfMonth;
        $perHour = $perDay / (($this->time_out() - $this->time_in()) / 3600);
        $perMin = $perHour / 60;
        $perSec = $perMin / 60;
        $totalSalary = $totalWorkedHours * $perSec;
        // if($deductionSec == 0){
        //     return $prevDed;
        // }
        return $totalSalary;
    }

    private function getWeekdaysCount($from,$to){
        $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
        $holidayDays = ['*-12-25', '*-01-01', '2013-12-23']; # variable and fixed holidays
    
        $from = new DateTime($from);
        $to = new DateTime($to);
        $to->modify('+1 day');
        $interval = new DateInterval('P1D');
        $periods = new DatePeriod($from, $interval, $to);
    
        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) continue;
            if (in_array($period->format('Y-m-d'), $holidayDays)) continue;
            if (in_array($period->format('*-m-d'), $holidayDays)) continue;
            $days++;
        }
        return $days;
    }
    private function get_working_hours($from,$to)
    {
        // timestamps
        $from_timestamp = strtotime($from);
        $to_timestamp = strtotime($to);

        // work day seconds
        $workday_start_hour = $this->time_in();
        $workday_end_hour = $this->time_out();
        $workday_seconds = $workday_end_hour - $workday_start_hour;

        // work days beetwen dates, minus 1 day
        $from_date = date('Y-m-d',$from_timestamp);
        $to_date = date('Y-m-d',$to_timestamp);
        $workdays_number = count($this->get_workdays($from_date,$to_date));
        $workdays_number = $workdays_number<0 ? 0 : $workdays_number;

        // start and end time
        $start_time_in_seconds = date("H",$from_timestamp)*3600+date("i",$from_timestamp)*60;
        $end_time_in_seconds = date("H",$to_timestamp)*3600+date("i",$to_timestamp)*60;

        // final calculations
        $working_hours = $workdays_number * $workday_seconds + $end_time_in_seconds - $start_time_in_seconds;

        return $working_hours;
    }
    private function get_workdays($from,$to) 
    {
        // arrays
        $days_array = array();
        $skipdays = array("Saturday", "Sunday");

        // other variables
        $i = 0;
        $current = $from;

        if($current == $to) // same dates
        {
            $timestamp = strtotime($from);
            if (!in_array(date("l", $timestamp), $skipdays)) {
                $days_array[] = date("Y-m-d",$timestamp);
            }
        }
        elseif($current < $to) // different dates
        {
            while ($current < $to) {
                $timestamp = strtotime($from." +".$i." day");
                if (!in_array(date("l", $timestamp), $skipdays)) {
                    $days_array[] = date("Y-m-d",$timestamp);
                }
                $current = date("Y-m-d",$timestamp);
                $i++;
            }
        }
        return $days_array;
    }
    private function countWorkingHourOfMonth($year, $month, $ignore) {
        $count = 0;
        $counter = mktime(0, 0, 0, $month, 1, $year);
        while (date("n", $counter) == $month) {
            if (in_array(date("w", $counter), $ignore) == false) {
                $count++;
            }
            $counter = strtotime("+1 day", $counter);
        }
        // $timeDiff = $this->time_out() - $this->time_in();
        return $count;
    }
    private function time_in(){
        $margin = DB::table('time_ins')->select('time')->where('status',1)->first();
        $str_time = $margin->time;
        $result = $this->convertTimeToSeconds($str_time);
        return $result;
    }
    private function time_out(){
        $margin = DB::table('time_outs')->select('time')->where('status',1)->first();
        $str_time = $margin->time;
        $result = $this->convertTimeToSeconds($str_time);
        return $result;
    }
    private function convertTimeToSeconds($time){
        $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $time);
        sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
        $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
        return $time_seconds;
    }
}