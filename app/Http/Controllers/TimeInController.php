<?php

namespace App\Http\Controllers;

use App\TimeIn;
use Illuminate\Http\Request;
use Session;

class TimeInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$timeIns = TimeIn::all();
        return view('admin.time_in.index',compact('timeIns'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = TimeIn::create($request->all());
		return redirect()->route('timein.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TimeIn  $timeIn
     * @return \Illuminate\Http\Response
     */
    public function show(TimeIn $timeIn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TimeIn  $timeIn
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeIn $timeIn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TimeIn  $timeIn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		//$timeIn->update($request->all());
		$time = TimeIn::findOrFail($id);
		$time->time = $request->time;
		$time->margin = $request->margin;
		$time->status = $request->status;
		$time->save();
		//dd($request->all());
        Session::flash('success', 'Time-in is updated successfully');
		return redirect()->route('timein.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TimeIn  $timeIn
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeIn $timeIn)
    {
        //
    }
}
