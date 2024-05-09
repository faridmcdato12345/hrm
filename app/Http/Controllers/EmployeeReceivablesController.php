<?php

namespace App\Http\Controllers;

use App\EmployeeReceivables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;
use DB;

class EmployeeReceivablesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query1 = collect(
            DB::connection('pgsql_external')->table('personnel_department as d')
            ->get()
        );
        $query2 = collect(
            DB::table('lasureco_hrm.employee_receivables as er')
            ->join('lasureco_hrm.employees as e','er.employee_id','=','e.id')
            ->select('er.id','er.name','er.quantity','e.firstname','e.lastname','e.department_id')
            ->get()
        );
        $newQuery = $query2->map(function($item) use($query1){
            if(!is_null($item->department_id)){
                $depName = $query1->where('id',$item->department_id)->pluck('dept_name')->first();
            }else{
                $depName = 'N.A';
            }
            return[
                'employee_name'=>ucfirst($item->firstname).' '.ucfirst($item->lastname),
                'particulars'=>$item->name,
                'designated_dep'=> $depName,
            ];
        });
        $new = $newQuery->groupBy('employee_name');
        return view('admin.receivables.index',['rec'=>$new]);
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
        $this->validate($request, [
            'name'      => 'required',
            'quantity'  => 'required',
        ]);
        $arr = [];
        $arr = [
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'quantity' => $request->quantity,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        EmployeeReceivables::insert($arr);
        Session::flash('success', 'Item is added successfully');
        return redirect()->route('employee.showDetail',['id'=>$request->employee_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeReceivables  $employeeReceivables
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeReceivables $employeeReceivables)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeReceivables  $employeeReceivables
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeReceivables $employeeReceivables)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeReceivables  $employeeReceivables
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeReceivables $employeeReceivables)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeReceivables  $employeeReceivables
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeReceivables $employeeReceivables)
    {
        //
    }

    public function clear($id){
        $receivables = EmployeeReceivables::findOrFail($id);
        $receivables->clear = 1;
        $receivables->save();
        return redirect()->route('employee.showDetail',['id'=>$receivables->employee_id]);
    }
}
