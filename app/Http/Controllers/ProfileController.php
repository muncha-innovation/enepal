<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
    public function show()
    {
        $countries = Country::all();
        $user = auth()->user();
        return view('modules.profile.show', compact(['user', 'countries']));
    }

    public function update(UpdateProfileRequest $request) {
        $user = auth()->user();
        $data = collect($request->validated());
        $userData = $data->except('address')->toArray();
        if ($request->hasFile('profile_picture')) {
            $userData['profile_picture'] = upload('profile/','png', $request->file('profile_picture'));
        }
        
        $user->update($userData);

        $address = $user->address;
        if(!$address) { 
            $user->address()->create($data->get('address'));
        } else {
            $address->update($data->get('address'));
        }
        return back()->with('success', 'Profile updated successfully');

    }

}
