<?php

namespace App\Http\Controllers;

use App\Loan;
use App\Employee;
use App\LoanType;
use Carbon\Carbon;
use App\Membership;
use App\EmployeeLoan;
use App\SocialWelfare;
use App\LoanManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
class EmployeeLoanController extends Controller
{
    public function getLoan($id){
        $loans = Loan::where('social_welfare_id',$id)->get();
        $output = '';
        foreach($loans as $loan){
            $output .= '<option value="'.$loan->id.'">'.$loan->name.'</option>';
        }
        $data = array(
            'dataTable' => $output,
        );
        return json_encode($data);
    }
    public function postLoanCategory(Request $request){
        $loans = LoanType::with('loans')->where('loan_id',$request->id)->where('status',1)->get();
        $output = '<option selected>Choose '.$loans[0]->loans->name.' category here...</option>';
        foreach($loans as $loan){
            $output .= '<option value="'.$loan->id.'">'.$loan->name.'</option>';
        }
        $data = array(
            'dataTable' => $output,
        );
        return json_encode($data);
    }
    public function addLoanToEmployee(Request $request, $id){
        
        $employee = Employee::findOrFail($id);
        EmployeeLoan::create([
            'employee_id' => $employee->id,
            'loan_id' => $request->loan_id,
            'social_welfare_id' => $request->social_welfare_id,
            'amount' => $request->amount,
            'monthly_deduction' => $request->monthly_deduction,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        Session::flash('success', $employee->firstname .' '.$employee->lastname .' loan is successfully saved.');
        return redirect()->route('employees');
    }
    public function showEmployeeLoans($id){
        $employeeLoans = DB::table('employees_loan_managements')->where('employee_id',$id)->get();
        $output = '';
        if($employeeLoans->isEmpty()){
            $output .= '<tr>';
            $output .= '<td>No found loan for this employee</td>';
            $output .= '</tr>';
        }
        else{
            foreach($employeeLoans as $e){
                if($e->amount == null){
                    $e->amount = 'Null';
                }
                $employee = Employee::where('id',$id)->first();
                $l = LoanManagement::where('id',$e->loan_id)->first();
                $output .= '<tr>';
                $output .= '<td>'.$employee->firstname.' '.$employee->lastname.'</td>';
                $output .= '<td>'.$l->socialWelfares->name.'('.$l->name.')'.'</td>';
                $output .= '<td>'.$e->amount.'</td>';
                $output .= '</tr>';
            }
        }
        $data = array(
            'dataTable' => $output,
        );
        return json_encode($data);
    }
}   
