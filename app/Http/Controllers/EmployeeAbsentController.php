<?php

namespace App\Http\Controllers;

use DB;
use App\Employee;
use Carbon\Carbon;
use App\AttendanceSummary;
use Illuminate\Http\Request;

class EmployeeAbsentController extends Controller
{
    public function absentEmployee(){
        // $e = DB::table('employees')
        // ->join('attendance_summaries','employees.id','=','attendance_summaries.employee_id')
        // ->select('employees.id')
        // ->get();
        $employees = DB::table('employees')->select('*')->whereNotIn('id',function($query){
            $query->select('employee_id')->from('attendance_summaries');
        })->get();
        $employees = json_decode($employees,true);
        $l = Employee::all();
        $today = Carbon::now()->toDateString();
        return view('admin.absents.showabsent',[
            'employees'=>$employees,
            'today'    => $today,
        ]);
    }
}
