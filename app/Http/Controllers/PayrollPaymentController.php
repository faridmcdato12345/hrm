<?php

namespace App\Http\Controllers;

use App\Department;
use App\Employee;
use App\Loan;
use App\LoanManagement;
use App\LoanType;
use Illuminate\Http\Request;
use DB;

class PayrollPaymentController extends Controller
{
    public function index(){
        $departments = Department::all();
        return view('admin.payroll.index',compact('departments'));
    }
    public function generatePayroll(Request $request){
        $datas = collect();
        $month = date($request->month);
        if($request->date == 15){
            $to = $month . '-15';
            $from = $month . '-01';
        }
        else{
            $to = $month . '-30';
            $from = $month . '-16';
        }
        //$employees = Employee::all();
        $employees = Employee::with('department')->where('department_id',$request->department)
        ->leftJoin('attendance_summaries',function($join) use ($from,$to){
            $join->on('employees.id','=','attendance_summaries.employee_id')
            ->whereBetween('attendance_summaries.first_timestamp_in',[$from,$to])
            ->whereBetween('attendance_summaries.last_timestamp_out',[$from,$to]);
        })
        ->leftJoin('employee_memberships',function($join){
            $join->on('employees.id','=','employee_memberships.employee_id');
        })
        ->leftJoin('leaves',function($join) use ($from,$to){
            $join->on('employees.id','=','leaves.employee_id')
            ->where('leaves.status','=','approved')
            ->whereBetween('leaves.datefrom',[$from,$to])
            ->whereBetween('leaves.dateto',[$from,$to]);
        })
        ->select('employees.id','employees.firstname','employees.lastname','first_timestamp_in','last_timestamp_out','department_id','social_welfare_id as membership_sw_employee_id','membership_id','datefrom as leave_date_from','dateto as leave_date_to')
        ->get();
        // $datas->push($employees->toArray());
        // foreach($datas as $data){
            
        // }
        // dd($employees->toArray());
        //$e = $employees->toArray();
        $grouped = $employees->groupBy('id');
        dd($grouped->all());
        $loans = Loan::where('status',1)->get();
        $loanms = LoanManagement::all();
        return view('admin.printable.payroll',compact('employees','loans','loanms'));
    }
}
