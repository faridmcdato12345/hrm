<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Department;
use App\Leave;
use App\Services\CheckLeave;
use App\Services\SalaryCalculation;
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
	public $result2 = 0;
    public function index(){
        $departments = DB::connection('pgsql_external')->table('personnel_department')->get();
        return view('admin.dtr.index',compact('departments'));
    }
	private function minutes($time){
		$time = explode(':', $time);
		return ($time[0]*60) + ($time[1]) + ($time[2]/60);
	}
	public function newPrintDtr(Request $request){

		if($request->mode == "ampm"){
			
			set_time_limit(0);
			setlocale(LC_MONETARY, 'en_US');
			// $from_date = '2022-11-16';
			// $to_date = '2022-11-30';
			$from_date = $request->from_date;
			$to_date = $request->to_date;
			$id = $request->dept_id;
			setlocale(LC_MONETARY, 'en_US');
			$from = Carbon::parse($request->from_date);
			$to = Carbon::parse($to_date);
			$fromPass = Carbon::parse($from_date)->format('m/d/Y');
			$toPass = Carbon::parse($to_date)->format('m/d/Y');
			$x = $from->diffInDays($to);
			$c = collect();
			$employees = Employee::with('designations')
			->select('id','emp_id','emp_code','firstname','lastname','designation_id','basic_salary')
			->where('department_id',$id)
			->where('status',1)
			// ->where('emp_id',315)
			->orderBy('designation_id')
			->get();
			$department = DB::connection('pgsql_external')
			->table('personnel_department')
			->select('dept_name')
			->where('id',$request->dept_id)
			->first();

			// For Normal Time
			$marginOutAm = '12:00 PM';
			$parseOutmargin = Carbon::parse($marginOutAm)->format('H:i');
			$minutesOutmargin = Carbon::parse($parseOutmargin)->format('H') * 60 + Carbon::parse($parseOutmargin)->format('i');
			$marginOutPm = '5:00 PM';
			$parseOutPmmargin = Carbon::parse($marginOutPm)->format('H:i');
			$minutesOutPmmargin = Carbon::parse($parseOutPmmargin)->format('H') * 60 + Carbon::parse($parseOutPmmargin)->format('i');
			//For Flex Time
			$timeype = 'flex';
			foreach ($employees as $employee) {
				$fromDate = Carbon::parse($request->from_date);
				$totalTardiness = 0;
				$totalAbsent = 0;
				$totalDeductions = 0;
				$totalHrs = 0;
				$totalMins = 0;
				$totalAbsentHrs = 0;
				$totalAbsentMins = 0;
				for ($i = 0; $i <= $x; $i++) {
					
					// get Am In and Out
					$amInOut = $this->getAmInOut($fromDate, $employee->emp_id); // pgsql query
					// get Pm In and Out
					$pmInOut = $this->getPmInOut($fromDate, $employee->emp_id); // pgsql query
					// Empty means the query returns no rows or an empty result set
					// AM
					$clockInAm = '';
					$clockOutAm = '';
					$leave = '';
					$holiday = '';
					if (empty($amInOut)) {
						//Check Holiday
						$holiday = $this->checkHoliday($fromDate);
						if($holiday === 'Holiday'){
							$clockInAm = $holiday;
							$clockOutAm = $holiday;
						}else{
							// Check Leave
							$leave = $this->checkLeave($employee->id,$fromDate); // hr query
							$clockInAm = $leave;
							$clockOutAm = $leave;
						}
						
						
					}

					$amMin = 0;
					if ($clockInAm == 'Absent' || $clockOutAm == 'Absent') {
						$amMin += 240;
					} 
					if ($clockInAm == 'Leave' || $clockOutAm == 'Leave' || $clockInAm == 'Holiday' || $clockOutAm == 'Holiday') {
						$amMin = 0;
					}
					if ($clockInAm == '' && $amInOut->clock_in == NULL) {
						$clockInAm = 'No Check-In';
						$amMin += 120;
					}
					if ($clockOutAm == '' && $amInOut->clock_out == NULL) {
						$clockOutAm = 'No Check-Out';
						$amMin += 120;
					}
					// AM Check in with value (Present)
					if(!empty($amInOut->clock_in) && $amInOut->clock_in != NULL){
						$clockInAm = Carbon::parse($amInOut->clock_in)->tz('Asia/Manila')->format('H:i A');
						
					}
					// Am Check in with value (Present)
					$undertimeAm = 0;
					if(!empty($amInOut->clock_out) &&  $amInOut->clock_out != NULL){
						$clockOutAm = Carbon::parse($amInOut->clock_out)->tz('Asia/Manila')->format('H:i A');
						// (Undertime)
						$parseOutAm = Carbon::parse($amInOut->clock_out)->format('H:i');
						$undertimeAm = $this->undertime($parseOutAm,$minutesOutmargin);

					}

					$diffam = 0;
					$checkUndertimeAm = 0;
					// Am Check In and Out With Value (Present)
					if (!empty($amInOut->clock_in) && $amInOut->clock_in != NULL && !empty($amInOut->clock_out) &&  $amInOut->clock_out != NULL) {
						$gracePeriodAm = Carbon::parse('08:15 AM', 'Asia/Manila')->format('H:i A');
						if (Carbon::parse($clockInAm, 'Asia/Manila')->greaterThan(Carbon::parse($gracePeriodAm, 'Asia/Manila'))) {
							// $clockInAm is greater than 8:15 AM
							$clockInWithGraceAm = Carbon::parse($amInOut->clock_in)->format('H:i');
							$clockInWithGraceAm = Carbon::parse($clockInWithGraceAm)->subMinutes(15)->format('H:i');
							$diffam = Carbon::parse($clockInWithGraceAm)->diffInMinutes(Carbon::parse($parseOutmargin));
							$amMin += abs($diffam - 240);
							
							// (Undertime) here no need for undertime
							if($undertimeAm != 0){
								$checkUndertimeAm = 1;
							}
						} else {
							// $clockInAm is not greater than 8:15 AM
							$diffam = 0;
							$amMin += abs($diffam);
							// (Undertime) here no need for undertime
							if($undertimeAm != 0){
								$checkUndertime = 1;
							}
						}
					}
					
					// PM
					$clockInPm = '';
					$clockOutPm = '';
					if (empty($pmInOut)) {
						//Check Holiday
						$holiday = $this->checkHoliday($fromDate);
						if($holiday === 'Holiday'){
							$clockInPm = $holiday;
							$clockOutPm = $holiday;
						}else{
							// Check Leave
							$leave = $this->checkLeave($employee->id,$fromDate); // hr query
							$clockInPm = $leave;
							$clockOutPm = $leave;
						}
						
					}
					$pmMin = 0;
					if ($clockInPm == 'Absent' || $clockOutPm == 'Absent') {
						$pmMin += 240;
					} 
					if ($clockInPm == 'Leave' || $clockOutPm == 'Leave' || $clockInPm == 'Holiday' || $clockOutPm == 'Holiday') {
						$pmMin = 0;
					}
					if ($clockInPm == '' && $pmInOut->clock_in == NULL) {
						$clockInPm = 'No Check-In';
						$pmMin += 120;
					}
					if ($clockOutPm == '' && $pmInOut->clock_out == NULL) {
						$clockOutPm = 'No Check-Out';
						$pmMin += 120;
					}
					// PM Checkin With value (Present)
					if(!empty($pmInOut->clock_in) &&  $pmInOut->clock_in != NULL){
						$clockInPm = Carbon::parse($pmInOut->clock_in)->tz('Asia/Manila')->format('h:i A');
					}
					// PM Checkout With value (Present)
					$undertimePm = 0;
					if(!empty($pmInOut->clock_out) &&  $pmInOut->clock_out != NULL){
						$clockOutPm = Carbon::parse($pmInOut->clock_out)->tz('Asia/Manila')->format('h:i A');
						// (Undertime)
						$parseOutPm = Carbon::parse($pmInOut->clock_out)->format('H:i');
						$undertimePm = $this->undertime($parseOutPm,$minutesOutPmmargin);
						// dd($i,$undertimePm);
					}
					// PM
					$diffpm = 0;
					$checkUndertimePm = 0;
					// PM Check In and Out with Value (Present)
					if (!empty($pmInOut->clock_in) &&  $pmInOut->clock_in != NULL && !empty($pmInOut->clock_out) &&  $pmInOut->clock_out != NULL) {
						$gracePeriodDateTime = DateTime::createFromFormat('H:i', '13:15');
						$formattedGrace = intval($gracePeriodDateTime->format('Gi'));
						$formattedCarbon = Carbon::parse($clockInPm)->format('Hi');
						$formatted = intval($formattedCarbon);

						if ($formatted >= $formattedGrace) {
							// $clockInAm is greater than 1:15 PM
							// $clockInWithGracePm = Carbon::parse($pmInOut->clock_in)->subMinutes(15)->format('h:i A');
							// $diffpm = Carbon::parse($clockInWithGracePm)->diffInMinutes(Carbon::parse($clockOutPm));
							// $pmMin += abs($diffpm - 240);
							
							$clockInWithGracePm = Carbon::parse($pmInOut->clock_in)->format('H:i');
							$clockInWithGracePm = Carbon::parse($clockInWithGracePm)->subMinutes(15)->format('H:i');
							$diffpm = Carbon::parse($clockInWithGracePm)->diffInMinutes(Carbon::parse($parseOutPmmargin));
							$pmMin += abs($diffpm - 240);
							// (Undertime) here no need for undertime
							if($undertimePm != 0){
								$checkUndertimePm = 1;
							}
						} else {
							// $clockInAm is not greater than 1:15 PM
							$diffpm = 0;
							$pmMin += abs($diffpm);
							// (Undertime) here no need for undertime
							if($undertimePm != 0){
								$checkUndertimePm = 1;
							}
						}					
					}
					// Set tardy to 0 if its weekend and If absent
					$absent = 0;
					if($clockInAm == 'Absent' || $clockOutAm == 'Absent'){
						if(Carbon::parse($fromDate)->isWeekend()){
							$absent = 0;
							$amMin = 0;
							$pmMin = 0;
						}
						
						if(Carbon::parse($fromDate)->isWeekday()){
							$absent = 1;
							$amMin = 0;
							$pmMin = 0;
						}
					}
					if($clockInPm == 'Absent' || $clockOutPm == 'Absent'){
						if(Carbon::parse($fromDate)->isWeekend()){
							$absent = 0;
							$amMin = 0;
							$pmMin = 0;
						}
						if(Carbon::parse($fromDate)->isWeekday()){
							$absent = 1;
							$amMin = 0;
							$pmMin = 0;
						}
					}
					if($clockInAm == 'Absent' && ($clockInPm != 'Absent' || $clockOutPm != 'Absent')){
						$absent = 0.5;
					}
					if($clockInPm == 'Absent' && ($clockInAm != 'Absent' || $clockOutAm != 'Absent')){
						$absent = 0.5;
					}
					

					$lateAm = $amMin + (($checkUndertimeAm == 0) ? 0 : $undertimeAm); // tardy AM
					$latePm = $pmMin + (($checkUndertimePm == 0) ? 0 : $undertimePm); // tardy PM
					$totalTardyPerDate = $lateAm + $latePm; 
					
					$absent = ($leave == 'Leave' || $holiday == 'Holiday') ? 0 : $absent; // Absent
					$totalTardiness += $totalTardyPerDate;
					$totalDeductions += $totalTardyPerDate;
					$totalAbsent += $absent;
					
					
					//for total tardi and Absent
					if($i == $x){
						$totalTardy = $this->totaltardi($totalTardiness);
						$totalHrs = $totalTardy['hrs'];
						$totalMins = $totalTardy['mins'];
						$getAbsentHrsMins = '';
						if($totalAbsent > 0){
							$getAbsentHrsMins = $this->totalAbsences($totalAbsent);
							$totalAbsentHrs = $getAbsentHrsMins['hrs'];
							$totalAbsentMins = $getAbsentHrsMins['mins'];
						}else{
							$totalAbsentHrs = 0;
							$totalAbsentHrs = 0;
							$totalAbsentMins = 0;
						}
					}
					$c->push([
						'emp_id' => $employee->id,
						'emp_name' => $employee->firstname.' '.$employee->lastname,
						'date' => Carbon::parse($fromDate)->format('Y-m-d'),
						'date_string' => Carbon::parse($fromDate),
						'clock_in' =>  $clockInAm.' | '.$clockOutAm,
						'clock_out' => $clockInPm .' | '.$clockOutPm,
						'late' => $lateAm.' | '.$latePm,
						'job' => $employee->designation_id,
						'emp_code'=>$employee->emp_code,
						'designation' => $employee->designations['designation_name'],
						'absent' => $absent,
						// 'absent1' => $totalAbsent.' | '.$totalAbsentHrs.' '.$totalAbsentMins,
						'total_tardi' => sprintf('%02d:%02d:%02d', $totalHrs, $totalMins,0),
						'total_absent' => sprintf('%02d:%02d:%02d',$totalAbsentHrs, $totalAbsentMins,0),
						'total_time_deduction' => sprintf('%02d:%02d:%02d',$totalHrs + $totalAbsentHrs, $totalMins + $totalAbsentMins,0),
						// 'salary' => $totalDeductions
						// 'salary' => number_format($s,2)
					]);
					$fromDate->addDays(1);
				}
				
				
			}
			$attendances = $c->groupBy('emp_name');
			// dd($attendances);
			// $attendances = $c->groupBy('emp_name');
			$mode = 'ampm';
			// $atts = $c;
			//dd($atts);
			return view('admin.dtr.print_new',compact('attendances','fromPass','toPass','department','diffs','mode'));
		}else{
			set_time_limit(0);
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
								->where('time_table_alias','')
								->first();
					$clockOut = DB::connection('pgsql_external')->table('att_payloadtimecard')
								->select('emp_id','clock_out','att_date')
								->whereDate('att_date',Carbon::parse($from))
								->where('emp_id',$value->emp_id)
								->where('time_table_alias','')
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
			$mode = 'wholeday';
			$atts = $c;
			//dd($atts);
			return view('admin.dtr.print_new',compact('attendances','fromPass','toPass','department','diffs','mode'));
		}
	}
	function undertime($parseout,$margin){
		// (Undertime)
		$undertime = 0;
		$parseOutAm = Carbon::parse($parseout)->format('H:i');
		$minutesOut = Carbon::parse($parseOutAm)->format('H') * 60 + Carbon::parse($parseOutAm)->format('i');
		if($minutesOut < $margin){
			$undertime  = $margin - $minutesOut;
		}else{
			$undertime  = 0;
		}

		return $undertime;
	}
	function totalAbsences($input) {
		if (is_numeric($input)) {
			if (strpos($input, '.') !== false) {
				$parts = explode('.', $input);
				$part1 = intval($parts[0]) * 8;
				$part2 = 4;


			} else {
				$part1 = intval($input) * 8;
				$part2 = 0;
			}
		} else {
			$part1 = 0;
			$part2 = 0;
		}

		$hrs = $part1 + $part2;
		$mins = 0;

		$result = collect(['hrs'=>$hrs,'mins'=>$mins]);
		return $result;
	}
	
	function totaltardi($totalTardyPerDate){
		$hours = floor($totalTardyPerDate / 60); // Calculate the number of whole hours
		$minutes = $totalTardyPerDate % 60; // Calculate the remaining minutes
		$seconds = 0; // Set seconds to 0

		$result = collect(['hrs'=>$hours,'mins'=>$minutes,'secs'=>$seconds]);
		return $result;
	}
	function salaryPerMinute($salary,$year,$month){
		$workingMinutes = $this->getTotalWorkingDaysInMonth($year,$month) * 8 * 60;  // Sample November 2022 -> 22 Working days
		if($workingMinutes == 0){
			return 0;
		}
        $salaryPerMinute = $salary / $workingMinutes;

		return $salaryPerMinute;
	}
	function getTotalWorkingDaysInMonth($year, $month)
	{
		$totalWorkingDays = 0;
		$startOfMonth = Carbon::createFromDate($year, $month, 1);
		$endOfMonth = $startOfMonth->copy()->endOfMonth();

		for ($date = $startOfMonth; $date <= $endOfMonth; $date->addDay()) {
			if ($this->isWorkingDay($date->format('Y-m-d'))) {
				$totalWorkingDays++;
			}
		}

		return $totalWorkingDays;
	}
	function isWorkingDay($date)
	{
		// Create a Carbon instance from the date string
		$carbonDate = Carbon::createFromFormat('Y-m-d', $date);

		// Check if it's a weekend (Saturday or Sunday)
		if ($carbonDate->isWeekend()) {
			return false; // Weekend, not a working day
		}

		return true; // It's a working day
	}
	function checkHoliday($date){
		$parse = Carbon::parse($date);
		$parse1 = $parse->format('Y-m-d');
		$parseYear = Carbon::parse($date)->format('Y');
		$leave = DB::table('holiday')
		->where('year', $parseYear)
		->where(function ($query) use ($parse1) {
		    $query->whereDate('date_from', '<=', $parse1)
		        ->whereDate('date_to', '>=', $parse1);
		})
		->get();
		if($leave->isEmpty()){
		    return 'Absent';
		}else{
		    return 'Holiday';
		}
	}
	function checkLeave($id,$date){
		$parse = Carbon::parse($date);
		$parse1 = $parse->format('Y-m-d H:i:s');
		$leave = DB::table('leaves')
		->where('employee_id', $id)
		->where(function ($query) use ($parse1) {
		    $query->whereDate('datefrom', '<=', $parse1)
		        ->whereDate('dateto', '>=', $parse1);
		})
		->get();
		if($leave->isEmpty()){
		    return 'Absent';
		}else{
		    return 'Leave';
		}
	}
	function getAmInOut($date,$id){
		
		$formatDate = $date->format('Y-m-d');
		$timeAM = DB::connection('pgsql_external')->table('att_payloadtimecard')
		->select('emp_id','clock_in','clock_out','att_date')
		->whereDate('att_date',Carbon::parse($formatDate))
		->where('emp_id',$id)
		// ->where('emp_id',258)
		->where('time_table_alias','am')
		->first();
		
		return $timeAM;
	}
	function getPmInOut($date,$id){
		$formatDate = $date->format('Y-m-d');
		$timePM = DB::connection('pgsql_external')->table('att_payloadtimecard')
		->select('emp_id','clock_in','clock_out','att_date')
		->whereDate('att_date',Carbon::parse($formatDate))
		->where('emp_id',$id)
		// ->where('emp_id',258)
		->where('time_table_alias','pm')
		->first();

		return $timePM;
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
