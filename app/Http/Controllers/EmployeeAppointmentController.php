<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Appointment;
use App\Designation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class EmployeeAppointmentController extends Controller
{
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$data = Employee::with('branch', 'department')->where('status', '!=', '0')->get();
        $active_employees = Employee::where('status', '1')->count();
        return view('admin.employees.appointment', ['title' => 'All Employees'])
            ->with('employees', $data)
            ->with('active_employees', $active_employees)
            ->with('designations', Designation::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
			'employee_id' => 'required',
			'from_designation_id' => 'required',
			'to_designation_id' => 'required',
            'created_at' => 'required',
            'document' => 'nullable'
		]);
        $arr = [
            'employee_id' => $request->employee_id,
            'to_designation_id' => $request->to_designation_id,
            'from_designation_id' => $request->from_designation_id,
            'created_at' => $request->created_at,
        ];
        if ($request->document != '') {
            $document = time().'_'.$request->document->getClientOriginalName();
            $path = 'storage/employees/appointment';
            if(!Storage::exists($path)){
                Storage::makeDirectory($path, 0777, true, true);
        // retry storing the file in newly created path.
            }   
            $request->document->move('storage/employees/appointment/', $document);
            $arr['document'] = 'storage/employees/appointment/'.$document;
        }
		Appointment::create($arr);
		$employee = Employee::findOrFail($request->employee_id);
		$employee->designation_id = $request->to_designation_id;
		
        // new
        if($employee->save()){
            DB::table('designation_sg')
            ->where('emp_id',$request->employee_id)
            ->update(['active'=>0]);

            // Update Employee Salary
            DB::table('employees')
            ->where('id',$request->employee_id)
            ->update(['basic_salary'=>$request->salary]);

            DB::table('designation_sg')->insert([
                'designation_id' => $request->to_designation_id,
                'emp_id' => $request->employee_id,
                'active'=> 1,
                'salary'=>$request->salary,
                'created_at'=>$request->created_at,
            ]);
        }
        Session::flash('success', 'Employee appointment is saved');
        return redirect()->route('appointment.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
		$employees = Employee::with('designations')->where('id',$employee->id)->get();  
        $designations = Designation::all();
        return view('admin.employees.employee_appointment_show',compact('employees','designations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
