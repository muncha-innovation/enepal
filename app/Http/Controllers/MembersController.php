<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Country;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class MembersController extends Controller
{

    public function index(Business $business)
    {

        $countries = Country::all();
        $business->load('users');
        return view('modules.members.index', compact(['business', 'countries']));
    }
    public function create(Request $request, Business $business)
    {
        $countries = Country::all();
        $business->load('users');
        return view('modules.members.createOrEdit', compact('business', 'countries'));
    }
    public function store(Request $request, Business $business)
    {

        $request->validate([
            'member_type' => 'required|in:new_user,existing_user',
            'position' => 'sometimes|nullable',
            'role' => 'required|in:admin,member'
        ]);
        if ($request->member_type == 'new_user') {
            $request->validate([
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'address.country_id' => ['required', 'exists:countries,id'],
                'address.state_id' => ['sometimes', 'nullable'],
                'address.city' => ['sometimes', 'nullable'],
                'phone' => ['required', 'string', 'min:6', 'max:20'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'force_update_password' => true
            ]);
            $address = new Address($request->address);
            $user->address()->save($address);
            $business->users()->attach($user->id, ['role' => 'admin', 'position' => $request->position, 'has_joined' => false]);

            return redirect()->route('business.members', $business)->with('success', 'Member Added Successfully');
        } else if ($request->member_type == 'existing_user') {

            $request->validate([
                'user_id' => 'required|exists:users,id',
                'role' => 'required|in:owner,admin,member'
            ]);
            $business->users()->attach($request->user_id, ['role' => $request->role, 'position' => $request->position, 'has_joined' => false]);
            return redirect()->back()->with('success', 'Member Added Successfully');
        }
    }
    public function show()
    {
    }
    public function update()
    {
    }
    public function destroy()
    {
    }
}
