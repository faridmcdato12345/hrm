<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMembershipRequest;
use App\Http\Requests\UpdateMembershipRequest;
use App\Membership;
use App\SocialWelfare;
use Illuminate\Http\Request;
use Session;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $memberships = Membership::with('socialWelfares')->get();
        $socialWelfares = SocialWelfare::all();
        return view('admin.membership.index',compact('memberships','socialWelfares'));
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
    public function store(StoreMembershipRequest $request)
    {
        $data = Membership::create([
            'social_welfare_id' => $request->social_welfare_id,
            'name' => $request->name,
            'status' => $request->status,
            'amount' => $request->amount
        ]);
        Session::flash('success', 'Membership is added');
        return redirect()->route('membership.index')->with('Membership is successfully added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Membership  $membership
     * @return \Illuminate\Http\Response
     */
    public function show(Membership $membership)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Membership  $membership
     * @return \Illuminate\Http\Response
     */
    public function edit(Membership $membership)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Membership  $membership
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMembershipRequest $request, Membership $membership)
    {
        $membership->social_welfare_id = $request->social_welfare_id;
        $membership->name = $request->name;
        $membership->status = $request->status;
        $membership->amount = $request->amount;
        $membership->save();
        Session::flash('success', $request->name .' is updated');
        return redirect()->route('membership.index')->with($request->name. ' is successfully added');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Membership  $membership
     * @return \Illuminate\Http\Response
     */
    public function destroy(Membership $membership)
    {
        $membership->delete();
        Session::flash('success', $membership->name .' is deleted');
        return redirect()->route('membership.index')->with($membership->name. ' is successfully added');
    }
}
