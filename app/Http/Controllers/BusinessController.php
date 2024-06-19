<?php

namespace App\Http\Controllers;

use App\Enums\SettingKeys;
use App\Http\Requests\StoreBusinessRequest;
use App\Models\Address;
use App\Models\Business;
use App\Models\BusinessSetting;
use App\Models\BusinessType;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;


class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->hasRole('super-admin')) {
            $businesses = Business::with(['address.country', 'type'])->paginate(10);
            return view('modules.business.index', compact('businesses'));
        } else {
            $businesses = auth()->user()->businesses()->with(['address.country', 'type'])->paginate(10);
            return view('modules.business.index', compact('businesses'));
        }
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
        return view('modules.business.createOrEdit', compact('businessTypes', 'countries'));
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
        $data = $request->validated();
        $data['cover_image'] = upload('business/cover_image', 'png', $data['cover_image']);
        $data['logo'] = upload('business/logo', 'png', $data['logo']);
        $business = Business::create(collect($data)->except(['address','settings'])->toArray());
        $address = new Address($data['address']);
        $business->address()->save($address);
        
        foreach($data['settings'] as $key=>$value) {
            BusinessSetting::create([
                'business_id' => $business->id,
                'key' => $key,
                'value' => $value
            ]);
        }
        $business->users()->attach(auth()->user()->id, ['role' => 'owner']);
        return redirect()->route('business.index')->with('success', 'Business Created Successfully');
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
        $b = $business;
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
        $businessTypes = BusinessType::all();
        $countries = Country::all();
        $business->load(['address','settings']);
        $facilities = $business->facilities;
        return view('modules.business.createOrEdit', compact(['business', 'businessTypes', 'countries']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function update(StoreBusinessRequest $request, Business $business)
    {

        $data = $request->validated();
        
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = upload('business/cover_image', 'png', $data['cover_image']);
        }
        if ($request->hasFile('logo')) {
            $data['logo'] = upload('business/logo', 'png', $data['logo']);
        }
        $business->update(collect($data)->except(['address','settings'])->toArray());
        $business->address()->updateOrCreate($data['address']);
        
        foreach($data['settings'] as $key=>$value) {
            
            $business->settings()->updateOrCreate([
                'key' => $key
            ], [
                'value' => $value
            ]);
        }
        return back()->with('success', 'Business Updated Successfully');
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
    public function setting(Business $business)
    {
        $businessTypes = BusinessType::all();
        $countries = Country::all();
        $business->load(['address','settings']);

        $showSettings = true;
        
        return view('modules.business.createOrEdit', compact(['business', 'businessTypes', 'countries', 'showSettings']));
    }
    public function verify(Business $business)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);
        $business->update(['is_verified' => !$business->is_verified]);
        return back()->with('success', 'Business Verified Successfully');
    }
    public function featured(Request $request, Business $business) {
        $business->update(['is_featured' => !$business->is_featured]);
        return back()->with('success', 'Business Featured Successfully');
    }
    public function uploadImage(Request $request, Business $business) {
        $request->validate([
            'upload' => 'required|image'
        ]);
        $path = upload('content/', 'png', $request->file('upload'));
        
        return response()->json(['url' => getImage($path, 'content/')]);

    }

}
