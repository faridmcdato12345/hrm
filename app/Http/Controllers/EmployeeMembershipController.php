<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Membership;
use App\SocialWelfare;
use App\EmployeeMembership;
use Illuminate\Http\Request;
use Session;

class EmployeeMembershipController extends Controller
{
    
    public function createMembershipEmployee($id){
        $memberships = Membership::where('social_welfare_id',$id)->get();
        $output = '';
        foreach($memberships as $membership){
            $output .= "<option value=".$membership->id.">".$membership->name."</option>";
        }
        $data = array(
            'dataTable' => $output,
        );
        return json_encode($data);
    }
    public function addMembershipEmployee($id,Request $request){
        $employee = Employee::findOrFail($id);
        EmployeeMembership::create([
            'employee_id' => $id,
            'social_welfare_id' => $request->social_welfare_id,
            'membership_id' => $request->membership_id
        ]);
        $sw = SocialWelfare::findOrfail($request->social_welfare_id);
        $mm = Membership::findOrFail($request->membership_id);
        Session::flash('success', $employee->firstname .' '.$employee->lastname .' is successfully a member of '.$sw->name.'('.$mm->name.')');
        return redirect()->route('employees')->with($employee->firstname .' '.$employee->lastname . ' is successfully added');
    }
    public function showMembershipEmployee($id){
        $memberships = EmployeeMembership::where('employee_id',$id)->get();
        return json_encode($memberships);
    }
}
