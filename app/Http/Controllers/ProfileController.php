<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    //
    public function show()
    {
        $countries = Country::all();
        $user = auth()->user();
        return view('modules.profile.show', compact(['user', 'countries']));
    }

    public function update(StoreUserRequest $request)
    {
        $user = auth()->user();
        $data = collect($request->validated());
        $userData = $data->except(['address','original_password'])->toArray();
        if ($request->hasFile('profile_picture')) {
            $userData['profile_picture'] = upload('profile/', 'png', $request->file('profile_picture'));
        }
        if (isset($userData['password']) && $userData['password'] != '') {;
            $userData['force_update_password'] = false;
        }
        $user->update($userData);
        
        $address = $user->primaryAddress;
        
        if (!$address) {
            $user->addresses()->create($data->get('address'));
        } else {
            $address->update($data->get('address'));
        }
        return back()->with('success', 'Profile updated successfully');
    }
}
