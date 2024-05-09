<?php

namespace App\Http\Controllers;

use App\Employee;
use App\TotalEarnedLeaves;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TotalEarnedLeavesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::where('employment_status','permanent')->get();
        return view('admin.previous_leave_points.index',compact('employees'));
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
        //
    }
   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stores(Request $request,$id)
    {
        $data = [
            'employee_id' => $id,
            'previous_total_earned' => $request->previous_total_earned,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        TotalEarnedLeaves::create($data);
        return redirect()->route('total_earned_leaves.index');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\TotalEarnedLeaves  $totalEarnedLeaves
     * @return \Illuminate\Http\Response
     */
    public function show(TotalEarnedLeaves $totalEarnedLeaves)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TotalEarnedLeaves  $totalEarnedLeaves
     * @return \Illuminate\Http\Response
     */
    public function edit(TotalEarnedLeaves $totalEarnedLeaves)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TotalEarnedLeaves  $totalEarnedLeaves
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TotalEarnedLeaves $totalEarnedLeaves)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TotalEarnedLeaves  $totalEarnedLeaves
     * @return \Illuminate\Http\Response
     */
    public function destroy(TotalEarnedLeaves $totalEarnedLeaves)
    {
        //
    }
    public function getEarnedLastYear(Request $request){
        $employees = TotalEarnedLeaves::where('employee_id',$request->id)->latest()->first();
        return response(['data'=>$employees],Response::HTTP_OK);
    }

    public function storingAjax(Request $request,$id){
        $data = [
            'employee_id' => $id,
            'previous_total_earned' => $request->totalBalance,
            'sl' => $request->sl,
            'vl' => $request->vl,
            'vl_count' => $request->vl_count,
            'sl_count' => $request->sl_count,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        TotalEarnedLeaves::create($data);
        return response([],Response::HTTP_CREATED);
    }
    public function history($id){
        $leaves = TotalEarnedLeaves::with('employees')->where('employee_id',$id)->get();
        $employee = Employee::findOrFail($id);
        return view('admin.previous_leave_points.history',compact('leaves','employee'));
    }

    public function checkingVlSlCount(Request $request,$id){
        $data = TotalEarnedLeaves::where('employee_id',$id)
        ->where('sl_count',$request->sl_count)
        ->where('vl_count',$request->vl_count)
        ->latest('created_at')->first();
        // if($data === null){
        //     return false;
        // }
        // return true;
        return response(['data'=>$data],Response::HTTP_OK);
    }
}
