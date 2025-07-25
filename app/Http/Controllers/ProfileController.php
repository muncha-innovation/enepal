<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Country;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $countries = Country::all();
        $user = auth()->user();
        
        // Clear the active tab session unless it's specifically set to show a different tab
        // This ensures the general tab shows by default when visiting /profile
        if (!request()->has('tab')) {
            session()->forget('active_profile_tab');
        }
        
        return view('modules.profile.show', compact(['user', 'countries']));
    }

    public function update(StoreUserRequest $request)
    {
        $user = auth()->user();
        $data = collect($request->validated());
        $userData = $data->except(['address', 'original_password'])->toArray();
        
        // Profile picture is now handled separately via AJAX
        
        if (isset($userData['password']) && $userData['password'] != '') {
            $userData['force_update_password'] = false;
        }
        
        $user->update($userData);

        $address = $user->primaryAddress;

        if (!$address) {
            $user->addresses()->create($data->get('address'));
        } else {
            $address->update($data->get('address'));
        }
        
        // Set active tab to general for redirect
        session(['active_profile_tab' => 'general']);
        
        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }
    
    /**
     * Upload profile image via AJAX
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,jpg,png,gif|max:1024', // 1MB max
            ]);

            $user = auth()->user();
            $file = $request->file('profile_picture');
            
            // Delete old profile picture if exists
            if ($user->profile_picture && $user->profile_picture !== 'def.png') {
                try {
                    Storage::disk('public')->delete($user->profile_picture);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete old profile picture: ' . $e->getMessage());
                }
            }
            
            // Upload new image
            $imagePath = upload('profile/', 'png', $file);
            
            // Update user record
            $user->update(['profile_picture' => $imagePath]);
            
            return response()->json([
                'success' => true,
                'message' => 'Profile image uploaded successfully!',
                'image_url' => getImage($imagePath, 'profile/')
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first('profile_picture')
            ], 422);
        } catch (\Exception $e) {
            Log::error('Profile image upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image. Please try again.'
            ], 500);
        }
    }

    public function getWorkExperience()
    {
        $experiences = auth()->user()->workExperience;
        return response()->json($experiences);
    }

    public function updateWorkExperience(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'nullable|exists:user_experiences,id',
                'job_title' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'description' => 'nullable|string',
            ]);

            $user = auth()->user();
            if (isset($data['id'])) {
                $experience = $user->workExperience()->findOrFail($data['id']);
                $experience->update($data);
            } else {
                $user->workExperience()->create($data);
            }
            
            // Set active tab session
            session(['active_profile_tab' => 'work-experience']);

            return response()->json(['success' => true, 'message' => 'Work experience updated successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update work experience: ' . $e->getMessage()], 500);
        }
    }

    public function deleteWorkExperience($id)
    {
        $experience = auth()->user()->workExperience()->findOrFail($id);
        $experience->delete();

        return response()->json(['success' => true, 'message' => 'Work experience deleted successfully.']);
    }

    public function getWorkExperienceItem($id)
    {
        $experience = auth()->user()->workExperience()->findOrFail($id);
        return response()->json($experience);
    }

    public function getEducation()
    {
        $education = auth()->user()->education;
        return response()->json($education);
    }

    public function updateEducation(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'nullable|exists:user_education,id',
                'degree' => 'nullable|string|max:255',
                'institution' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'type' => 'required|in:under_slc,slc,plus_two,bachelors,masters,phd,training',
            ]);

            $user = auth()->user();
            if (isset($data['id']) && !empty($data['id'])) {
                $education = $user->education()->findOrFail($data['id']);
                $education->update($data);
            } else {
                $user->education()->create($data);
            }
            
            // Set active tab session
            session(['active_profile_tab' => 'education']);

            return response()->json(['success' => true, 'message' => 'Education updated successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update education: ' . $e->getMessage()], 500);
        }
    }

    public function deleteEducation($id)
    {
        $education = auth()->user()->education()->findOrFail($id);
        $education->delete();

        return response()->json(['success' => true, 'message' => 'Education deleted successfully.']);
    }

    public function getEducationItem($id)
    {
        $education = auth()->user()->education()->findOrFail($id);
        return response()->json($education);
    }

    public function passwordUpdate()
    {
        return view('modules.profile.password-update');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
            'force_update_password' => false,
        ]);

        // Set active tab to security for redirect
        session(['active_profile_tab' => 'security']);

        return back()->with('success', 'Password updated successfully');
    }

    /**
     * Display the preferences page
     */
    public function preferences()
    {
        $user = auth()->user();
        $countries = \App\Models\Country::all();
        
        // Instead of rendering just the partial, set a session variable to indicate active tab
        session(['active_profile_tab' => 'preferences']);
        
        // Redirect to the main profile page which will show the preferences tab
        return redirect()->route('profile');
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        
        $validationRules = [
            'user_type' => 'nullable|string|in:student,nrn,job_seeker',
            'app_language' => 'nullable|string',
            'known_languages' => 'nullable|array',
            'countries' => 'nullable|array',
            'has_passport' => 'nullable|boolean',
            'passport_expiry' => 'nullable|date',
            'departure_date' => 'nullable|date',
            'receive_notifications' => 'nullable|boolean',
            'show_personalized_content' => 'nullable|boolean',
            'distance_unit' => 'nullable|string|in:km,miles',
        ];
        
        // Add conditional validation only for study_field if user_type is student
        if ($request->input('user_type') === 'student') {
            $validationRules['study_field'] = 'nullable|string|max:255';
        }
        
        $validated = $request->validate($validationRules);
        
        // Prepare the data for saving
        $preferenceData = [
            'user_type' => $validated['user_type'] ?? null,
            'app_language' => $validated['app_language'] ?? 'en',
            'has_passport' => $request->has('has_passport'),
            'receive_notifications' => $request->has('receive_notifications'),
            'show_personalized_content' => $request->has('show_personalized_content'),
            'distance_unit' => $validated['distance_unit'] ?? 'km',
        ];
        
        // Only add these fields if user type is not NRN
        if (!isset($validated['user_type']) || $validated['user_type'] !== 'nrn') {
            $preferenceData['known_languages'] = $validated['known_languages'] ?? [];
            $preferenceData['countries'] = $validated['countries'] ?? [];
            
            if (isset($validated['departure_date'])) {
                $preferenceData['departure_date'] = $validated['departure_date'];
            }
        }
        
        // Add conditional fields
        if (isset($validated['user_type']) && $validated['user_type'] === 'student') {
            $preferenceData['study_field'] = $validated['study_field'] ?? null;
        }
        
        if ($request->has('has_passport') && isset($validated['passport_expiry'])) {
            $preferenceData['passport_expiry'] = $validated['passport_expiry'];
        }
        
        // Update or create preferences
        $user->preference()->updateOrCreate(
            ['user_id' => $user->id],
            $preferenceData
        );
        
        // Keep the active tab as preferences
        session(['active_profile_tab' => 'preferences']);
        
        // Redirect to the main profile page with success message
        return redirect()->route('profile')->with('success', 'Preferences updated successfully');
    }
}
