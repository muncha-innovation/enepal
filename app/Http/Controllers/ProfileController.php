<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Country;
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
        $data = $request->validated();
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = upload('profile/','png', $request->file('profile_picture'));
        }
        $user->update($data);
        return back()->with('success', 'Profile updated successfully');

    }
}
