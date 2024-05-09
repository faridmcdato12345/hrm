<?php

namespace App\Services;

use App\Leave;

class MinuteRuling {

    // private $in;
    private $min;
    private $hr;
    private $time;
    // private $newMin;
    // private $newHr;

    public function __construct($min,$hr,$time){
        // $this->in = $in;
        $this->min = $min;
        $this->hr = $hr;
        $this->time = $time;
    }
    
    public function rule(){
        if($this->min > 0 && $this->min < 30){
            $this->min = 30;
        }

        if($this->min > 30 && $this->min < 60){
            $this->hr = $this->hr + 1;
            $this->min = 0;
            if($this->hr > 12){
                if($this->time == 'am'){
                    $this->hr = 1;
                }else{
                    $this->hr = 12 + 1;
                }
                
            }
        }

        return ['min'=>$this->min,'hr'=>$this->hr];
    }
    public function newMin(){
        $newMin = collect($this->rule());
        return $newMin['min'];
    }

    public function newHr(){
        $newHr = collect($this->rule());
        return $newHr['hr'];
    }

    public function tardyInMinutes(){
        // $newHrMin = collect($this->newHr());
        $hr = $this->newHr();
        $min = $this->newMin();
        if($this->time == 'am'){
            if($hr == 0){
                $late = 0;
            }else if($hr < 8 && $hr > 0){
                $late = 8 - $hr;
            }else{
                $late = $hr - 8;
            }
        }else if($this->time == 'pm'){
            if($hr == 0){
                $late = 0;
            }else if($hr < 13 && $hr > 0){
                $late = 13 - $hr;
            }else{
                $late = $hr - 13;
            }
        }else{
            return false;
        }
        
        $totalLate = ($late * 60) + $min;

        return $totalLate;
    }


}