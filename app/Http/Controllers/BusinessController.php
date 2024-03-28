<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessRequest;
use App\Models\Address;
use App\Models\Business;
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
        if(auth()->user()->hasRole('super-admin')) {
            $businesses = Business::with(['address.country','type'])->paginate(10);
            return view('modules.business.index',compact('businesses'));
        } else {
            $businesses = auth()->user()->businesses()->with(['address.country','type'])->paginate(10);
            return view('modules.business.index',compact('businesses'));
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
            'country_id' => $data['country'],
        ]);
        unset($data['country']);
        $business = Business::create($data);
        $business->address()->save($address);
        $business->users()->attach(auth()->user()->id, ['role' => 'owner']);
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
        $businessTypes = BusinessType::all();
        $business->load('users');
        return view('modules.business.setting', compact(['business', 'businessTypes']));
    }
    public function members(Request $request , Business $business) {
        if($request->isMethod('get')) {
            $countries = Country::all();
            $business->load('users');
            return view('modules.business.members', compact(['business','countries']));
        } else if($request->isMethod('post'))  {
            $request->validate([
                'member_type' => 'required|in:new_user,existing_user'
            ]);
            if($request->member_type == 'new_user') {
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'country' => ['required', 'exists:countries,id'],
                    'phone' => ['required', 'string', 'min:8','max:20'],
                    'password' => ['required', 'confirmed', Password::defaults()],
                ]);
                $role = Role::findByName($request->role);
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make('password')
                ]);
                $user->assignRole('user');
                $address = new Address([
                    'country_id' => $request->country
                ]);
                $user->address()->save($address);
                $business->users()->attach($user->id, ['role' => 'admin', 'position' => $request->position]);
                return redirect()->back()->with('success','Member Added Successfully');

            } else if ($request->member_type == 'existing_user') {

                $request->validate([
                    'user_id' => 'required|exists:users,id',
                    'role' => 'required|in:owner,admin,member'
                ]);
                $role = Role::findByName($request->role);
                $business->users()->attach($request->user_id, ['role' => $role->name]);
                return redirect()->back()->with('success','Member Added Successfully');
            
            }
        }
    }
    public function posts(Business $business) {
        
        return view('modules.business.posts', compact('business'));
    }
    public function addMember(Request $request, Business $business) {
        if($request->isMethod('get')) {
            $countries = Country::all();
            $business->load('users');
            return view('modules.business.add-member', compact('business','countries'));
        } else if($request->isMethod('post'))  {
            
            $request->validate([
                'member_type' => 'required|in:new_user,existing_user'
            ]);
            if($request->member_type == 'new_user') {
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'country' => ['required', 'exists:countries,id'],
                    'phone' => ['required', 'string', 'min:8','max:20'],
                    'password' => ['required', 'confirmed', Password::defaults()],
                ]);
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'force_update_password' => true
                ]);
                $address = new Address([
                    'country_id' => $request->country
                ]);
                $user->address()->save($address);
                $business->users()->attach($user->id, ['role' => 'admin', 'position' => $request->position, 'has_joined' => false]);
                
                
                return redirect()->route('business.members', $business)->with('success','Member Added Successfully');

            } else if ($request->member_type == 'existing_user') {

                $request->validate([
                    'user_id' => 'required|exists:users,id',
                    'role' => 'required|in:owner,admin,member'
                ]);
                $role = Role::findByName($request->role);
                $business->users()->attach($request->user_id, ['role' => $role->name]);
                return redirect()->back()->with('success','Member Added Successfully');
            
            }
        }
    }
    
    public function createPost(Request $request, Business $business) {
        if($request->isMethod('get')) {
            return view('modules.business.createPost', compact(['business']));
        } else if($request->isMethod('post')) {
            $request->validate([
                'title' => 'required|string|max:255',
                'short_desc' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'required|image'

            ]);
        }
    }
}
