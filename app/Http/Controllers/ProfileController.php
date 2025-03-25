<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use App\Models\UserExperience;
use App\Models\UserEducation;

class ProfileController extends Controller
{
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
        $userData = $data->except(['address', 'original_password'])->toArray();
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

        return back()->with('success', 'Password updated successfully');
    }
}
