<?php

namespace App\Http\Controllers;

use App\LoanType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Session;

class LoanTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loanCategories = LoanType::with('loans')->get();
        return view('admin.loan.category',compact('loanCategories'));
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
    public function store($id,Request $request)
    {
        $loanCategory = LoanType::create([
            'loan_id'=> $id,
            'name' => $request->name,
            'amount' => $request->amount,
            'status' => $request->status,
            'period' => $request->period
        ]);
        return redirect()->route('loan_type.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LoanType  $loanType
     * @return \Illuminate\Http\Response
     */
    public function show(LoanType $loanType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LoanType  $loanType
     * @return \Illuminate\Http\Response
     */
    public function edit(LoanType $loanType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LoanType  $loanType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loan = LoanType::findOrFail($id);
        $loan->name = $request->name;
        $loan->amount = $request->amount;
        $loan->status = $request->status;
        $loan->period = $request->period;
        $loan->save();
        Session::flash('success', 'Loan category is updated successfully');
        return redirect()->route('loan.category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LoanType  $loanType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loan = LoanType::find($id);
        $loan->delete();
        Session::flash('success', 'Loan category deleted successfully.');
        return redirect()->route('loan.category.index');
    }
}
