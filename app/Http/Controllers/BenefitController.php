<?php

namespace App\Http\Controllers;

use App\Benefit;
use App\Employee;
use Illuminate\Http\Request;
use Session;

class BenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $benefits = Benefit::all();
        return view('admin.benefit.index',compact('benefits'),['title' => 'All Benefits']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.benefit.create',['title'=>'All Benefits']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = Benefit::create([
            'name' => $request->input('name'),
            'amount' => $request->input('amount')
        ]);
        return redirect()->route('benefit.index');
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
        $benefit = Benefit::find($id);
        $benefit->name = $request->name;
        $benefit->amount = $request->amount;
        $benefit->save();
    }
    public function updates(Request $request, $id)
    {
        $benefit = Benefit::find($id);
        $benefit->name = $request->name;
        $benefit->amount = $request->amount;
        $benefit->save();
        return redirect()->route('benefit.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $benefit = Benefit::find($id);
        $benefit->delete();
        return redirect()->route('benefit.index');
    }
}
