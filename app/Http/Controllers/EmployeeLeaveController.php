<?php

namespace App\Http\Controllers;

use DB;
use App\Leave;
use App\Employee;
use App\Exports\EmployeeEarnedLeave;
use App\Exports\EmployeeLeave;
use App\LeaveType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class EmployeeLeaveController extends Controller
{
    public function ajaxCheckEmployeeLeave(Request $request){
        $leave_type = DB::table('leave_types')->where('id',$request->leave_id)->first();
        $leaves = DB::table('leaves')->select('count')
        ->where('leave_type',$request->leave_id)
        ->where('employee_id',Auth::user()->id)
        ->whereYear('created_at','2020')
        ->where('status','approved')
        ->sum('count');
        return response(['leaves'=>$leaves,'leave_type'=>$leave_type->count],Response::HTTP_OK);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $employees = Employee::findOrFail($id);
        $employee = $employees->toArray();
        $leaveTypes = LeaveType::all();
        //dd($employee);
        return view('admin.leaves.show',compact('employee','leaveTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|integer',
            'leave_type' => 'required',
            'datefrom' => 'date|required',
            'dateto' => 'date|required',
            'document' => 'nullable'
        ]);
        $arr = [];
        $arr = [
            'employee_id' => $request->employee_id,
            'document' => $request->document,
            'datefrom' => $request->datefrom,
            'dateto' => $request->dateto,
            'leave_type' => $request->leave_type,
        ];
        if ($request->document != '') {
            $document = time().'_'.$request->document->getClientOriginalName();
            $path = 'storage/employees/leave';
            if(!Storage::exists($path)){
                Storage::makeDirectory($path, 0777, true, true);
        // retry storing the file in newly created path.
            }   
            $request->document->move('storage/employees/leave/', $document);
            $arr['document'] = 'storage/employees/leave/'.$document;
        }
        $leave = Leave::create($arr);

        return redirect()->route('employeeleaves')->with('success', 'Employee leave is created succesfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leaves = Leave::with(['employee','leaveType'])
        ->select(
            'id',
            'employee_id',
            'leave_type',
            'datefrom',
            'dateto',
            'document'
        )
        ->where('employee_id',$id)
        ->get();
        $employee = Employee::select('id','firstname','lastname')
        ->where('id',$id)
        ->first();
        return view('admin.employees.leave.show',compact('leaves','employee'));
        // $dfrom = Carbon::parse($leave->datefrom);
        // $dto = Carbon::parse($leave->dateto);
        // $diff = $dfrom->diffInDays($dto);
        // dd($diff);
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
    public function exportLeaveForm()
    {
        $filePath = storage_path('import/Application_Leave.xlsx');
        $fileName = 'Application Leave.xlsx';

        return response()->download($filePath, $fileName);
    }
    public function exportLeaveFile($id){
        $filenameExport = 'Application Leave.xlsx';
        return Excel::download(new EmployeeLeave($id), $filenameExport); 
    }
    public function exportEarnedLeave(Request $request){
        $year = $request->montYear;
        $id = $request->employee_id;
        $filenameExport = 'Earned Leave.xlsx';
        // $samp = Excel::download(new EmployeeEarnedLeave($id,$year), $filenameExport);
        return Excel::download(new EmployeeEarnedLeave($id, $year), $filenameExport)
                ->deleteFileAfterSend(true)
                ->setStatusCode(200);
    }
    
    
}
