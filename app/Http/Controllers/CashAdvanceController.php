<?php

namespace App\Http\Controllers;

use App\Employee;
use Carbon\Carbon;
use App\CashAdvance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashAdvanceController extends Controller
{

    private $additionalDay = 15;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = CashAdvance::with('employees')->get();
        $uniques = $datas->unique(function ($item){
            return $item['created_at'].$item['employee_id'];
        });
        return view('admin.cash_advance.index',compact('uniques'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::all();
        return view('admin.cash_advance.create',compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        //dd(Carbon::parse($request->deduction_starting_date)->format('Y-m-d'));
        $amount = (int)$request->amount;
        $period = (int)$request->period;
        $periodHalf = $period / 2;
        $d = Carbon::parse($request->deduction_starting_date);
        do {
            //first amount /2 
            $periodHalf;
            //first date 
            $d;
            //save to database
            $ca = CashAdvance::create([
                'employee_id' => $id,
                'amount' => $request->amount,
                'month_deduction' => $d,      
                'period_amount_deduction' => $period / 2,
            ]);
            if($d->daysInMonth == 28){
                $temp = ($d->format('d') == "28") ? $temp = $d->addDays(15) : $temp = $d->addDays(13);
            }
            elseif($d->daysInMonth == 31){
                $temp = ($d->format('d') == "30") ? $temp = $d->addDays(16) : $temp = $d->addDays(15);
            }
            else{
                $temp = $d->addDays(15);
            }
            $d = $temp;
            $periodHalf += $period / 2;
        } while ($periodHalf <= $amount);
        return redirect()->route('cash.advance.index')->with('Cash advance to was successfully added');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\CashAdvance  $cashAdvance
     * @return \Illuminate\Http\Response
     */
    public function show(CashAdvance $cashAdvance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CashAdvance  $cashAdvance
     * @return \Illuminate\Http\Response
     */
    public function edit(CashAdvance $cashAdvance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CashAdvance  $cashAdvance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CashAdvance $cashAdvance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CashAdvance  $cashAdvance
     * @return \Illuminate\Http\Response
     */
    public function destroy(CashAdvance $cashAdvance)
    {
        //
    }

    public function getEmployee(){
        $employees = Employee::all();
        $output  = '';
        foreach($employees as $employee){
        }
        $data = array(
            'employees' => $output,
        );
        return json_encode($data);
    }
}
