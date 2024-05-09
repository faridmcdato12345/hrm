<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Designation;
use App\UnclaimedThirteen;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnclaimedThirteenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unclaimeThirteen = UnclaimedThirteen::all();
        return view('admin.unclaimes.thirteen_month.index',compact('unclaimeThirteen'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::all();
        return view('admin.unclaimes.thirteen_month.create',compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $u = UnclaimedThirteen::create($request->all());
        return redirect()->route('thirteen.getEmployee',['id'=>'']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UnclaimedThirteen  $unclaimedThirteen
     * @return \Illuminate\Http\Response
     */
    public function show(UnclaimedThirteen $unclaimedThirteen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UnclaimedThirteen  $unclaimedThirteen
     * @return \Illuminate\Http\Response
     */
    public function edit(UnclaimedThirteen $unclaimedThirteen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UnclaimedThirteen  $unclaimedThirteen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $unclaimed = UnclaimedThirteen::where('employee_id',$id)->first();
        $unclaimed->flag = $request->input('flag');
        $unclaimed->save();
        return response(['data'=>$unclaimed],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UnclaimedThirteen  $unclaimedThirteen
     * @return \Illuminate\Http\Response
     */
    public function destroy(UnclaimedThirteen $unclaimedThirteen)
    {
        //
    }
    public function getEmployee($id=''){
        if ($id == 'all') {
            $data = Employee::with('branch', 'department')->get();
        } elseif ($id == '') {
            $data = Employee::with('branch', 'department')->where('status', '!=', '0')->get();
        } else {
            $data = Employee::with('branch', 'department')
                ->where('employment_status', $id)
                ->get();
        }
        $active_employees = Employee::where('status', '1')->count();
        $unclaimedSalaryEmployee = UnclaimedThirteen::where('flag',0)->get();
        return view('admin.unclaimes.thirteen_month.index', ['title' => 'All Employees'])
            ->with('employees', $unclaimedSalaryEmployee)
            ->with('active_employees', $active_employees)
            ->with('designations', Designation::all())
            ->with('selectedFilter', $id);
    }
    public function stores(Request $request)
    {
        $input['employee_id'] = $request->employee_id;
        $input['from_year'] = $request->from_year;
        $input['to_year'] = $request->to_year;
        UnclaimedThirteen::create($request->all());
        return redirect()->route('thirteen.getEmployee',['id'=>'']);
    }
}
