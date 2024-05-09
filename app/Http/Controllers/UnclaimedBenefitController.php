<?php

namespace App\Http\Controllers;

use App\Benefit;
use App\Employee;
use App\Designation;
use App\UnclaimedBenefit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnclaimedBenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::all();
        return view('admin.unclaimes.benefits.create',compact('employees'))->with('benefits',Benefit::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $u = UnclaimedBenefit::create($request->all());
        return redirect()->route('unclaim_benefit.getEmployee',['id'=>'']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $unclaimed = UnclaimedBenefit::where('id',$id)->first();
        $unclaimed->flag = $request->input('flag');
        $unclaimed->save();
        return response(['data'=>$unclaimed],Response::HTTP_OK);
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
        $unclaimedSalaryEmployee = UnclaimedBenefit::where('flag',0)->get();
        return view('admin.unclaimes.benefits.index', ['title' => 'All Employees'])
            ->with('employees', $unclaimedSalaryEmployee)
            ->with('active_employees', $active_employees)
            ->with('designations', Designation::all())
            ->with('selectedFilter', $id);
    }
}
