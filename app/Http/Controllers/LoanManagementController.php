<?php

namespace App\Http\Controllers;

use App\Loan;
use App\LoanManagement;
use Illuminate\Http\Request;
use Session;
class LoanManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = LoanManagement::all();
        $social_welfares = Loan::all(); 
        return view('admin.loan_management.index',compact('datas','social_welfares'));
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
        $data = LoanManagement::create([
            'social_welfare_id' => $request->social_welfare_id,
            'name' => $request->name,
            'status' => $request->status
        ]);
        Session::flash('success', $data->socialWelfares->name . '(' .$data->name.') is added successfully');
        return redirect()->route('loan_management.index')->with('Loan is added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LoanManagement  $loanManagement
     * @return \Illuminate\Http\Response
     */
    public function show(LoanManagement $loanManagement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LoanManagement  $loanManagement
     * @return \Illuminate\Http\Response
     */
    public function edit(LoanManagement $loanManagement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LoanManagement  $loanManagement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoanManagement $loanManagement)
    {
        $loanManagement->name = $request->name;
        $loanManagement->status = $request->status;
        $loanManagement->save();
        Session::flash('success','Successfully updated');
        return redirect()->route('loan_management.index')->with('Loan is updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LoanManagement  $loanManagement
     * @return \Illuminate\Http\Response
     */
    public function destroy(LoanManagement $loanManagement)
    {
        $loanManagement->delete();
        Session::flash('success', $loanManagement->socialWelfares->name .' ('.$loanManagement->name.') is deleted');
        return redirect()->route('loan_management.index');
    }
}
