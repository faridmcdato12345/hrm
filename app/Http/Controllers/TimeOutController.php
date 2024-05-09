<?php

namespace App\Http\Controllers;

use App\TimeOut;
use Illuminate\Http\Request;

class TimeOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $timeOuts = TimeOut::all();
        return view('admin.time_out.index',compact('timeOuts'));
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
        TimeOut::create($request->all());
		return redirect()->route('timeout.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TimeOut  $timeOut
     * @return \Illuminate\Http\Response
     */
    public function show(TimeOut $timeOut)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TimeOut  $timeOut
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeOut $timeOut)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TimeOut  $timeOut
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $time = TimeOut::findOrFail($id);
		$time->time = $request->time;
		$time->status = $request->status;
		$time->save();
		return redirect()->route('timeout.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TimeOut  $timeOut
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeOut $timeOut)
    {
        //
    }
}
