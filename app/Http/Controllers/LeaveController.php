<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use App\Leave;
use App\Branch;
use App\Employee;
use App\LeaveType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Traits\MetaTrait;
use App\AttendanceSummary;
use App\EmployeeLeaveType;
use Illuminate\Http\Request;
use App\OrganizationHierarchy;
use App\TotalEarnedLeaves;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LeaveController extends Controller
{
    use MetaTrait;

    public $leave_types = [
        'unpaid_leave' => 'Unpaid Leave',
        'half_leave'   => 'Half Leave',
        'short_leave'  => 'Short Leave',
        'paid_leave'   => 'Paid Leave',
        'sick_leave'   => 'Sick Leave',
        'casual_leave' => 'Casual Leave',
    ];

    public $statuses = [
        'pending'  => 'Pending',
        'approved' => 'Approved',
        'declined' => 'Declined',
    ];
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee = Auth::User();
        $this->meta['title'] = 'Show Leaves';
        $leaves = Leave::where('employee_id',$employee->id)->with('leaveType')->get();
        $consumed_leaves = 0;
        if ($leaves->count() > 0) {
            foreach ($leaves as $leave) {
                $datefrom = Carbon::parse($leave->datefrom);
                $dateto = Carbon::parse($leave->dateto);
                $consumed_leaves += $dateto->diffInDays($datefrom) + 1;
            }
        }

        return view('admin.leaves.showleaves', $this->metaResponse(), [
            'leaves'          => $leaves,
            'consumed_leaves' => $consumed_leaves,
            'employee'        => $employee,
        ]);
    }

    public function employeeleaves()
    {
        $data = Employee::where('status',1)->get();
        return view('admin.leaves.index', ['title' => 'Appointment'])
            ->with('employees', $data);
    }
    public function indexEmployee($id)
    {
        $this->meta['title'] = 'Show Leaves';
        $leaves = Leave::where('employee_id', $id)->get();

        return view('admin.leaves.employeeshowleaves', $this->metaResponse(), ['leaves' => $leaves]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id = Auth::User()->id;
        $this->meta['title'] = 'Create Leave';
        $OrganizationHierarchy = OrganizationHierarchy::where('employee_id', $id)->with('lineManager')->first();
        $employees = Employee::all();
        $line_manager = isset($OrganizationHierarchy->lineManager) ? $OrganizationHierarchy->lineManager : '';

        return view('admin.leaves.create', $this->metaResponse(), [
            'employees'    => $employees,
            'line_manager' => $line_manager,
            'leave_types'  => LeaveType::where('status', '1')->get(),
        ]);
    }

    public function adminCreate($id = '')
    {
        if ($id != '') {
            $employee_id = $id;
        } else {
            $employee_id = Auth::user()->id;
        }
        $this->meta['title'] = 'Create Leave';
        $OrganizationHierarchy = OrganizationHierarchy::where('employee_id', $employee_id)->with('lineManager')->first();
        $employees = Employee::where('status', '!=', '0')->orderBy('firstname')->get();
        $selectedEmployee = Employee::where('id', $employee_id)->first();
        $line_manager = isset($OrganizationHierarchy->lineManager) ? $OrganizationHierarchy->lineManager : '';

        return view('admin.leaves.admincreateleave', $this->metaResponse(), [
            'employees'        => $employees,
            'line_manager'     => $line_manager,
            'leave_types'      => LeaveType::where('status', '1')->get(),
            'selectedEmployee' => $selectedEmployee,
        ]);
    }

    public function EmployeeCreate()
    {
        $this->meta['title'] = 'Create Leave';
        $employees = Employee::all();

        return view('admin.leaves.create', $this->metaResponse(), ['employees' => $employees]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function adminStore(Request $request)
    {
        $this->validate($request, [
            'leave_type' => 'required',
            'datefrom'   => 'required',
            'dateto'     => 'required|after_or_equal:datefrom',
        ]);
        $employee_id = $request->employee;
        $leave_type = $request->leave_type;

        $dateFromTime = Carbon::parse($request->datefrom);
        $dateToTime = Carbon::parse($request->dateto);

        $consumed_leaves = $dateToTime->diffInDays($dateFromTime) + 1;

        $attendance_summaries = AttendanceSummary::where(['employee_id' => $employee_id])
            ->whereDate('date', '>=', $dateFromTime->toDateString())
            ->whereDate('date', '<=', $dateToTime->toDateString())
            ->get();

        if ($attendance_summaries->count() > 0) {
            $msg = '';
            foreach ($attendance_summaries as $key => $attendance_summary) {
                $msg .= ' '.$attendance_summary->date;
            }

            return redirect()->back()->with('error', 'Employee was already present on dates: '.$msg);
        }

        $leave = Leave::create([
            'employee_id'      => $employee_id,
            'leave_type'       => $leave_type,
            'datefrom'         => $dateFromTime,
            'dateto'           => $dateToTime,
            'subject'          => $request->subject,
            'description'      => $request->description,
			'count'			   => 1,
        ]);

        if ($leave) {
            return redirect()->route('employeeleaves')->with('success', 'Leave for Employee is created successfully');
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'datefrom' => 'required',
            'dateto'   => 'required|after_or_equal:datefrom',
        ]);
        $employee_id = Auth::User()->id;
        $leave_type = $request->leave_type;

        $dateFromTime = Carbon::parse($request->datefrom);
        $dateToTime = Carbon::parse($request->dateto);

        $consumed_leaves = $dateToTime->diffInDays($dateFromTime) + 1;
        $leave = Leave::create([
            'employee_id'      => $employee_id,
            'leave_type'       => $leave_type,
            'datefrom'         => $dateFromTime,
            'dateto'           => $dateToTime,
            'subject'          => $request->subject,
            'description'      => $request->description,
            'count'            => 1
        ]);
		return redirect()->route('leave.index')->with('success', 'Leave is created succesfully');
    }
    /**
     * Display the specified resource.
     *
     * @param \App\Leave $leave
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->meta['title'] = 'Leave Details';

        $leave = Leave::where(['id' => $id])->with([
            // $leave = Leave::find($id)->with([
            'employee',
            'lineManager',
            'pointOfContact',
            'leaveType',
        ])->first();

        $dateFromTime = Carbon::parse($leave->datefrom);
        $dateToTime = Carbon::parse($leave->dateto);
        $leave_days = $dateToTime->diffInDays($dateFromTime) + 1;
        $period = CarbonPeriod::create($dateFromTime, $dateToTime);

        $branch_id = $leave->employee->branch_id;
        $branch = Branch::find($branch_id);

        // Iterate over the period
        foreach ($period as $date) {
            if ($date->format('l') == 'Sunday') {
                $leave_days--;
            }
        }

        return view('admin.leaves.show', $this->metaResponse(), [
            'leave'      => $leave,
            'leave_days' => $leave_days,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Leave $leave
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->meta['title'] = 'Update Leave';

        $employee_id = Auth::User()->id;
        $employees = Employee::all();
        $leave = Leave::find($id);
        return view('admin.leaves.edit', $this->metaResponse(), [
            'employees'    => $employees,
            'leave_types'  => LeaveType::all(),
            'leave'        => $leave,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Leave               $leave
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $leave = Leave::find($id);

        $this->validate($request, [
            'datefrom' => 'required',
            'dateto'   => 'required|after_or_equal:datefrom',
        ]);

        $dateFromTime = Carbon::parse($request->datefrom);
        $dateToTime = Carbon::parse($request->dateto);

        $consumed_leaves = $dateToTime->diffInDays($dateFromTime) + 1;

        $attendance_summaries = AttendanceSummary::where(['employee_id' => $leave->employee_id])
            ->whereDate('date', '>=', $dateFromTime->toDateString())
            ->whereDate('date', '<=', $dateToTime->toDateString())
            ->get();

        if ($attendance_summaries->count() > 0) {
            $msg = '';
            foreach ($attendance_summaries as $key => $attendance_summary) {
                $msg .= ' '.$attendance_summary->date;
            }

            return redirect()->back()->with('error', 'Employee was already present on dates: '.$msg);
        }

        $leave->leave_type = $request->leave_type;
        $leave->datefrom = $dateFromTime;
        $leave->dateto = $dateToTime;
        $leave->subject = $request->subject;
        $leave->description = $request->description;
        $leave->status = 'Pending';

        $leave = $leave->save();

        return redirect()->route('leave.index')->with('success', 'Leave is created succesfully');
    }

    public function updateEmployeeLeaveType($employee_id, $leave_type_id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Leave $leave
     *
     * @return \Illuminate\Http\Response
     */
    public function updateStatus($id, $status)
    {
        $leave = Leave::find($id);
        if ($leave->status == 'Approved') { // if already approved do nothing
            return redirect()->back()->with('success', 'Leave already approved');
        }
        if ($status == 'Approved') {
            $dateFromTime = Carbon::parse($leave->datefrom);
            $dateToTime = Carbon::parse($leave->dateto);
            $consumed_leaves = $dateToTime->diffInDays($dateFromTime) + 1;
            if(Auth::user()->hasRole('Supervisor')){
                DB::table('employee_leave_type')->insert([
                    'leave_id' => $leave->id,
                    'status' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                DB::table('leaves')->where('id',$leave->id)->update([
                    'status' => 'pending on ogm'
                ]);
            }
            if(Auth::user()->hasRole('OGM')){
                 DB::table('employee_leave_type')->insert([
                    'leave_id' => $leave->id,
                    'status' => 3,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                DB::table('leaves')->where('id',$leave->id)->update([
                    'status' => 'approved',
                    'count' => $consumed_leaves
                ]);
            }
        }
        
        return redirect()->back()->with('success', 'Leave status is updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Leave $leave
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Leave $leave, $id)
    {
        $leave = Leave::where('employee_id', $id)->first();
        $leave->delete();

        return redirect()->back()->with('success', 'Leave is deleted successfully');
    }

    public function leaveDelete($id)
    {
        $leave = Leave::where('id', $id)->first();
        $leave->delete();

        return redirect()->back()->with('success', 'Leave is deleted successfully');
    }
    public function leaveOutput(Request $request,$id){
        $request->validate([
            'year' => 'required|min:4',
        ]);
        $year = $request->year;
        $leaveId = $id; 
        $employeeLeaves = Leave::with('employee')->with('leaveType')->where('employee_id',$id)->where('status','approved')->whereYear('created_at', $request->year)->get();
        $employee = Leave::with('employee')->where('employee_id',$id)->where('status','approved')->whereYear('created_at', $request->year)->first();
        if($employee == null){
            return redirect()->route('report.leave');
        }
        return view('admin.reports.leave.output',compact('employeeLeaves','employee','year','leaveId'));
    }
    public function leaveOuputAjax(Request $request,$id){
        $year = $request->year;
        $employeeLeaves = Leave::with('employee')->where('employee_id',$id)->where('status','approved')->whereYear('created_at', $request->year)->get();
        $employee = Leave::with('employee')->where('employee_id',$id)->where('status','approved')->whereYear('created_at', $request->year)->first();
        return response([$employeeLeaves],Response::HTTP_CREATED);
    }
    
}
