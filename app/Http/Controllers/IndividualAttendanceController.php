<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndividualAttendanceController extends Controller
{
    public function index()
    {
        $present = 0;
        $absent = 0;
        $rendered = 0;
        $late = 0;
        $leaves = 0;
        $datefrom = Carbon::now()->format('Y-m-d');
        $dateto = Carbon::now()->format('Y-m-d');

        $employees = DB::table('employees')
                ->select('emp_id', DB::raw("CONCAT(firstname, ' ', lastname) as full_name"))
                ->where('id','!=',1)
                ->orderBy('firstname','asc')
                ->get();

        $employee = 0;
        $output = collect();
        return view('admin.attendance.dtr_individual', compact('present', 'absent', 'rendered', 'late', 'leaves','datefrom','dateto','employees','employee','output'));
    }
    public function showAttendance(Request $request){
        $employees = DB::table('employees')
                ->select('emp_id', DB::raw("CONCAT(firstname, ' ', lastname) as full_name"))
                ->where('id','!=',1)
                ->orderBy('firstname','asc')
                ->get();
        $employee = DB::table('employees')
            ->select('emp_id', DB::raw("CONCAT(firstname, ' ', lastname) as full_name"))
            ->where('emp_id',$request->emp_id)
            ->orderBy('firstname','asc')
            ->get();
        $datefrom = $request->datefrom;
        $dateto = $request->dateto;
        $startDate = Carbon::parse($request->datefrom);
		$endDate = Carbon::parse($request->dateto);
        $count = $startDate->diffInDays($endDate) + 1;
        $rendered = 0;
        $present = 0;
        $absent = 0;
        $late = 0;
        $leaves = 0;
        $standardAm = Carbon::parse('08:00')->format('H:i');
        $standardPm = Carbon::parse('13:00')->format('H:i');
        $gracPeriodAm = Carbon::parse('08:15')->format('H:i');
        $gracPeriodPm = Carbon::parse('13:15')->format('H:i');
        $marginOutAm = Carbon::parse('12:00')->format('H:i');
        $marginOutPm = Carbon::parse('17:00')->format('H:i');
        $output = collect();
        // ------------------------------------------------------------------------- hererererererererer dateeeeeeeeeeeeeeee----------------------
        for($i=0;$i < $count;$i++){
             // get Am In and Out
            $amInOut = $this->getAmInOut($startDate, $request->emp_id); // pgsql query
            // // get Pm In and Out
            $pmInOut = $this->getPmInOut($startDate, $request->emp_id); // pgsql query
            $clockInAm = '';
			$clockOutAm = '';
			$clockInPm = '';
			$clockOutPm = '';
            $leave1 = '';
            $holiday = '';
            $amIs = '';
            $pmIs = '';
            $forcalcAmin = '';
            $forcalcPmin = '';
            $diffam = 0;
            $diffpm = 0;
            $newDate = '';
            $indRendered = 0;
            $indLate = 0;
            if (empty($amInOut)) { // either Holiday,leave,weekends or Absent
                //Check Holiday
                $holiday = $this->checkHoliday($startDate);
                if($holiday === 'Holiday'){
                    $clockInAm = $holiday;
                    $clockOutAm = $holiday;
                    $present += 0.5;
                    $amIs = $holiday;
                }
                if($holiday === 'Absent'){
                    // Check Leave
                    $leave1 = $this->checkLeave($request->emp_id,$startDate); // hr query
                    if($leave1 === 'Leave'){
                        $leaves += 0.5;
                    }else{
                        $absent += 0.5;
                    }
                    $clockInAm = $leave1;
                    $clockOutAm = $leave1;
                    $amIs = $holiday;
                }
                if(Carbon::parse($startDate)->isWeekend()){
                    $clockInAm = 'Weekend';
                    $clockOutAm = 'Weekend';
                    $absent -= 0.5;
                }
            }
            if (empty($pmInOut)) { // either Holiday,leave,weekends or Absent
                //Check Holiday
                $holiday = $this->checkHoliday($startDate);
                if($holiday === 'Holiday'){
                    $clockInPm = $holiday;
                    $clockOutPm = $holiday;
                    $present += 0.5;
                    $pmIs = $holiday;
                }
                if($holiday === 'Absent'){
                    // Check Leave
                    $leave1 = $this->checkLeave($request->emp_id,$startDate); // hr query
                    if($leave1 === 'Leave'){
                        $leaves += 0.5;
                    }else{
                        $absent += 0.5;
                    }
                    $clockInPm = $leave1;
                    $clockOutPm = $leave1;
                    $pmIs = $holiday;
                }
                if(Carbon::parse($startDate)->isWeekend()){
                    $clockInPm = 'Weekend';
                    $clockOutPm = 'Weekend';
                    $absent -= 0.5;
                }
                
            }
            // missing Punch In or Out (Am/Pm)
            if ($clockInAm == '' && $amInOut->clock_in == NULL) {
                $clockInAm = 'No Check-In';
                $late += 120;
                $rendered += 120;
                $indRendered += 120;
                $indLate += 120;
                if($amInOut->clock_out != NULL){
                    $clockOutAm = Carbon::parse($amInOut->clock_out)->format('H:i');
                }
            }
            if ($clockOutAm == '' && $amInOut->clock_out == NULL) {
                $clockOutAm = 'No Check-Out';
                $late += 120;
                $rendered += 120;
                $indRendered += 120;
                $indLate += 120;
                if($amInOut->clock_in != NULL){
                    $clockInAm = Carbon::parse($amInOut->clock_in)->format('H:i');
                }
            }
            if ($clockInPm == '' && $pmInOut->clock_in == NULL) {
                $clockInPm = 'No Check-In';
                $late += 120;
                $rendered += 120;
                $indRendered += 120;
                $indLate += 120;
                if($pmInOut->clock_out != NULL){
                    $clockOutPm = Carbon::parse($pmInOut->clock_out)->format('H:i');
                }
            }
            if ($clockOutPm == '' && $pmInOut->clock_out == NULL) {
                $clockOutPm = 'No Check-Out';
                $late += 120;
                $rendered += 120;
                $indRendered += 120;
                $indLate += 120;
                if($pmInOut->clock_in != NULL){
                    $clockInPm = Carbon::parse($pmInOut->clock_in)->format('H:i');
                }
            }
            // With punch (In and Out) for Am
            if(!empty($amInOut->clock_in) && $amInOut->clock_in != NULL && !empty($amInOut->clock_out) &&  $amInOut->clock_out != NULL){
                // Set time for clockin and clockout Am, and 24hr format for comparing.
                $clockInAm = Carbon::parse($amInOut->clock_in)->format('H:i');
                $clockOutAm = Carbon::parse($amInOut->clock_out)->format('H:i');
                // check if within grace period
                if($clockInAm <= $gracPeriodAm){
                    $forcalcAmin = $standardAm;
                }else{
                    $forcalcAmin = $clockInAm;
                }
                // check if overtime
                if($clockOutAm > $marginOutAm){
                    $diffam = Carbon::parse($forcalcAmin)->diffInMinutes(Carbon::parse($marginOutAm));
                }else{
                    $diffam = Carbon::parse($forcalcAmin)->diffInMinutes(Carbon::parse($clockOutAm));
                }
                $late += abs($diffam - 240);
                $indLate += abs($diffam - 240);
                $present += 0.5;
                $rendered += $diffam;
                $indRendered += $diffam;
            }
            // With punch (In and Out) for Pm
            if (!empty($pmInOut->clock_in) &&  $pmInOut->clock_in != NULL && !empty($pmInOut->clock_out) &&  $pmInOut->clock_out != NULL) {
                // Set time for clockin and clockout Pm, and 24hr format for comparing.
                $clockInPm = Carbon::parse($pmInOut->clock_in)->format('H:i');
                $clockOutPm = Carbon::parse($pmInOut->clock_out)->format('H:i');
                // check if within grace period
                if($clockInPm <= $gracPeriodPm){
                    $forcalcPmin = $standardPm;
                }else{
                    $forcalcPmin = $clockInPm;
                }
                // check if overtime
                if($clockOutPm > $marginOutPm){
                    $diffpm = Carbon::parse($forcalcPmin)->diffInMinutes(Carbon::parse($marginOutPm));
                }else{
                    $diffpm = Carbon::parse($forcalcPmin)->diffInMinutes(Carbon::parse($clockOutPm));
                }
                
                $late += abs($diffpm - 240);
                $indLate += abs($diffpm - 240);
                $present += 0.5;
                $rendered += $diffpm;
                $indRendered += $diffpm;

            }
            $output->push([
                'num'=> $i,
                'id'=> $request->emp_id,
                'name'=> $employee->first()->full_name,
                'am_clock_in'=> (strtotime($clockInAm) !== false) ? Carbon::parse($clockInAm)->format('H:i A') : $clockInAm,
                'am_clock_out'=> (strtotime($clockOutAm) !== false) ? Carbon::parse($clockOutAm)->format('H:i A') : $clockOutAm,
                'pm_clock_in'=> (strtotime($clockInPm) !== false) ? Carbon::parse($clockInPm)->format('h:i A') : $clockInPm,
                'pm_clock_out'=> (strtotime($clockOutPm) !== false) ? Carbon::parse($clockOutPm)->format('h:i A') : $clockOutPm,
                'rendered'=> $indRendered,
                'late'=> $indLate,
                'att_date_am'=> (!empty($amInOut) ? $amInOut->id : NULL),
                'att_date_pm'=> (!empty($pmInOut) ? $pmInOut->id : NULL),
                'date'=>  Carbon::parse($startDate)->format('Y-m-d'),
            ]);
            $startDate->addDays(1);
        }
        return view('admin.attendance.dtr_individual', compact('output','present','absent','rendered','late','leaves','employees','datefrom','dateto'));
        
    }
    public function edit(Request $request){
        $formatDate = Carbon::parse($request->date)->format('Y-m-d');
		$timeAM = DB::connection('pgsql_external')->table('att_payloadtimecard')
		->whereDate('att_date',Carbon::parse($formatDate))
		->where('emp_id',$request->id)
        ->where('time_table_alias', 'am');

        $timePM = DB::connection('pgsql_external')->table('att_payloadtimecard')
		->whereDate('att_date',Carbon::parse($formatDate))
		->where('emp_id',$request->id)
        ->where('time_table_alias', 'pm');

        $dateWeek = Carbon::parse($formatDate);
        $yearStart = Carbon::createFromDate($dateWeek->year, 1, 1);
        $week = $yearStart->diffInWeeks($dateWeek);
        $weekday = $dateWeek->dayOfWeekIso - 1;

        if(isset($request->amin)){
            if($timeAM->first()){
                $timeAM->update(['clock_in'=>$request->date.' '.$request->amin.'+08']);
            }else{
                $uuidAM = $this->generateCustomUuid('amin'.$request->id);
                $check_inAm = Carbon::parse($formatDate.' 08:00:00 +08:00');
                $check_inAm = $check_inAm->format('Y-m-d H:i:sP');
                $check_outAm = Carbon::parse($formatDate.' 12:00:00 +08:00');
                $check_outAm = $check_outAm->format('Y-m-d H:i:sP');
                $timeAM->insert([
                    'id'=> $uuidAM,
                    'att_date'=> $formatDate,
                    'week'=> $week,
                    'weekday'=> $weekday,
                    'date_type'=> 1,
                    'time_table_alias'=> 'am',
                    'check_in'=> $check_inAm,
                    'check_out'=>$check_outAm,
                    'work_day'=> '1.0',
                    'clock_in'=> $request->date.' '.$request->amin.'+08',
                    'clock_out'=> NULL,
                    'break_out'=> NULL,
                    'break_in'=> NULL,
                    'lock_down'=> 'false',
                    'emp_id'=>$request->id,
                    'time_table_id'=> 4,
                ]);
            }

        }
        if(isset($request->amout)){
            if($timeAM->first()){
                $timeAM->update(['clock_out'=>$request->date.' '.$request->amout.'+08']);
            }else{
                $uuidAM = $this->generateCustomUuid('amout'.$request->id);
                $check_inAm = Carbon::parse($formatDate.' 08:00:00 +08:00');
                $check_inAm = $check_inAm->format('Y-m-d H:i:sP');
                $check_outAm = Carbon::parse($formatDate.' 12:00:00 +08:00');
                $check_outAm = $check_outAm->format('Y-m-d H:i:sP');
                $timeAM->insert([
                    'id'=> $uuidAM,
                    'att_date'=> $formatDate,
                    'week'=> $week,
                    'weekday'=> $weekday,
                    'date_type'=> 1,
                    'time_table_alias'=> 'am',
                    'check_in'=> $check_inAm,
                    'check_out'=>$check_outAm,
                    'work_day'=> '1.0',
                    'clock_in'=> NULL,
                    'clock_out'=> $request->date.' '.$request->amout.'+08',
                    'break_out'=> NULL,
                    'break_in'=> NULL,
                    'lock_down'=> 'false',
                    'emp_id'=>$request->id,
                    'time_table_id'=> 4,
                ]);
            }
        }
        if(isset($request->pmin)){
            if($timePM->first()){
                $timePM->update(['clock_in'=>$request->date.' '.$request->pmin.'+08']);
            }else{
                $uuidPM = $this->generateCustomUuid('pmin'.$request->id);
                $check_inPm = Carbon::parse($formatDate.' 13:00:00 +08:00');
                $check_inPm = $check_inPm->format('Y-m-d H:i:sP');
                $check_outPm = Carbon::parse($formatDate.' 17:00:00 +08:00');
                $check_outPm = $check_outPm->format('Y-m-d H:i:sP');
                $timePM->insert([
                    'id'=> $uuidPM,
                    'att_date'=> $formatDate,
                    'week'=> $week,
                    'weekday'=> $weekday,
                    'date_type'=> 1,
                    'time_table_alias'=> 'pm',
                    'check_in'=> $check_inPm,
                    'check_out'=>$check_outPm,
                    'work_day'=> '1.0',
                    'clock_in'=> $request->date.' '.$request->pmin.'+08',
                    'clock_out'=> NULL,
                    'break_out'=> NULL,
                    'break_in'=> NULL,
                    'lock_down'=> 'false',
                    'emp_id'=>$request->id,
                    'time_table_id'=> 3,
                ]);
            }
        }
        if(isset($request->pmout)){
            if($timePM->first()){
                $timePM->update(['clock_out'=>$request->date.' '.$request->pmout.'+08']);
            }else{
                $uuidPM = $this->generateCustomUuid('pmout'.$request->id);
                $check_inPm = Carbon::parse($formatDate.' 13:00:00 +08:00');
                $check_inPm = $check_inPm->format('Y-m-d H:i:sP');
                $check_outPm = Carbon::parse($formatDate.' 17:00:00 +08:00');
                $check_outPm = $check_outPm->format('Y-m-d H:i:sP');
                $timePM->insert([
                    'id'=> $uuidPM,
                    'att_date'=> $formatDate,
                    'week'=> $week,
                    'weekday'=> $weekday,
                    'date_type'=> 1,
                    'time_table_alias'=> 'pm',
                    'check_in'=> $check_inPm,
                    'check_out'=>$check_outPm,
                    'work_day'=> '1.0',
                    'clock_in'=> NULL,
                    'clock_out'=> $request->date.' '.$request->pmout.'+08',
                    'break_out'=> NULL,
                    'break_in'=> NULL,
                    'lock_down'=> 'false',
                    'emp_id'=>$request->id,
                    'time_table_id'=> 3,
                ]);
            }
        }

        return redirect()->route('individual.attendance')->with('success', 'Your update was successful!');
    }
    public function delete(Request $request){
        $formatDate = Carbon::parse($request->date)->format('Y-m-d');
		$queryAM = DB::connection('pgsql_external')->table('att_payloadtimecard')
		->whereDate('att_date',Carbon::parse($formatDate))
		->where('emp_id',$request->id)
        ->where('time_table_alias','am');

        $queryPM = DB::connection('pgsql_external')->table('att_payloadtimecard')
		->whereDate('att_date',Carbon::parse($formatDate))
		->where('emp_id',$request->id)
        ->where('time_table_alias','pm');

        if($request->att_am_id !== NULL){
            $queryAM->where('id',$request->att_am_id);
            $queryAM->delete();
        }
        if($request->att_pm_id !== NULL){
            $queryPM->where('id',$request->att_pm_id);
            $queryPM->delete();
        }
        return redirect()->route('individual.attendance')->with('success', 'Succesfully Deleted');
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
    function getAmInOut($date,$id){
		$formatDate = $date->format('Y-m-d');
		$timeAM = DB::connection('pgsql_external')->table('att_payloadtimecard')
		->select('emp_id','clock_in','clock_out','att_date','id')
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
		->select('emp_id','clock_in','clock_out','att_date','id')
		->whereDate('att_date',Carbon::parse($formatDate))
		->where('emp_id',$id)
		// ->where('emp_id',258)
		->where('time_table_alias','pm')
		->first();

		return $timePM;
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
}
