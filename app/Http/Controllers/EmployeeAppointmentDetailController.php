<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Appointment;
use App\Designation;
use Illuminate\Http\Request;

class EmployeeAppointmentDetailController extends Controller
{
    public function show(Employee $employee){
        $appointments = Appointment::with('designationsFrom','designationsTo','employees')
        ->where('employee_id',$employee->id)
        ->get();
        $employees = Employee::where('id',$employee->id)->get();
        return view('admin.employees.appointment.detail.show')
        ->with('appointments',$appointments)
        ->with('employees',$employees);
    }
}
