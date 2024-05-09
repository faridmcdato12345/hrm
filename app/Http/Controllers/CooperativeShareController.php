<?php

namespace App\Http\Controllers;

use App\CooperativeShare;
use App\Http\Requests\StoreCooperativeShareRequest;
use App\Http\Requests\UpdateCooperativeShareRequest;
use App\Membership;
use Illuminate\Http\Request;
use Session;

class CooperativeShareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shares = CooperativeShare::all();
        $memberships = Membership::with('socialWelfares')->get();
        return view('admin.cooperative_share.index',compact('shares','memberships'));
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
    public function store(StoreCooperativeShareRequest $request)
    {
        $data = CooperativeShare::create([
            'social_walfare_id' => $request->social_walfare_id,
            'bracket' => $request->bracket,
            'to_bracket' => $request->to_bracket,
            'amount' => $request->amount,
            'status' => $request->status,
        ]);
        if($data->bracket != null && $data->to_brack != null){
            Session::flash('success', $data->socialWelfares->name .' ('.$data->bracket.'-'.$data->to_bracket.') is added');
        }
        else{
            Session::flash('success', $data->socialWelfares->name .' is added');
        }
        return redirect()->route('cooperative_share.index')->with('Cooperative share is successfully added');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\CooperativeShare  $cooperativeShare
     * @return \Illuminate\Http\Response
     */
    public function show(CooperativeShare $cooperativeShare)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CooperativeShare  $cooperativeShare
     * @return \Illuminate\Http\Response
     */
    public function edit(CooperativeShare $cooperativeShare)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CooperativeShare  $cooperativeShare
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCooperativeShareRequest $request, CooperativeShare $cooperativeShare)
    {
        $cooperativeShare->bracket = $request->bracket;
        $cooperativeShare->to_bracket = $request->to_bracket;
        $cooperativeShare->amount = $request->amount;
        $cooperativeShare->status = $request->status;
        $cooperativeShare->save();
        if($cooperativeShare->bracket != null && $cooperativeShare->to_brack != null){
            Session::flash('success', $cooperativeShare->socialWelfares->name .' ('.$cooperativeShare->bracket.'-'.$cooperativeShare->to_bracket.') is updated');
        }
        else{
            Session::flash('success', $cooperativeShare->socialWelfares->name .' is updated');
        }
        return redirect()->route('cooperative_share.index')->with('Cooperative share is successfully updated.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CooperativeShare  $cooperativeShare
     * @return \Illuminate\Http\Response
     */
    public function destroy(CooperativeShare $cooperativeShare)
    {
        $cooperativeShare->delete();
        Session::flash('success', $cooperativeShare->socialWelfares->name .' is deleted');
        return redirect()->route('cooperative_share.index')->with('Cooperative deleted');
    }
}
