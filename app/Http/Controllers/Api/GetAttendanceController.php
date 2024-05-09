<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use stdClass;
use Carbon\Carbon;


class GetAttendanceController extends Controller
{
    public function index(){
		$bioTimeAttendances = DB::connection('pgsql_external')->table('att_payloadtimecard')->where('att_date',date('Y-m-d'))->get();
        $biotimeAttendance = [];
        $totaltime = 0;
        $datess = new stdClass();
        foreach($bioTimeAttendances as $bioTimeAttendance){
            // dd(gettype(json_encode(Carbon::parse($bioTimeAttendance->clock_out)->format('Y-m-d H:i:s'))));
			$e = DB::connection('pgsql_external')->table('personnel_employee')->select('id','emp_code')->where('id',$bioTimeAttendance->emp_id)->first();
			$employeeCode = $e->emp_code;			
            $datess->in = Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d H:i:s');
            $datess->out = Carbon::parse($bioTimeAttendance->clock_out)->format('Y-m-d H:i:s');
            $myDate = json_encode($datess);
            $datess->in = Carbon::parse($datess->in);
            $datess->out = Carbon::parse($datess->out);
            $in = json_decode(Carbon::parse($bioTimeAttendance->clock_in)->format('Y-m-d H:i:s'));
            $out = json_decode(Carbon::parse($bioTimeAttendance->clock_out)->format('Y-m-d H:i:s'));
            $totaltime += $datess->out->diffInMinutes($datess->in);
            DB::table('attendance_summaries')->insert([
                'employee_id' => $employeeCode,
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
		return response($bioTimeAttendances,200);
	}
    // public function showImage($code){
    //     $photoPath = public_path('assets/photos/' . $code . '.jpg');
    //     $photo_url = file_exists($photoPath) ? asset('photos/' . $code . '.jpg') : null;

    //     return response()->json($photo_url);
    // }
    public function showImage($code){
        dd(1);
        // $photoPath = public_path('assets/photos/' . $code . '.jpg');

        // // Check if the file exists
        // if (file_exists($photoPath)) {
        //     // Return the image file with appropriate headers
        //     return response()->file($photoPath);
        // } else {
        //     // Image not found, return appropriate response
        //     return response()->json(['error' => 'Image not found'], 404);
        // }
    }

}
