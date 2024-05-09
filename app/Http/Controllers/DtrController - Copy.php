<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Department;
use App\Leave;
use App\Services\CheckLeave;
use App\Services\SalaryCalculation;
use App\AttendanceSummary;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use DB;
use App\Employee;
use DateTime;
use DatePeriod;
use DateInterval;


class DtrController extends Controller
{	
	public $prevDeduct = 0;
	public $result = 0;
    public function index(){
        $departments = DB::connection('pgsql_external')->table('personnel_department')->get();
        return view('admin.dtr.index',compact('departments'));
    }
	private function minutes($time){
		$time = explode(':', $time);
		return ($time[0]*60) + ($time[1]) + ($time[2]/60);
	}
	public function newPrintDtr(Request $request){
		setlocale(LC_MONETARY, 'en_US');
		$from = Carbon::parse($request->from_date);
		$to = Carbon::parse($request->to_date);
		$fromPass = Carbon::parse($request->from_date)->format('m/d/Y');
		$toPass = Carbon::parse($request->to_date)->format('m/d/Y');
		$x = $from->diff($to);
		$c = collect();
		$cc = collect();
		$employee = Employee::with('designations')
		->select('id','emp_id','emp_code','firstname','lastname','designation_id','basic_salary')
		->where('department_id',$request->dept_id)
		->where('status',1)
		->orderBy('designation_id')
		->get();
		$department = DB::connection('pgsql_external')
		->table('personnel_department')
		->select('dept_name')
		->where('id',$request->dept_id)
		->first();

		$margin = DB::table('time_ins')->select('margin')->where('status',1)->pluck('margin');
		$margins = strtotime($margin[0]);
		$uu = date('H:i:s',$margins);
		foreach($employee as $key => $value){
			$from = Carbon::parse($request->from_date);
			$leave = Leave::where('employee_id',$value->id)->latest()->first();
			if($leave){
				$period = new DatePeriod(
					 new DateTime(Carbon::parse($leave->datefrom)->format('Y-m-d')),
					 new DateInterval('P1D'),
					 new DateTime(Carbon::parse($leave->dateto)->addDays(1)->format('Y-m-d'))
				);
				foreach ($period as $key => $val) {
					$array[]=$val->format('Y-m-d');    
				}
			}
			$total_absent = array();
			$totalTardi = array();
			for($i=0;$i<=$x->days;$i++){
				$clockIn = DB::connection('pgsql_external')->table('att_payloadtimecard')
							->select('emp_id','clock_in','att_date')
							->whereDate('att_date',Carbon::parse($from))
							->where('emp_id',$value->emp_id)
							->first();
				$clockOut = DB::connection('pgsql_external')->table('att_payloadtimecard')
							->select('emp_id','clock_out','att_date')
							->whereDate('att_date',Carbon::parse($from))
							->where('emp_id',$value->emp_id)
							->first();
							
				$ins = ($clockIn) ? get_object_vars($clockIn) : NULL;
				$outs = ($clockOut) ? get_object_vars($clockOut) : NULL;
				//dd(Carbon::parse($outs['clock_out']));
				$in = ($ins) ? strtotime(Carbon::parse($ins['clock_in'])->format('H:i:s')) : NULL;
				$sz = Carbon::parse($outs['clock_out'])->format('H:i:s');
				$xx = strtotime($sz);
				$ix = Carbon::parse($ins['clock_in'])->format('H:i:s');
				$ixs = strtotime($ix);
				$late = '00:00:00';
				$clockInParse = (!is_null($ins['clock_in'])) ? Carbon::parse($ins['clock_in']) : null;
				$clockOutParse = (!is_null($outs['clock_out'])) ? Carbon::parse($outs['clock_out']) : null;
				if($in){
					if($in > $margins){
						$re = new DateTime(Carbon::parse($ins['clock_in'])->format('H:i:s'));
						$f = new DateTime($margin[0]);
						if($ins && $ixs <= strtotime('12:00')){
							$late = $f->diff($re)->format("%H:%I:%S%");
						}
					}
					else{
						$late = '00:00:00';
					}
				}else{
					$late = '00:00:00';
				}
				if($clockInParse && $clockOutParse){
					$absent = '--';
				}
				elseif(!$clockInParse && !$clockOutParse){
					$sat = "saturday";
					$sun = "sunday";
					$dt1 = strtotime($from);
					$dt2 = date("l", $dt1);
					$dt3 = strtolower($dt2);
					if(strcmp($dt3,$sat) === 0 || strcmp($dt3,$sun) === 0){
						$absent = "--";
					}else{
						$absent = 1;
						array_push($total_absent,'08:00:00');
					}
				}
				elseif($clockInParse && !$clockOutParse){
					$absent = 1/2;
					array_push($total_absent,'04:00:00');
				}
				elseif(!$clockInParse && $clockOutParse){
					$absent = 1/2;
					array_push($total_absent,'04:00:00');
				}
				else{
					return false;
				}
				array_push($totalTardi,$late);
				$all_seconds = 0;
				foreach ($totalTardi as $time) {
					list($hour, $minute, $second) = explode(':', $time);
					$all_seconds += $hour * 3600;
					$all_seconds += $minute * 60; 
					$all_seconds += $second;

				}
				$total_minutes = floor($all_seconds/60); 
				$seconds = $all_seconds % 60; 
				$hours = floor($total_minutes / 60); 
				$minutes = $total_minutes % 60;
				$all_absent_seconds = 0;
				foreach ($total_absent as $time) {
					list($hour, $minute, $second) = explode(':', $time);
					$all_absent_seconds += $hour * 3600;
					$all_absent_seconds += $minute * 60; 
					$all_absent_seconds += $second;
				}
				$total_absent_minutes = floor($all_absent_seconds/60);
				$absent_seconds = $all_absent_seconds % 60; 
				$absent_hours = floor($total_absent_minutes / 60); 
				$absent_minutes = $total_absent_minutes % 60;
				$totalAmountAbsent = $absent_hours + $absent_minutes + $absent_seconds;
				$totalTardiness = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
				//dd($value->firstname." ".$totalTardiness);
				$totalAbsent = sprintf('%02d:%02d:%02d', $absent_hours ,$absent_minutes, $absent_seconds);
				$secs = strtotime($totalTardiness)-strtotime("00:00:00");
				// dd($secs);
				// $time1 = new DateTime($totalTardiness);
				// $time2 = new DateTime($totalAbsent);
				// $time1->add($time2->diff($time1));
				// $this->result = $time1->format("H:i:s");
				//$this->result = date("H:i:s",strtotime($totalAbsent)+$secs);
				$this->result = $this->add_times($totalTardiness,$totalAbsent);
				$salary = (is_null($value->designations)) ? 0 : $value->designations['salary'];
				if($this->isWeekend($from)){
					$result = date("H:i:s",strtotime($totalAbsent)+$secs);
					$s = (new SalaryCalculation)->calculate($this->prevDeduct,$request->from_date,$request->to_date,$this->result,$salary);
					$this->prevDeduct = $s;
					$s = $this->prevDeduct;
				}
				elseif(!is_null($ins['clock_in']) && !is_null($outs['clock_out'])){
					$result = date("H:i:s",strtotime($totalAbsent)+$secs);
					$s = (new SalaryCalculation)->calculate($this->prevDeduct,$request->from_date,$request->to_date,$this->result,$salary);
					$this->prevDeduct = $s;
					$s = $this->prevDeduct;
				}
				elseif(is_null($clockInParse) || is_null($clockOutParse)){
					$result = date("H:i:s",strtotime($totalAbsent)+$secs);
					$s = (new SalaryCalculation)->calculate($this->prevDeduct,$request->from_date,$request->to_date,$this->result,$salary);
					$this->prevDeduct = $s;
					$s = $this->prevDeduct;
				}
				
				// dd($value->designation);
				// var_dump($salary);
				$c->push([
					'emp_id' => $value->id,
					'emp_name' => $value->firstname.' '.$value->lastname,
					'date' => Carbon::parse($from)->format('Y-m-d'),
					'date_string' => Carbon::parse($from),
					'clock_in' =>  ((new CheckLeave)->checking($value->id,Carbon::parse($from)->format('Y-m-d'))) 
									? 'On Leave' 
									: (($clockInParse != null) ? $clockInParse->format('g:i A') : $clockInParse),
					'clock_out' => ((new CheckLeave)->checking($value->id,Carbon::parse($from)->format('Y-m-d'))) 
									? 'On Leave' 
									: (($clockOutParse != null) ?  $clockOutParse->format('g:i A') : $clockOutParse),
					'late' => $late,
					'job' => $value->designation_id,
					'emp_code'=>$value->emp_code,
					'designation' => $value->designations['designation_name'],
					'absent' => $absent,
					'total_tardi' => sprintf('%02d:%02d:%02d', $hours, $minutes,$seconds),
					'total_absent' => sprintf('%02d:%02d:%02d',$absent_hours,$absent_minutes,$absent_seconds),
					'total_time_deduction' => $this->result,
					'salary' => number_format($s,2)
				]);
				$from->addDays(1);
			}
		}
		$attendances = $c->groupBy('emp_name');
		//dd($attendances);
		
		$atts = $c;
		//dd($atts);
		return view('admin.dtr.print_new',compact('attendances','fromPass','toPass','department','diffs'));
	}

	function isWeekend($date) {
		return (date('N', strtotime($date)) >= 6);
	}
	function add_times($time1, $time2) {
		$seconds1 = $this->time_to_seconds($time1);
		$seconds2 = $this->time_to_seconds($time2);
		$total_seconds = $seconds1 + $seconds2;
		$hours = floor($total_seconds / 3600);
		$minutes = floor(($total_seconds % 3600) / 60);
		$seconds = $total_seconds % 60;
		// $hours = $hours % 24;
		return str_pad($hours, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ":" . str_pad($seconds, 2, '0', STR_PAD_LEFT);
	}
	function time_to_seconds($time) {
		$parts = explode(':', $time);
		return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
	}
}
