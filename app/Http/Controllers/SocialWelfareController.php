<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSocialWelfareRequest;
use App\Http\Requests\UpdateSocialWelfareRequest;
use App\SocialWelfare;
use Illuminate\Http\Request;
use Session;
class SocialWelfareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $socialWelfares = SocialWelfare::all();
        return view('admin.social_welfare.index',compact('socialWelfares'));
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
    public function store(StoreSocialWelfareRequest $request)
    {
        $data = SocialWelfare::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);
        Session::flash('success', $data->name .' is added');
        return redirect()->route('social_welfares.index')->with($data->name. ' is successfully added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SocialWelfare  $socialWelfare
     * @return \Illuminate\Http\Response
     */
    public function show(SocialWelfare $socialWelfare)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SocialWelfare  $socialWelfare
     * @return \Illuminate\Http\Response
     */
    public function edit(SocialWelfare $socialWelfare)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SocialWelfare  $socialWelfare
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSocialWelfareRequest $request, SocialWelfare $socialWelfare)
    {
        $socialWelfare->name = $request->name;
        $socialWelfare->status = $request->status;
        $socialWelfare->save();
        Session::flash('success', $socialWelfare->name .' is updated');
        return redirect()->route('social_welfares.index')->with($socialWelfare->name . ' is updated!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SocialWelfare  $socialWelfare
     * @return \Illuminate\Http\Response
     */
    public function destroy(SocialWelfare $socialWelfare)
    {
        $socialWelfare->delete();
        Session::flash('success', $socialWelfare->name .' is deleted');
        return redirect()->route('social_welfares.index')->with($socialWelfare->name . ' deleted');
    }
}
