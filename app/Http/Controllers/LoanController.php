<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Loan;
use App\SocialWelfare;
use Illuminate\Http\Request;
use Session;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loans = Loan::all();
        $socialWelfares = SocialWelfare::all();
        return view('admin.loan.index',compact('loans','socialWelfares'));
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
    public function store(StoreLoanRequest $request)
    {
        $data = Loan::create([
            'social_welfare_id' => $request->social_welfare_id,
            'name' => $request->name,
            'status' => $request->status,
        ]);
        Session::flash('success', $data->socialWelfares->name . '('.$data->name.')' . ' is added');
        return redirect()->route('loan.index')->with('success', $data->name.' is successfully added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(Loan $loan)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function edit(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $loan->social_welfare_id = $request->social_welfare_id;
        $loan->name = $request->name;
        $loan->status = $request->status;
        $loan->save();
        Session::flash('success', 'Loan is updated successfully');
        return redirect()->route('loan.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $loan = Loan::find($id);
        $loan->delete();
        Session::flash('success', 'Loan type deleted successfully.');
        return redirect()->route('loan.index');
    }
}
