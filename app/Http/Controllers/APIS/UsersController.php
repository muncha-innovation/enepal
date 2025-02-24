<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    //
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required'

        ]);
        $user = auth()->user();
        $user->fcm_token = request('fcm_token');
        $user->save();
        return response()->json(['message' => 'FCM Token updated successfully']);
    }

    // public function toggleNewsPreference(Category $category) {

    //     $user = auth()->user();
    //     $user->toggleNewsPreference($category->id);
    //     return response()->json([
    //         'success' => true
    //     ]);
    // }

    public function user()
    {
        $countries = Country::with(['states'])->get();

        $user = User::with(['addresses'])->find(auth()->id());
        return response()->json([
            'countries' => CountryResource::collection($countries),
            'user' => UserResource::make($user)
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primaryAddress' => 'required|array',
            'primaryAddress.country_id' => 'required',
            'primaryAddress.state_id' => 'required',
            'primaryAddress.city' => 'required|string',
            'primaryAddress.latitude' => 'required|numeric',
            'primaryAddress.longitude' => 'required|numeric',
            'birthAddress' => 'nullable|array',
            'birthAddress.country_id' => 'nullable|required_with:birthAddress',
            'birthAddress.state_id' => 'nullable|required_with:birthAddress',
            'birthAddress.city' => 'nullable|required_with:birthAddress',
            'birthAddress.latitude' => 'nullable|numeric',
            'birthAddress.longitude' => 'nullable|numeric',
        ]);

        $user = auth()->user();
        return response()->json([
            'data' => $request->all(),
            'file'=> $request->file('file')
        ]);
        // Update basic user info
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        if($request->hasFile('image')) {
            $user->update([
                'profile_picture' => upload('profile/', 'png', $request->file('image'))
            ]);
        }

        // Delete existing addresses
        $user->addresses()->delete();

        // Create primary address
        if ($request->has('primaryAddress')) {
            $primaryAddress = $request->primaryAddress;
            $user->addresses()->create([
                'country_id' => $primaryAddress['country_id'],
                'state_id' => $primaryAddress['state_id'],
                'city' => $primaryAddress['city'],
                'address_line_1' => $primaryAddress['address_line_1'] ?? null,
                'address_line_2' => $primaryAddress['address_line_2'] ?? null,
                'address_type' => 'primary',
                'location' => \DB::raw("POINT({$primaryAddress['longitude']}, {$primaryAddress['latitude']})"),
            ]);
        }

        // Create birth address if provided
        if ($request->has('birthAddress')) {
            $birthAddress = $request->birthAddress;
            $user->addresses()->create([
                'country_id' => $birthAddress['country_id'],
                'state_id' => $birthAddress['state_id'],
                'city' => $birthAddress['city'],
                'address_line_1' => $birthAddress['address_line_1'] ?? null,
                'address_line_2' => $birthAddress['address_line_2'] ?? null,
                'address_type' => 'birth',
                'location' => \DB::raw("POINT({$birthAddress['longitude']}, {$birthAddress['latitude']})"),
            ]);
        }

        // Return updated user data
        return UserResource::make($user->load('addresses'));
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = auth()->user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
                'errors' => [
                    'current_password' => ['The provided password does not match our records.']
                ]
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->last_password_updated = now();
        $user->force_update_password = false;
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully',
            'data' => new UserResource($user)
        ]);
    }
}
