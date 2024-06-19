<?php

namespace App\Http\Controllers;

use App\Events\MemberAddedToBusiness;
use App\Models\Address;
use App\Models\Business;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'role' => 'required|in:admin,member',
            'email' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if($user) {
            $password = '';
            if($user->force_update_password) {
                $password = \Str::random(8);
                $user->password = Hash::make($password);
                $user->save();
            }
            $business->users()->detach($user->id);
            $business->users()->attach($user->id, ['role' => $request->role, 'position' => $request->position, 'has_joined' => false]);
            event(new MemberAddedToBusiness($user, $business, $password, $request->role));
            return redirect()->back()->with('success', 'Member Added Successfully');
        } else if($request->has('member_type')){
            $request->validate([
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'address.country_id' => ['required', 'exists:countries,id'],
                'address.state_id' => ['sometimes', 'nullable'],
                'address.city' => ['sometimes', 'nullable'],
                'phone' => ['required', 'string', 'min:6', 'max:20']
            ]);
            $password = \Str::random(8);
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($password),
                'force_update_password' => true
            ]);
            $user->assignRole(User::User);

            $address = new Address($request->address);
            $user->address()->save($address);
            $business->users()->attach($user->id, ['role' => $request->role, 'position' => $request->position, 'has_joined' => false]);
            event(new MemberAddedToBusiness($user, $business, $password, $request->role));
            return redirect()->route('members.index', $business)->with('success', 'Member Added Successfully');
        
        } else {
            return back()->with(['email' => $request->email, 'role' => $request->role, 'showFullForm' => true]);
        }
    }
    public function show()
    {
    }
    public function update()
    {
    }
    public function destroy(Request $request, $businessId, $memberId)
    {
        // abort if auth user is not super admin or admin of business
        if(!$request->user()->hasRole('super-admin') && !$request->user()->businesses()->where('business_id', $businessId)->where('role', 'admin')->exists()) {
            return response()->json(['message'=> trans('You are not authorized to perform this action')], 403);

        }
        // if auth user removes himself, throw error
        if(auth()->user()->id == $memberId) {
            return response()->json(['message'=> trans('User cannot be removed')], 403);
        }
        $business = Business::find($businessId);
        $business->users()->detach($memberId);
        return response()->json(['message' => trans('Member Removed Successfully')]);
    }

    
}
