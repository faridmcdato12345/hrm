<?php

namespace App\Http\Controllers;

use DB;
use App\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeAttendanceController extends Controller
{
    public function getEmployeeAttendanceUnderSupervisory(){
        $e = DB::connection('pgsql_external')->select('select emp_id,clock_in,clock_out from att_payloadtimecard');
        $employee = Employee::all();
        dd(Carbon::parse($e[0]->clock_in));
        return view('admin.employees.supervisory.index')->with('employees',$employee)->with('attendances',$e);
    }
}
