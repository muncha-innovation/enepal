<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessRequest;
use App\Models\Address;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\Country;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $businesses = Business::paginate(10);
        return view('modules.business.index',compact('businesses'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $businessTypes = BusinessType::all();
        $countries = Country::all();
        return view('modules.business.create',compact('businessTypes','countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBusinessRequest $request)
    {
        //
        // dd($request->all());
        $data = $request->validated();
        $data['cover_image'] = upload('business/cover_image','png',$data['cover_image']);
        $data['logo'] = upload('business/logo','png',$data['logo']);
        $address = new Address([
            'country' => $data['country'],
        ]);
        unset($data['country']);
        $business = Business::create($data);
        $business->address()->save($address);
        return redirect()->route('business.index')->with('success','Business Created Successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function show(Business $business)
    {
        //
        
    return view('modules.business.show', compact('business'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function edit(Business $business)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Business $business)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function destroy(Business $business)
    {
        //
    }
    public function setting(Business $business) {
        return view('modules.business.setting', compact('business'));
    }
    public function members(Business $business) {
        return view('modules.business.members', compact('business'));
    }
    public function posts(Business $business) {
        
        return view('modules.business.posts', compact('business'));
    }
    public function addMember(Business $business) {
        return view('modules.business.add-member', compact('business'));
    }
    
}
