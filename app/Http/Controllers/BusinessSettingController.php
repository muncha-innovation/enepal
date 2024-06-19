<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class BusinessSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = BusinessSetting::where('business_id',null)->get();
        return view('admin-views.settings.index', compact('settings'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessSetting $businessSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessSetting $businessSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusinessSetting $businessSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessSetting $businessSetting)
    {
        //
    }
}
