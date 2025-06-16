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
use Illuminate\Support\Facades\Cache;
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
        $countries = Cache::rememberForever('countries_with_states', function () {
            return Country::with('states')->get();
        });

        $user = User::with('addresses')->find(auth()->id());

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
            'addresses' => 'required|array',
            'addresses.*.address_type' => 'required|in:primary,birth',
            'addresses.*.country_id' => 'required|exists:countries,id',
            'addresses.*.state_id' => 'required|exists:states,id',
            'addresses.*.city' => 'nullable|string',
            'addresses.*.address_line_1' => 'nullable|string',
            'addresses.*.address_line_2' => 'nullable|string',
            'addresses.*.latitude' => 'required_with:addresses.*.longitude|nullable|numeric',
            'addresses.*.longitude' => 'required_with:addresses.*.latitude|nullable|numeric',
        ]);

        $user = auth()->user();
        // Update basic user info
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update addresses
        foreach ($request->addresses as $addressInput) {
            $addressType = $addressInput['address_type'];

            $addressData = [
                'country_id' => $addressInput['country_id'],
                'state_id' => $addressInput['state_id'],
                'city' => $addressInput['city'] ?? null,
                'address_line_1' => $addressInput['address_line_1'] ?? null,
                'address_line_2' => $addressInput['address_line_2'] ?? null,
            ];

            // Add location point if latitude and longitude are provided
            if (!empty($addressInput['latitude']) && !empty($addressInput['longitude'])) {
                $addressData['location'] = new \Grimzy\LaravelMysqlSpatial\Types\Point(
                    $addressInput['latitude'],
                    $addressInput['longitude']
                );
            }

            // Use updateOrCreate for each address type
            $user->addresses()->updateOrCreate(
                ['address_type' => $addressType],
                $addressData
            );
        }

        // Return updated user data with addresses
        return UserResource::make($user->fresh('addresses'));
    }

    public function updatePassword(Request $request)
    {
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

    public function updateImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();
        $path = upload('profile/', 'png', $request->file('image'));

        $user->update([
            'profile_picture' => $path
        ]);

        $user->load('addresses');

        return UserResource::make($user);
    }
}
