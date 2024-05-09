<?php

namespace App\Console\Commands;

use App\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class pmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:pmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'HR Executes PM In';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        date_default_timezone_set('Asia/Manila');
        $department = array(9); //5 OGM AND 9 IT
        // $department = array(10);
        $employees = Employee::with('designations')
        ->select('id','emp_id','emp_code','firstname','lastname','designation_id','basic_salary')
        ->whereIn('department_id', $department)
        ->where('status', 1)
        ->orderBy('designation_id')
        ->get();

        if(Carbon::now()->isWeekend() || Carbon::now()->isMonday()){
            // Return String and Do nothing No Insertion Made
            return $this->line('Weekend/Monday');
        }

        // if(Carbon::now()->isWednesday()){
        //     return $this->line('Wednesday/Holiday');
        // }
        $ignoreIds = [153,154];
        foreach($employees as $employee){
            if (in_array($employee->emp_id, $ignoreIds)) {
                continue;
            }
            $emp_id = $employee->emp_id;
            $att_date = Carbon::now()->format('Y-m-d');
            $dateWeek = Carbon::parse($att_date);
            $yearStart = Carbon::createFromDate($dateWeek->year, 1, 1);
            $week = $yearStart->diffInWeeks($dateWeek);
            $weekday = $dateWeek->dayOfWeekIso - 1;
            $date_type = 1;
            //PM
            $check_inPm = Carbon::parse($att_date.' 13:00:00 +08:00');
            $check_inPm = $check_inPm->format('Y-m-d H:i:sP');
            $check_outPm = Carbon::parse($att_date.' 17:00:00 +08:00');
            $check_outPm = $check_outPm->format('Y-m-d H:i:sP');
            //PM end
            $work_day = 1.0;
            //random 8:00 - 8:15
            $pm = $this->randomCheck('pm');
            $clock_inPM = Carbon::parse($att_date.' '.$pm[0].' +08:00')->format('Y-m-d H:i:sP');
            $clock_outPM = NULL;
            $break_out = NULL;
            $break_in = NULL;
            $lock_down = 'false';
            $emp_id = $emp_id;
            $time_table_id = NULL;
            $parse = Carbon::parse($att_date);
            $checkPM = $this->checkIfInOutPMExist($parse,$emp_id);
            if($checkPM == 0){
                $uuidPM = $this->generateCustomUuid('pm'.$emp_id);
                // For PM
                DB::connection('pgsql_external')
                ->table('att_payloadtimecard')
                ->insert([
                    "id"=>$uuidPM,
                    "att_date"=>$att_date,
                    "week"=>$week,
                    "weekday"=>$weekday,
                    "date_type"=>$date_type,
                    "time_table_alias"=> 'pm',
                    "check_in"=>$check_inPm,
                    "check_out"=>$check_outPm,
                    "work_day"=>$work_day,
                    "clock_in"=>$clock_inPM,
                    "clock_out"=>$clock_outPM,
                    "break_out"=>$break_out,
                    "break_in"=>$break_in,
                    "lock_down"=>$lock_down,
                    "emp_id"=>$emp_id,
                    "time_table_id"=>$time_table_id,
                ]);

                $this->line('PM In Succesfully Inserted Data! '.'Dated: '.Carbon::now()->format('Y-m-d H:i').' ID ='.$emp_id.' '.$uuidPM);
            }else{
                $timeAM = DB::connection('pgsql_external')->table('att_payloadtimecard')
                ->select('emp_id','clock_in','clock_out','att_date')
                ->whereDate('att_date',Carbon::now()->format('Y-m-d'))
                ->where('emp_id',$employee->emp_id)
                ->where('time_table_alias','pm')
                ->first();
                if($timeAM->clock_in == NULL){
                    // Update
                    DB::connection('pgsql_external')
                    ->table('att_payloadtimecard')
                    ->where('emp_id',$emp_id)
                    ->where('time_table_alias','pm')
                    ->whereDate('att_date',Carbon::now()->format('Y-m-d'))
                    ->update([
                        "clock_in"=>$clock_inPM,
                    ]);

                    $this->line('PM In Already Exist. Updating! '.'Dated: '.Carbon::now()->format('Y-m-d H:i').' ID ='.$emp_id);
                }else{
                    $this->line('PM In Already Exist. Bypassing! '.'Dated: '.Carbon::now()->format('Y-m-d H:i').' ID ='.$emp_id);
                }
            }
        }
        return $this->line('Completed');
    }
    function generateCustomUuid($value)
    {
        $count= DB::connection('pgsql_external')->table('att_payloadtimecard')->count();
        $hash = hash('sha256', $value . $count);

        $uuid = substr($hash, 0, 8) . '-' .
        substr($hash, 8, 4) . '-' .
        substr($hash, 12, 4) . '-' .
        substr($hash, 16, 4) . '-' .
        substr($hash, 20, 12);

        return $uuid;
    }
    function checkIfInOutPMExist($date,$id){
        $formatDate = $date->format('Y-m-d');
		$timeAM = DB::connection('pgsql_external')->table('att_payloadtimecard')
		->select('emp_id','clock_in','clock_out','att_date')
		->whereDate('att_date',Carbon::parse($formatDate))
		->where('emp_id',$id)
		->where('time_table_alias','pm')
		->first();

        if(empty($timeAM)){
            return 0;
        }else{
            return 1;
        }
    }
    function randomCheck($type){
        $array = array();
        if($type == 'am'){
            // Random 7 or 8 For AM In
            $randomIn1st = intval(rand(7,8));
            if($randomIn1st == 8){
                $inRand = rand(0,15);
                $inRand = str_pad($inRand, 2, '0', STR_PAD_LEFT);
                $amIn = '08:'.$inRand;
            }
            if($randomIn1st == 7){
                $inRand = rand(30,59);
                $inRand = str_pad($inRand, 2, '0', STR_PAD_LEFT);
                $amIn = '07:'.$inRand;
            }

            $outRand = rand(0,29);
            $outRand = str_pad($outRand, 2, '0', STR_PAD_LEFT);
            $amOut = '12:'.$outRand;

            array_push($array, $amIn, $amOut);

            return $array;
        }else{
            $randomIn1st = intval(rand(12,13));
            if($randomIn1st == 12){
                $inRand = rand(30,59);
                $inRand = str_pad($inRand, 2, '0', STR_PAD_LEFT);
                $pmIn = '12:'.$inRand;
            }
            
            if($randomIn1st = 13){
                $inRand = rand(0,15);
                $inRand = str_pad($inRand, 2, '0', STR_PAD_LEFT);
                $pmIn = '13:'.$inRand;
            }

            $outRand = rand(0,29);
            $outRand = str_pad($outRand, 2, '0', STR_PAD_LEFT);
            $pmOut = '17:'.$outRand;
            array_push($array, $pmIn, $pmOut);

            return $array;
        }
    }
}
