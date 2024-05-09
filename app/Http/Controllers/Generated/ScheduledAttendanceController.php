<?php

namespace App\Http\Controllers\Generated;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use stdClass;

class ScheduledAttendanceController extends Controller
{
    public function index(){
        $bioTimeAttendances = DB::connection('pgsql_external')->select('select * from att_payloadtimecard');
        $datess = new stdClass();
        $totaltime = 0;
        //dd(Carbon::parse(date("Y/m/d"))->format('Y-m-d'));
        foreach($bioTimeAttendances as $bioTimeAttendance){
           //dd(Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d'));
            if(Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d') >= Carbon::parse(date("Y/m/d"))->format('Y-m-d')){
                // dd("running if");
                $datess->in = Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d H:i:s');
                $datess->out = Carbon::parse($bioTimeAttendance->clock_out)->format('Y-m-d H:i:s');
                $myDate = json_encode($datess);
                $datess->in = Carbon::parse($datess->in);
                $datess->out = Carbon::parse($datess->out);
                $in = json_decode(Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d H:i:s'));
                $out = json_decode(Carbon::parse($bioTimeAttendance->clock_out)->format('Y-m-d H:i:s'));
                $totaltime += $datess->out->diffInMinutes($datess->in);
                $l = DB::table('attendance_summaries')->select('*')->get();
                DB::table('attendance_summaries')->insert([
                    'employee_id' => $bioTimeAttendance->emp_id,
                    'first_timestamp_in' => $datess->in,
                    'last_timestamp_out' => $datess->out,
                    'total_time' => $totaltime,
                    'date' => $bioTimeAttendance->att_date,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'status' => 'present',
                    'is_delay' => 'no'
                ]);
            }
        }
    }

    public function manualStoring(Request $request){
        $bioTimeAttendances = DB::connection('pgsql_external')->select('select * from att_payloadtimecard');
        $datess = new stdClass();
        $totaltime = 0;
        foreach($bioTimeAttendances as $bioTimeAttendance){
            if(Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d') == $request->date){
                $datess->in = Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d H:i:s');
                $datess->out = Carbon::parse($bioTimeAttendance->clock_out)->format('Y-m-d H:i:s');
                $myDate = json_encode($datess);
                $datess->in = Carbon::parse($datess->in);
                $datess->out = Carbon::parse($datess->out);
                $in = json_decode(Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d H:i:s'));
                $out = json_decode(Carbon::parse($bioTimeAttendance->clock_out)->format('Y-m-d H:i:s'));
                $totaltime += $datess->out->diffInMinutes($datess->in);
                DB::table('attendance_summaries')->insert([
                    'employee_id' => $bioTimeAttendance->emp_id,
                    'first_timestamp_in' => $datess->in,
                    'last_timestamp_out' => $datess->out,
                    'total_time' => $totaltime,
                    'date' => $bioTimeAttendance->att_date,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'status' => 'present',
                    'is_delay' => 'no'
                ]);
            }
        }
        return "Done generating attendance";
    }
    public function manualIndex(){
        return view('admin.attendance.manual');
    }
}
