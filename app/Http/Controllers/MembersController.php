<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Business;
use App\Models\Country;
use App\Models\User;
use App\Models\UserSegment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MembersController extends Controller
{
    public function index(Business $business)
    {
        // Get users belonging to this business
        $users = $business->users()
            ->with(['segments' => function($query) use ($business) {
                $query->where('business_id', $business->id);
            }])
            ->get();

        // Get segments belonging to this business
        $segments = $business->segments()
            ->with('users')  // No longer filtering by business_id here
            ->active()
            ->get();

        $countries = Country::all();

        return view('modules.members.index', compact('business', 'users', 'segments', 'countries'));
    }

    public function create(Business $business)
    {
        $segments = $business->segments()->active()->get();
        $countries = Country::all();

        return view('modules.members.createOrEdit', compact('business', 'segments', 'countries'));
    }

    public function store(Request $request, Business $business)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => 'required|in:admin,member',
            'member_type' => 'nullable|in:existing_user,new_user',
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'address.country_id' => ['sometimes', 'required', 'exists:countries,id'],
            'address.state_id' => ['sometimes', 'nullable'],
            'address.city' => ['sometimes', 'required', 'string'],
            'phone' => ['sometimes', 'required', 'string', 'min:6', 'max:20'],
            'segments' => 'nullable|array',
            'segments.*' => 'exists:user_segments,id'
        ]);

        // Check if user exists by email
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            // If user doesn't exist and we're not creating a new one, redirect back with form
            if (!isset($validated['member_type']) || $validated['member_type'] !== 'new_user') {
                return back()->with([
                    'email' => $validated['email'],
                    'role' => $validated['role'],
                    'showFullForm' => true,
                    'error' => 'User not found. Please provide details for a new user.'
                ]);
            }

            // Create new user
            $password = Str::random(8);
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'force_update_password' => true
            ]);

            // Create address if provided
            if (isset($validated['address'])) {
                $address = new Address($validated['address']);
                $user->addresses()->save($address);
            }
        }

        // Attach user to business with role
        if (!$business->users()->where('user_id', $user->id)->exists()) {
            $business->users()->attach($user->id, ['role' => $validated['role']]);
        } else {
            $business->users()->updateExistingPivot($user->id, ['role' => $validated['role']]);
        }

        // Add user to segments if provided
        if (!empty($validated['segments'])) {
            // Make sure segments belong to this business
            $segmentIds = UserSegment::where('business_id', $business->id)
                ->whereIn('id', $validated['segments'])
                ->pluck('id')
                ->toArray();
            
            $user->segments()->syncWithoutDetaching($segmentIds);
        }

        // Always add user to default segment based on role
        $defaultSegment = $business->segments()
            ->where('type', $validated['role'])
            ->where('is_default', true)
            ->first();

        if ($defaultSegment) {
            $user->segments()->syncWithoutDetaching([$defaultSegment->id]);
        }

        return redirect()->route('members.index', $business)->with('success', 'Member added successfully');
    }

    public function edit(Business $business, User $user)
    {
        $segments = $business->segments()->active()->get();
        
        // Get only segments belonging to this business
        $userSegments = $user->segments()
            ->where('business_id', $business->id)
            ->get()
            ->pluck('id')
            ->toArray();
            
        $countries = Country::all();
        return view('modules.members.createOrEdit', compact('business', 'user', 'segments', 'userSegments', 'countries'));
    }

    public function update(Request $request, Business $business, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,member',
            'segments' => 'nullable|array',
            'segments.*' => 'exists:user_segments,id'
        ]);

        // Update role
        $business->users()->updateExistingPivot($user->id, ['role' => $validated['role']]);

        // Update segments
        $segmentIds = $validated['segments'] ?? [];

        // Always include the default segment for the user's role
        $defaultSegment = $business->segments()
            ->where('type', $validated['role'])
            ->where('is_default', true)
            ->first();

        if ($defaultSegment) {
            $segmentIds[] = $defaultSegment->id;
        }

        // We'll handle segment syncing only for this business's segments
        $businessSegmentIds = $business->segments()->pluck('id')->toArray();
        
        // Get current user segments for this business
        $currentUserSegments = $user->segments()
            ->where('business_id', $business->id)
            ->pluck('id')
            ->toArray();
            
        // Segments to add (new segments for this business)
        $segmentsToAdd = array_intersect(
            array_diff($segmentIds, $currentUserSegments),
            $businessSegmentIds
        );
        
        if (count($segmentsToAdd) > 0) {
            $user->segments()->syncWithoutDetaching($segmentsToAdd);
        }
        
        // Segments to remove (segments to be removed only for this business)
        $segmentsToRemove = array_intersect(
            array_diff($currentUserSegments, $segmentIds),
            $businessSegmentIds
        );
        
        if (count($segmentsToRemove) > 0) {
            $user->segments()->detach($segmentsToRemove);
        }

        return redirect()->route('members.index', $business)->with('success', 'Member updated successfully');
    }

    public function destroy(Business $business, User $user)
    {
        // Remove user from all segments of this business
        $businessSegmentIds = $business->segments()->pluck('id')->toArray();
        $user->segments()->detach($businessSegmentIds);

        // Remove user from business
        $business->users()->detach($user->id);

        return redirect()->route('members.index', $business)->with('success', 'Member removed successfully');
    }
}
